<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

function confirmAdminPrivilegies() {
	if ( !Auth::check() ) {
		echo( json_encode( array('rows_affected'=>0, 'error_message'=>'Authorization error') ) );
		die();
	}
	if( !( Auth::user()->admin_privilegies > 0) ) {
		echo( json_encode( array('rows_affected'=>0, 'error_message'=>'Authorization error') ) );
		die();
	}
}

function makeWhereClauseOutOfGetRequest( $addWhere = TRUE ) { 
	$whereString = '';
	$where = array();
	if( isset($_GET['name']) ) {
		array_push( $where, "name LIKE '" . $_GET['name'] . "%'" );
	}
	if( isset($_GET['email']) ) {
		array_push( $where, "email LIKE '" . $_GET['email'] . "%'" );
	}
	if( isset($_GET['title']) ) {
		array_push( $where, "title LIKE '" . $_GET['title'] . "%'" );
	}
	if( isset($_GET['minbalance']) ) {
		array_push( $where, "NOT balance < " . $_GET['minbalance'] . "" ); 	// 
	}
	if( isset($_GET['mindeposit']) ) {
		array_push( $where, "NOT deposit < " . $_GET['mindeposit'] . "" ); 	// 
	}
	if( sizeof($where) > 0 ) {
		$whereString = ( ($addWhere) ? " WHERE " : "" ) . implode( ' AND ', $where );
	}
	return $whereString;
}

function makeOrderByClauseOutOfGetRequest() { 
	$orderBy = '';
	$sortBy = null;
	$sortOrder = null;
	if( isset($_GET['sortBy']) ) {
		$orderBy = " ORDER BY " . $_GET['sortBy'];				
	}
	if( isset($_GET['sortOrder']) ) {
		$orderBy .= " " . $_GET['sortOrder'];				
	}
	return $orderBy;
}


function appendSQLQueryWithLimitAndOffsetOutOfGetString( $query ) {
	$limit = '';
	$offset = 0;
	$offsetPlusLimit = 50;
	if( isset($_GET['offset']) && isset($_GET['offsetPlusLimit']) ) {
		$offset = (int)$_GET['offset'];
		$offsetPlusLimit = (int)$_GET['offsetPlusLimit'];
		$limit = 'LIMIT ' . ($offsetPlusLimit - $offset + 1) . ' OFFSET ' . ($offset - 1);
	}

	$query .= ' ' . $limit;
	return array( $query, $offset, $offsetPlusLimit );
}

function makeRunAndReturnSQLQueriesWithCount( $query, $totalQuery ) {
	list( $query, $offset, $offsetPlusLimit ) = appendSQLQueryWithLimitAndOffsetOutOfGetString( $query );
	$data = DB::select( DB::raw( $query ) );
	$total = DB::select( DB::raw( $totalQuery ) );
	echo json_encode( 
		array( 'data'=>$data, 
			'pagination'=> array( 'total'=>$total[0]->total, 'offset'=>$offset , 'offsetPlusLimit'=>$offsetPlusLimit,
				'sortBy' => (isset($_GET['sortBy'])) ? $_GET['sortBy'] : '', 
				'orderBy'=> (isset($_GET['orderBy'])) ? $_GET['orderBy']:'' ) ) );
} 


Route::get('/', 'PageController@index' );
Route::get('/participation', 'PageController@participation');
Route::get('/questions_and_answers', 'PageController@faq');

// Maps
Route::get('/map', function () {
    return view('map.index');
});

Route::get('/map/{param}', function ($param) {
    return view('map.show',compact('param'));
});


// A "NO email verification" code
// Auth::routes();
// A "WITH email verification" code
Auth::routes(['verify' => true]);

Route::get('/user', 'UserController@index')->name('user');

Route::get('/user_reset_password', 'UserController@resetPassword');

Route::get('/plugin_main/', 'PluginController@main');

Route::get('/crop/{id}', 'CropController@show' );
Route::get('/crop', 'CropController@index');

Route::get('/publication/{id}', 'PublicationController@show' );
Route::get('/publication', 'PublicationController@index');

Route::get('/farm/{id}', 'FarmController@show' );
Route::get('/farm', 'FarmController@index');

Route::get('/supply/{id}', 'SupplyController@show' );
Route::get('/supply', 'SupplyController@index');

Route::get('/delivery_point/{id}', 'DeliveryPointController@show' );
Route::get('/delivery_point', 'DeliveryPointController@index');

Route::get('/workflow/{what}/{param?}', [ 'uses'=>'WorkflowController@indexParam'] );
//Route::get('/workflow/{what}', [ 'uses'=>'WorkflowController@index'] );

Route::get('/a_workflow/{what}/{param?}', [ 'uses'=>'WorkflowController@aindexParam'] );
//Route::get('/a_workflow/{what}', [ 'uses'=>'WorkflowController@aindex'] );

Route::get('/noauth_pending_supplies_short/', function () {
	$query = "SELECT id, title FROM `supplies` WHERE is_delivered=0 ORDER BY deliver_to";
	return DB::select( DB::raw( $query ) );
});


Route::get('/noauth_supply_short/{id}', function ($id) {
	$query = "SELECT id, title FROM `supplies` WHERE id=".$id." LIMIT 1";
	return DB::select( DB::raw( $query ) );
});


Route::get('/noauth_crops/', function () {
	$where = makeWhereClauseOutOfGetRequest();
	$orderBy = makeOrderByClauseOutOfGetRequest();
	$query = "SELECT * FROM `crops`" . $where . $orderBy;
	$totalQuery = "SELECT count(*) as total FROM `crops`" . $where . $orderBy;
	makeRunAndReturnSQLQueriesWithCount( $query, $totalQuery );
});


Route::get('/noauth_crops_short/', function () {
	$data = DB::table('crops')->get( ['id','title'] );
	return $data;
});


Route::get('/locations/{params?}', function ($params=null) {
	if( $params == null ) {
		$query = "SELECT id, title, latitude, longitude, 'farm' as type FROM `farms` UNION ALL ".
			"SELECT id, title, latitude, longitude, 'delivery_point' as type FROM `delivery_points`";
	} else {
		$keyValue = explode("=", $params);
		if( sizeof($keyValue) != 2 ) {
			return redirect("/");
		}
		$table = null;
		if( $keyValue[0] == 'farm' ) {
			$table = 'farms';
		} elseif( $keyValue[0] == 'delivery_point' ) {
			$table = 'delivery_points'; 
		}
		if( $table == null ) {
			return redirect("/");
		}			
		$query = "SELECT id, title, latitude, longitude, '{$keyValue[0]}' as type FROM `{$table}` WHERE id={$keyValue[1]}";
		//return json_encode( array( 'query' => $query ) );
	}
	return DB::select( DB::raw( $query ) );
});


Route::get('/noauth_farms/', function () {
	return DB::table('farms')->get();
});

Route::get('/noauth_farms_short/', function () {
	return DB::table('farms')->get(['id','title']);
});


Route::get('/noauth_delivery_points/', function () {
	return DB::table('delivery_points')->get();
});


Route::get('/noauth_delivery_points_short/{id}', function ($id) {
	return DB::table('delivery_points')->where('id',$id)->limit(1)->get(['id','title','pickup_info','delivery_info']);
});

Route::get('/noauth_delivery_points_short/', function () {
	return DB::table('delivery_points')->get(['id','title']);
});


Route::get('/noauth_delivery_units_short/', function () {
	return DB::table('delivery_units')->get(['id','title']);
});

Route::get('/noauth_sorting_stations_short/', function () {
	return DB::table('sorting_stations')->get(['id','title']);
});


Route::get('/a_cultivation_assignments/cr/{crop_id}', function ($crop_id) {
	//if ( !Auth::check() ) { return ''; }
	//$query = "SELECT * FROM `cultivation_assignments` WHERE crop_id=" . $crop_id . " AND is_finished=0";
	$query = "SELECT ca.*, fa.id as fa_id, fa.title as fa_title FROM `cultivation_assignments` AS ca, `farms` as fa ".
		"WHERE ca.crop_id=" . $crop_id . " AND ca.farm_id=fa.id AND ca.is_finished=0";
	return DB::select( DB::raw( $query ) );
});


Route::get('/a_harvesting_assignments/su/{supply_id}', function ($supply_id) {
	return DB::table('harvesting_assignments')->where('supply_id', $supply_id)->get();
});


Route::get('/a_harvesting_assignments/ca/{ca_id}', function ($ca_id) {
	return DB::table('harvesting_assignments')->where('cultivation_assignment_id', $ca_id)->get();
});


Route::get('/a_cultivation_assignments/fa/{id}', function ($id) {
	return DB::select( DB::raw( "SELECT * FROM `farms` WHERE id=".$id." LIMIT 1") );
});


Route::get('/a_cultivation_assignments/ha_number/{ca_id}', function ($ca_id) {
	return DB::select( DB::raw( "SELECT COUNT(id) as ha_cnt FROM `harvesting_assignments` WHERE cultivation_assignment_id=" . $ca_id) );
});


Route::get('/a_operations/ca/{ca_id}', function ($ca_id) {
	$query = "SELECT op.*, ca.title as ca_title, ca.start_by_plan as ca_start_by_plan, ca.finish_by_plan as ca_finish_by_plan ".
		"FROM `operations` as op  INNER JOIN `cultivation_assignments` as ca ON op.cultivation_assignment_id=" . $ca_id . 
		" AND op.cultivation_assignment_id=ca.id";
	return DB::select( DB::raw( $query ) );
});


Route::get('/a_supplies/deb/{supply_id}/{supply_price}', function ($supply_id, $supply_price) {
	if( !is_numeric($supply_price) ) {
		return json_encode( array( 'error_message' => 'Invalid price or price is not set for the supply' ) );
	}
	$query = "SELECT (SELECT COUNT(us.id) FROM `users` as us INNER JOIN `debetings` as db ON us.id=db.user_id WHERE ".
		"db.supply_id=".$supply_id.") as nd, ".
		"(SELECT COUNT(us.id) FROM `users` as us ".
		"WHERE us.is_suspended_for_supply!=1 AND us.delivery_point_id > 0 AND NOT (us.balance < " .$supply_price. ") AND NOT EXISTS ".
		"(SELECT id FROM `debetings` WHERE debetings.user_id=us.id AND debetings.supply_id=".$supply_id.")) as nnd";
	return DB::select( DB::raw( $query ) );
});


Route::get('/e_publications/', function () {
	confirmAdminPrivilegies();
	$query = "SELECT * FROM `publications`";
	$totalQuery = "SELECT count(*) as total FROM `publications`";
	makeRunAndReturnSQLQueriesWithCount( $query, $totalQuery );
});


Route::get('/a_persons/', function () {
	confirmAdminPrivilegies();
	return DB::table('persons')->get();
});

Route::get('/a_slides/', function () {
	confirmAdminPrivilegies();
	return DB::table('slides')->get();
});

Route::get('/a_texts/{lang}/', function($lang=NULL) {
	confirmAdminPrivilegies();
	$table = 'texts_en';
	if( $lang != NULL ) { 
		if( $lang == 'RU' ) { $table = 'texts_ru'; }
	}
	return DB::table($table)->get();
});


Route::get('/a_links/', function () {
	confirmAdminPrivilegies();
	return DB::table('links')->get();
});

Route::get('/a_users/{param?}', function ($param=NULL) {
	confirmAdminPrivilegies();
	$select = "";
	$where = "";
	if( $param == NULL ) {
		$select = "id, name, balance, deposit, deposit_comment, email, delivery_point_id, contacts ";
		$where = "admin_privilegies=0 AND delivery_point_id > 0 AND farm_admin=-1 AND delivery_unit_admin=-1 AND sorting_station_admin=-1";
	} else if( $param == 'all' ) {  
		$select = "id, name, balance, deposit, deposit_comment, email, contacts, delivery_point_id, delivery_point_admin, delivery_unit_admin, farm_admin, admin_privilegies";
		$where = "";
	} else if( $param == 'delivery_point_admin' ) {  
		$select = "id, name, balance, email, delivery_point_id, contacts, delivery_point_admin";
		$where = "delivery_point_admin <> -1";
	} else if( $param == 'not_assigned_to_dp' ) {
		$select = "id, name, balance, deposit, deposit_comment, email, delivery_point_id, contacts ";
		$where = "admin_privilegies=0 AND (NOT delivery_point_id > 0) AND farm_admin=-1 AND delivery_unit_admin=-1 AND sorting_station_admin=-1";		
	} else if( $param == 'farm_admin' ) {  
		$select = "id, name, email, contacts, farm_admin";
		$where = "farm_admin <> -1";
	} else if( $param == 'delivery_unit_admin' ) {  
		$select = "id, name, email, contacts, delivery_unit_admin";
		$where ="delivery_unit_admin <> -1";
	} 
	$query = "SELECT " . $select . " FROM `users`";
	$totalQuery = "SELECT count(id) as total FROM `users`";
	$whereOutOfGetString = makeWhereClauseOutOfGetRequest();
	$orderBy = makeOrderByClauseOutOfGetRequest();

	if( strlen($whereOutOfGetString) > 0 ) {
		$query .= " " . $whereOutOfGetString;
		$totalQuery .= " " . $whereOutOfGetString;
	}
	if( strlen($where) > 0 ) {
		if( strlen($whereOutOfGetString) > 0 ) {
			$query .= " AND " . $where . $orderBy;
			$totalQuery .= " AND " . $where;
		} else {
			$query .= " WHERE " . $where . $orderBy;
			$totalQuery .= " WHERE " . $where;
		}
//echo($whereOutOfGetString);
//echo("<br>");
//echo($totalQuery);
//return;
	}
	list( $query, $offset, $offsetPlusLimit ) = appendSQLQueryWithLimitAndOffsetOutOfGetString( $query );
	$data = DB::select( DB::raw( $query ) );
	$total = DB::select( DB::raw( $totalQuery ) );
	return json_encode( array( 'data'=>$data, 
		'pagination'=> array('total'=>$total[0]->total, 'offset'=>$offset , 'offsetPlusLimit'=>$offsetPlusLimit) ) );
});


Route::get('/a_users', function () {
	confirmAdminPrivilegies();
	$query = "SELECT id, name, balance, email, delivery_point_id, contacts, icon ".
		//"delivery_point_admin, delivery_unit_admin, sorting_station_admin, admin_privilegies ".
		"FROM `users` WHERE admin_privilegies=0 AND farm_admin=-1 AND delivery_unit_admin=-1 AND sorting_station_admin=-1";
	return DB::select( DB::raw( $query ) );
});


Route::get('/a_users/refills/{user_id}', function ($user_id) {
	confirmAdminPrivilegies();
	$query = "SELECT re.id AS id, re.title AS title, re.amount AS amount, re.made_at AS made_at".
		", re.user_id AS user_id, us.name AS user_name, us.email AS user_email".
		" FROM `refills` AS re INNER JOIN `users` AS us ON re.user_id=us.id WHERE re.user_id=" . $user_id;
	$totalQuery = "SELECT COUNT(id) as total FROM `refills` WHERE user_id=" . $user_id;
	makeRunAndReturnSQLQueriesWithCount( $query, $totalQuery );
});


Route::get('/a_users/debetings/{user_id}', function ($user_id) {
	confirmAdminPrivilegies();
	$select = "db.id as id, db.title as title, db.amount as amount, DATE_FORMAT(db.made_at, '%Y-%m-%d %H:%i') AS made_at".
		", db.supply_id as supply_id, su.title as supply_title, db.is_delivered as is_delivered, db.user_id as user_id";
	$query = "SELECT " . $select . " FROM `debetings` as db, `supplies` as su WHERE db.user_id=" . $user_id . " AND db.supply_id=su.id";
	$totalQuery = "SELECT COUNT(db.id) as total FROM `debetings` as db, `supplies` as su WHERE db.user_id=" . $user_id . " AND db.supply_id=su.id";
	makeRunAndReturnSQLQueriesWithCount( $query, $totalQuery );
});


Route::get('/a_delivery_points/', function () {
	confirmAdminPrivilegies();
	$query = "SELECT dp.id, dp.title, dp.descr, dp.address, dp.latitude, dp.longitude, dp.delivery_info, dp.pickup_info, dp.icon".
		", COUNT(us.delivery_point_id) as users_count".
		" FROM `delivery_points` AS dp LEFT JOIN `users` AS us ON dp.id=us.delivery_point_id GROUP BY dp.id, us.delivery_point_id";
	//$query = "SELECT dp.id, dp.title, us.id as uid".
	//	" FROM `delivery_points` AS dp LEFT JOIN `users` AS us ON dp.id=us.delivery_point_id";
	return DB::select( DB::raw( $query ) );
	//$data = DB::table('delivery_points')->get();
	//return $data;
});


Route::get('/a_delivery_units/', function () {
	confirmAdminPrivilegies();
	$data = DB::table('delivery_units')->get();
	return $data;
});

Route::get('/a_farms/', function () {
	confirmAdminPrivilegies();
	return DB::table('farms')->get();
});


Route::get('/a_cultivation_assignments/', function () {
	confirmAdminPrivilegies();
	$from = "FROM `cultivation_assignments` as ca LEFT JOIN `harvesting_assignments` as ha ON ca.id = ha.cultivation_assignment_id".
		" INNER JOIN `farms` as fa ON fa.id=ca.farm_id";
	$query = "SELECT ca.id as id, ca.title as title, ca.descr as descr, ca.square,".
		" ca.amount_prognosed as amount_prognosed, ca.amount_by_plan as amount_by_plan, SUM(ha.amount_prognosed) as reserved,".
		" COUNT(ha.id) as ha_cnt, ca.start_by_plan as start_by_plan, ca.finish_by_plan as finish_by_plan,". 
		" ca.start_prognosed as start_prognosed, ca.finish_prognosed as finish_prognosed,". 
		" ca.crop_id as crop_id, ca.farm_id as farm_id, fa.title as farm_title, fa.square as farm_square,".
		" ca.is_accepted as is_accepted, ca.is_finished as is_finished ". $from . 
		" GROUP BY ha.cultivation_assignment_id, ca.id ORDER BY ca.finish_by_plan";
	$totalQuery = "SELECT COUNT(1) as total " . $from;
	makeRunAndReturnSQLQueriesWithCount( $query, $totalQuery );
});


Route::get('/a_cultivation_assignments/{id}', function ($id) {
	confirmAdminPrivilegies();
	$a = DB::select( DB::raw('SELECT * FROM `cultivation_assignments` WHERE id='.$id.' LIMIT 1') );
	if( sizeof($a) == 0 ) {
		return json_encode( array( 'id' => -1, 'error_message' => 'No delivery points' ) );
	}
	return json_encode($a[0]);
});


Route::get('/a_cultivation_assignments_not_finished', function () {
	confirmAdminPrivilegies();
	$query = 'SELECT finish_prognosed, title FROM `cultivation_assignments` WHERE is_finished=0 ORDER BY finish_prognosed DESC LIMIT 200';
	$cas = DB::select( DB::raw( $query ) );
	return $cas;
});

Route::get('/a_harvesting_assignments/', function () {
	confirmAdminPrivilegies();
/*
	$fromWhere = "FROM `harvesting_assignments` as ha, `cultivation_assignments` as ca, `supplies` as su ".
		"WHERE ha.is_finished=0 AND ha.cultivation_assignment_id=ca.id AND ha.supply_id=su.id ORDER BY ha.finish_by_plan";
	$query = "SELECT ha.*, ca.title as ca_title, ca.finish_by_plan as ca_finish_by_plan, ca.finish_prognosed ca_finish_prognosed, ".
		"ca.amount_prognosed as ca_amount_prognosed, su.id as su_id, su.title as su_title, ".
		"DATE_FORMAT(su.deliver_to, '%Y-%m-%d %H:%i') AS su_deliver_to " . $fromWhere;
	$totalQuery = "SELECT COUNT(1) as total " . $fromWhere;
	makeRunAndReturnSQLQueriesWithCount( $query, $totalQuery );
*/
	$query = "SELECT ha.id, ha.title, ha.descr, ha.crop_id, ha.cultivation_assignment_id, ha.supply_id, ha.farm_id".
		", ha.amount_by_plan, ha.amount_prognosed, ha.amount_actual".
		", DATE_FORMAT(ha.start_by_plan, '%Y-%m-%d %H:%i') AS start_by_plan, DATE_FORMAT(ha.finish_by_plan, '%Y-%m-%d %H:%i') AS finish_by_plan".
	 	", ca.title as ca_title, ca.is_accepted as ca_is_accepted, ca.is_finished as ca_is_finished".
		", ca.start_by_plan as ca_start_by_plan, ca.finish_by_plan as ca_finish_by_plan, ca.finish_prognosed ca_finish_prognosed".
		", ca.amount_by_plan as ca_amount_by_plan, ca.amount_prognosed as ca_amount_prognosed, ca.square as ca_square".
		", su.id as su_id, su.title as su_title".
		", DATE_FORMAT(su.deliver_to, '%Y-%m-%d %H:%i') AS su_deliver_to, SUM(ha2.amount_prognosed) as reserved". 
		" FROM `harvesting_assignments` as ha INNER JOIN `cultivation_assignments` AS ca ON ha.cultivation_assignment_id=ca.id".
		" LEFT JOIN `supplies` AS su ON ha.supply_id=su.id INNER JOIN `harvesting_assignments` AS ha2".
		" ON ha.cultivation_assignment_id = ha2.cultivation_assignment_id GROUP BY ha.id".
		" ORDER BY ha.finish_by_plan DESC";
	$totalQuery = "SELECT COUNT(1) as total FROM `harvesting_assignments`";
	makeRunAndReturnSQLQueriesWithCount( $query, $totalQuery );


});


Route::get('/a_supplies/', function () {
	confirmAdminPrivilegies();
	$query = "SELECT id, DATE_FORMAT(`deliver_to`, '%Y-%m-%d %H:%i') AS `deliver_to`, title, icon, descr, price_per_user, delivery_info ".
		"FROM `supplies` ORDER BY deliver_to DESC"; 
	$totalQuery = "SELECT COUNT(1) as total FROM `supplies`";
	makeRunAndReturnSQLQueriesWithCount( $query, $totalQuery );
});


Route::get('/a_supplies/deliveries/{supply_id}', function ($supply_id) {
	$query = "SELECT deb.delivery_point_id as delivery_point_id, COUNT(1) as cnt_deliveries, SUM(deb.is_delivered) as cnt_delivered".
		", deb.is_problem, deb.problem, dpt.title AS delivery_point_title, " . $supply_id . " as supply_id, sup.title as supply_title".
		", DATE_FORMAT(sup.deliver_to, '%Y-%m-%d %H:%i') AS supply_deliver_to".
		" FROM `debetings` AS deb INNER JOIN `delivery_points` as dpt ON deb.delivery_point_id=dpt.id".
		" INNER JOIN `supplies` AS sup ON deb.supply_id=sup.id".
		" WHERE supply_id=".$supply_id." GROUP BY delivery_point_id, is_problem, problem HAVING (cnt_deliveries > 0)";
	return DB::select( DB::raw( $query ) );
});


Route::get('/a_supplies/debetings/{supply_id}/{delivery_point_id}', function ($supply_id, $delivery_point_id) {
	$query = "SELECT debetings.id as id, users.name as user_name, DATE_FORMAT(debetings.made_at, '%Y-%m-%d %H:%i') as made_at".
		", debetings.amount, debetings.is_delivered, debetings.is_problem, debetings.problem FROM `debetings`".
		" INNER JOIN `users` ON debetings.user_id = users.id".
		" WHERE debetings.supply_id=" . $supply_id . " AND debetings.delivery_point_id=" . $delivery_point_id;
	return DB::select( DB::raw( $query ) );
});


Route::get('/d_supplies/{param}', function ($param) {
	$is_delivered = ($param == 'all') ? '1' : ($param == 'delivered') ? 'is_delivered=1' : 'is_delivered=0';
	$query = "SELECT id, DATE_FORMAT(`deliver_to`, '%Y-%m-%d %H:%i') AS `deliver_to`, delivery_info, is_delivered, title, icon, descr, icon ".
		"FROM `supplies` WHERE " . $is_delivered . " ORDER BY is_delivered ASC, deliver_to DESC"; 
	$totalQuery = "SELECT COUNT(1) AS total FROM `supplies` WHERE " . $is_delivered;
	makeRunAndReturnSQLQueriesWithCount( $query, $totalQuery );
});


Route::get('/d_supplies/deliveries/{supply_id}', function ($supply_id) {
	$query = "SELECT deb.delivery_point_id, deb.problem, COUNT(1) as cnt_deliveries, SUM(deb.is_delivered) as cnt_delivered,".
		" dpt.title as delivery_point_title, sup.title as supply_title,".
		" DATE_FORMAT(sup.deliver_to, '%Y-%m-%d %H:%i') AS deliver_to, " . $supply_id . " as supply_id".
		" FROM debetings as deb INNER JOIN delivery_points as dpt ON deb.delivery_point_id=dpt.id" . 
		" INNER JOIN supplies as sup ON deb.supply_id=sup.id ".
		" WHERE deb.supply_id=" . $supply_id . " GROUP BY deb.delivery_point_id, deb.problem HAVING (cnt_deliveries > 0)";
	return DB::select( DB::raw( $query ) );
});


Route::get('/a_sorting_stations/', function () {
	confirmAdminPrivilegies();
	return DB::table('sorting_stations')->get();
});


Route::get('/u_suspend/{param}', function ($param) {
	if ( !Auth::check() ) {
		return false;
	}
	$is_suspended = -1;
	if( $param == 'on' ) {
		$is_suspended = 1;
	} elseif( $param == 'off' ) {	
		$is_suspended = 0;
	}
	$response = 0;
	if( $is_suspended == 1 || $is_suspended == 0 ) {
		//echo("$is_suspended=".$is_suspended);
		$response = DB::table('users')->where( 'id', Auth::user()->id )->update( ['is_suspended_for_supply' => $is_suspended] );
		//echo("Response=".$response);
	}
	header('Content-Type: application/json');
	return json_encode( array('records_affected' => $response, 'is_suspended_for_supply' => $is_suspended) );
});

Route::get('/u_refills', function () {
	if ( !Auth::check() ) { return false; }
	$query = "SELECT made_at, amount, title FROM `refills` WHERE user_id=" . Auth::user()->id . " ORDER BY made_at";
	$totalQuery = "SELECT COUNT(1) as total FROM `refills` WHERE user_id=" . Auth::user()->id;
 	makeRunAndReturnSQLQueriesWithCount( $query, $totalQuery ); 
	//return DB::select( DB::raw( $query ) );
});

Route::get('/u_debetings', function () {
	if ( !Auth::check() ) { return false; }
	$query = "SELECT made_at, amount, supply_id, delivery_point_id, is_delivered FROM `debetings` WHERE user_id=".Auth::user()->id." ORDER BY made_at";
	$totalQuery = "SELECT COUNT(1) as total FROM `debetings` WHERE user_id=" . Auth::user()->id;
 	makeRunAndReturnSQLQueriesWithCount( $query, $totalQuery ); 
	//return DB::select( DB::raw( $query ) );
});

Route::get('/u_supplies/{param}', function ($param) {
	if ( !Auth::check() ) { return false; }
	$is_delivered = ($param == 'delivered') ? 1 : 0;
	$sort = ($param == 'delivered') ? 'DESC' : 'ASC';
	$query = "SELECT DATE_FORMAT(su.deliver_to, '%Y-%m-%d %H:%i') AS deliver_to, us.delivery_point_id as current_delivery_point_id,".
		" su.id as id, su.title as title, su.descr as descr, IF(ISNULL(dbt.id),'n','y') as is_debeted,".
		" IFNULL(dbt.amount,0) as amount_debeted, dbt.made_at as date_debeted, dbt.delivery_point_id as delivery_point_id,".
		" dbt.problem, dbt.is_problem, IF(ISNULL(dlvp.title),'',dlvp.title) AS delivery_point_title".
		" FROM `supplies` as su INNER JOIN `users` as us ON 1 LEFT JOIN `debetings` as dbt ON us.id=dbt.user_id AND dbt.supply_id=su.id".
		" LEFT JOIN `delivery_points` as dlvp ON dbt.delivery_point_id=dlvp.id".
 		" WHERE us.id=" . Auth::user()->id . " AND su.is_delivered=" . $is_delivered . " ORDER BY su.deliver_to " . $sort;
	$totalQuery = "SELECT COUNT(1) as total FROM supplies WHERE is_delivered=".$is_delivered;
 	makeRunAndReturnSQLQueriesWithCount( $query, $totalQuery); 
	//return DB::select( DB::raw( $query ) );
});


Route::get('/u_supply/is_debeted/{supply_id}', function ($supply_id) {
	if ( !Auth::check() ) { return ''; }
	$query = "SELECT id, made_at, amount FROM `debetings` WHERE supply_id=" . $supply_id . " AND user_id=" . Auth::user()->id . " LIMIT 1";
	return DB::select( DB::raw( $query ) );
});


Route::get('/u_delivery_point/{id}', function ($id) {
	$query = '';
	if( Auth::user()->delivery_point_admin > 0 ) {
		$query = "SELECT dp.id as id, dp.title as title, dp.descr as descr, dp.address as address, ".
			"dp.latitude as latitude, dp.longitude as longitude, '' as admin_contacts, '' as admin_name, '' as admin_email, ".
			"dp.delivery_info as delivery_info, dp.pickup_info as pickup_info, COUNT(us.id) as cnt_users ".
			"FROM `delivery_points` as dp INNER JOIN `users` as us ON dp.id=" . $id . " AND us.delivery_point_id=" . $id . 
			" GROUP BY dp.id"; // Auth::user()->delivery_point_id
	} else{
		$query = "SELECT dp.id as id, dp.title as title, dp.descr as descr, dp.pickup_info as pickup_info".	
			", dp.address as address, us.contacts as admin_contacts, us.name as admin_name, us.email as admin_email".
			", '' as delivery_info, '' as cnt_users".
			" FROM `delivery_points` as dp INNER JOIN `users` as us ON dp.id=us.delivery_point_admin".
			" WHERE dp.id=" . $id . " LIMIT 1";
	}
	return DB::select( DB::raw( $query ) );
});


Route::get('/u_delivery_point_users', function () {
	if( Auth::user()->delivery_point_admin > 0 ) {
		$query = "SELECT id, name, email, balance, icon, is_suspended_for_supply, contacts FROM `users` ".
			"WHERE delivery_point_id=" . Auth::user()->delivery_point_admin;
		$totalQuery = "SELECT COUNT(1) as total FROM `users` WHERE delivery_point_id=" . Auth::user()->delivery_point_admin;
 		makeRunAndReturnSQLQueriesWithCount( $query, $totalQuery); 
	}
	return '';
});


Route::get('/f_cultivation_assignments/{status}', function ($status) {
	if ( !Auth::check() ) { return ''; }
	$is_finished = ($status == 'all' ) ? '1' : (( $status == 'pending' ) ? 'ca.is_finished=0' : 'ca.is_finished=1');

	$query = "SELECT ca.id as id, ca.title as title, ca.descr as descr, ca.is_accepted as is_accepted, ca.is_finished as is_finished".
		", ca.amount_by_plan as amount_by_plan, ca.amount_prognosed as amount_prognosed, ca.amount_actual as amount_actual".
		", SUM(ha.amount_prognosed) as reserved, COUNT(ha.id) as ha_cnt".
		", ca.start_by_plan as start_by_plan, ca.finish_by_plan as finish_by_plan". 
		", ca.start_prognosed as start_prognosed, ca.finish_prognosed as finish_prognosed". 
		", ca.start_actual as start_actual, ca.finish_actual as finish_actual". 
		", ca.crop_id as crop_id, ca.work_time as work_time".
		" FROM `cultivation_assignments` as ca LEFT JOIN `harvesting_assignments` as ha ON ca.id = ha.cultivation_assignment_id".
		" WHERE " . $is_finished . " AND ca.farm_id=" . Auth::user()->farm_admin .  
		" GROUP BY ha.cultivation_assignment_id, ca.id ORDER BY is_finished, ca.finish_by_plan DESC";

	$totalQuery = " SELECT COUNT(ca.id) as total FROM `cultivation_assignments` as ca".
		" WHERE " . $is_finished . " AND ca.farm_id=" . Auth::user()->farm_admin; 

	list( $query, $offset, $offsetPlusLimit ) = appendSQLQueryWithLimitAndOffsetOutOfGetString( $query );
	$data = DB::select( DB::raw( $query ) );
	$total = DB::select( DB::raw( $totalQuery ) );
	$totalValue = ( sizeof($total) > 0 ) ? $total[0]->total : 0; 
	return json_encode( array( 'data'=>$data, 
		'pagination'=> array('total'=>$totalValue, 'offset'=>$offset, 'offsetPlusLimit'=>$offsetPlusLimit) ) );
});


Route::get('/f_harvesting_assignments/{status}', function ($status) {
	if ( !Auth::check() ) { return false; }

	$is_finished = ($status == 'all' ) ? '1' : (( $status == 'pending' ) ? 'ca.is_finished=0' : 'ca.is_finished=1');
	$where = "ha.farm_id=" . Auth::user()->farm_admin . " AND " . $is_finished;
	$query = "SELECT ha.*, ca.title as ca_title, ca.finish_by_plan as ca_finish_by_plan, ca.finish_prognosed ca_finish_prognosed".
		", ca.amount_prognosed as ca_amount_prognosed, su.id as su_id, su.title as su_title".
		", DATE_FORMAT(su.deliver_to, '%Y-%m-%d %H:%i') AS su_deliver_to".
		" FROM `harvesting_assignments` as ha INNER JOIN `cultivation_assignments` as ca ON ha.cultivation_assignment_id=ca.id".
		" INNER JOIN `supplies` as su ON ha.supply_id=su.id".
		" WHERE ha.is_finished=0 AND ha.cultivation_assignment_id=ca.id AND ha.supply_id=su.id".
		" AND " . $where . " ORDER BY is_finished, ha.finish_by_plan DESC";

	$totalQuery = " SELECT COUNT(ha.id) as total FROM `harvesting_assignments` as ha WHERE " . $where; 

	list( $query, $offset, $offsetPlusLimit ) = appendSQLQueryWithLimitAndOffsetOutOfGetString( $query );
	$data = DB::select( DB::raw( $query ) );
	$total = DB::select( DB::raw( $totalQuery ) );
	$totalValue = ( sizeof($total) > 0 ) ? $total[0]->total : 0; 
	return json_encode( array( 'data'=>$data, 
		'pagination'=> array('total'=>$totalValue, 'offset'=>$offset , 'offsetPlusLimit'=>$offsetPlusLimit) ) );
});


Route::get('/f_supervisor', function () {
	if ( !Auth::check() ) { return false; }
	return DB::Select( DB::raw("SELECT name, email, contacts FROM `users` WHERE admin_privilegies=255 LIMIT 1") );
});

Route::post('/u_update', 'UserDBIOController@update' );
Route::post('/u_update_delivery_point', 'UserDBIOController@updateDeliveryPoint' );

Route::post('/e_publications_new', 'EditorDBIOController@newPublication' );
Route::post('/e_publications_update', 'EditorDBIOController@updatePublication' );
Route::post('/e_publications_delete', 'EditorDBIOController@deletePublication' );

Route::post('/f_cultivation_assignments_update', 'FarmerDBIOController@updateCultivationAssignment' );
Route::post('/f_operations_update', 'FarmerDBIOController@updateOperation' );
Route::post('/f_harvesting_assignments_update', 'FarmerDBIOController@updateHarvestingAssignment' );

Route::post('/d_update_delivery_status', 'DeliveryDBIOController@updateDeliveryStatus' );
Route::post('/d_update_delivery_problem', 'DeliveryDBIOController@updateDeliveryProblem' );

Route::post('/a_users_update', 'AdminDBIOController@updateUser' );
Route::post('/a_users_delete', 'AdminDBIOController@deleteUser' );
Route::post('/a_users_refill', 'AdminDBIOController@refillUser' );
Route::post('/a_users_debet', 'AdminDBIOController@debetUser' );
Route::post('/a_users_delete_refill', 'AdminDBIOController@deleteUserRefill' );
Route::post('/a_users_update_refill', 'AdminDBIOController@updateUserRefill' );
Route::post('/a_users_debet', 'AdminDBIOController@debetUser' );
Route::post('/a_users_delete_debeting', 'AdminDBIOController@deleteUserDebeting' );
Route::post('/a_crops_new', 'AdminDBIOController@newCrop' );
Route::post('/a_crops_update', 'AdminDBIOController@updateCrop' );
Route::post('/a_crops_delete', 'AdminDBIOController@deleteCrop' );
Route::post('/a_cultivation_assignments_new', 'AdminDBIOController@newCultivationAssignment' );
Route::post('/a_cultivation_assignments_update', 'AdminDBIOController@updateCultivationAssignment' );
Route::post('/a_cultivation_assignments_delete', 'AdminDBIOController@deleteCultivationAssignment' );
Route::post('/a_operations_new', 'AdminDBIOController@newOperation' );
Route::post('/a_operations_update', 'AdminDBIOController@updateOperation' );
Route::post('/a_operations_delete', 'AdminDBIOController@deleteOperation' );
Route::post('/a_harvesting_assignments_new', 'AdminDBIOController@newHarvestingAssignment' );
Route::post('/a_harvesting_assignments_update', 'AdminDBIOController@updateHarvestingAssignment' );
Route::post('/a_harvesting_assignments_delete', 'AdminDBIOController@deleteHarvestingAssignment' );
Route::post('/a_supplies_new', 'AdminDBIOController@newSupply' );
Route::post('/a_supplies_update', 'AdminDBIOController@updateSupply' );
Route::post('/a_supplies_delete', 'AdminDBIOController@deleteSupply' );
Route::post('/a_supplies_new_with_selected', 'AdminDBIOController@newSupplyWithSelected' );
Route::post('/a_supplies_debet', 'AdminDBIOController@debetForSupply' );
Route::post('/a_supplies_revoke_debetings', 'AdminDBIOController@revokeDebetingsForSupply' );
//Route::post('/a_delivery_assignments_new', 'AdminDBIOController@newDeliveryAssignments' );
Route::post('/a_farms_new', 'AdminDBIOController@newFarm' );
Route::post('/a_farms_update', 'AdminDBIOController@updateFarm' );
Route::post('/a_farms_delete', 'AdminDBIOController@deleteFarm' );
Route::post('/a_delivery_points_new', 'AdminDBIOController@newDeliveryPoint' );
Route::post('/a_delivery_points_update', 'AdminDBIOController@updateDeliveryPoint' );
Route::post('/a_delivery_points_delete', 'AdminDBIOController@deleteDeliveryPoint' );
Route::post('/a_delivery_units_new', 'AdminDBIOController@newDeliveryUnit' );
Route::post('/a_delivery_units_update', 'AdminDBIOController@updateDeliveryUnit' );
Route::post('/a_delivery_units_delete', 'AdminDBIOController@deleteDeliveryUnit' );
Route::post('/a_persons_new', 'AdminDBIOController@newPerson' );
Route::post('/a_persons_update', 'AdminDBIOController@updatePerson' );
Route::post('/a_persons_delete', 'AdminDBIOController@deletePerson' );
Route::post('/a_links_new', 'AdminDBIOController@newLink' );
Route::post('/a_links_update', 'AdminDBIOController@updateLink' );
Route::post('/a_links_delete', 'AdminDBIOController@deleteLink' );
Route::post('/a_texts_update', 'AdminDBIOController@updateText' );
Route::post('/a_slides_new', 'AdminDBIOController@newSlide' );
Route::post('/a_slides_update', 'AdminDBIOController@updateSlide' );
Route::post('/a_slides_delete', 'AdminDBIOController@deleteSlide' );

