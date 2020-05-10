<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use DB;
use Auth;
use App\MyHelpers;

function confirmAdminPrivilegies() {
	if ( Auth::check() ) {
		if( Auth::user()->admin_privilegies == 255 ) {
			return true;
		}
	}
	//response()->json( ['rows_affected'=>0, 'error_message'=>'Authorization error'] )->send();
	echo( json_encode( array('rows_affected'=>0, 'error_message'=>'Authorization error') ) );
	die();
}


class AdminDBIOController extends Controller
{
	public function updateUser(Request $request) 
	{
		confirmAdminPrivilegies();
		$key_value_pairs = [];
		if( $request->has( 'email' ) ) {
			if( strlen( $request->email ) > 0 ) { 
				$key_value_pairs[ 'email' ] = $request->email;
			}
		} 
		if( $request->has( 'delivery_point_id' ) ) {
			if( strlen($request->delivery_point_id) > 0 ) { 
				$key_value_pairs[ 'delivery_point_id' ] = $request->delivery_point_id;
			}
		} 
		if( $request->has( 'delivery_point_admin' ) ) {
			if( strlen($request->delivery_point_admin) > 0 ) { 
				$key_value_pairs[ 'delivery_point_admin' ] = $request->delivery_point_admin;
			}
		} 
		if( $request->has( 'sorting_station_admin' ) ) {
			if( strlen($request->sorting_station_admin) > 0 ) { 
				$key_value_pairs[ 'sorting_station_admin' ] = $request->sorting_station_admin;
			}
		} 
		if( $request->has( 'delivery_unit_admin' ) ) {
			if( strlen($request->delivery_unit_admin) > 0 ) { 
				$key_value_pairs[ 'delivery_unit_admin' ] = $request->delivery_unit_admin;
			}
		} 
		if( $request->has( 'farm_admin' ) ) {
			if( strlen($request->farm_admin) > 0 ) { 
				$key_value_pairs[ 'farm_admin' ] = $request->farm_admin;
			}
		} 
		if( $request->has( 'deposit' ) ) {
			if( strlen($request->deposit) > 0 ) { 	
				$key_value_pairs[ 'deposit' ] = $request->deposit;
			}
		} 
		if( $request->has( 'deposit_comment' ) ) {
			$key_value_pairs[ 'deposit_comment' ] = $request->deposit_comment;
		} 
		if( $request->hasFile('icon') ) {
			$mh = new MyHelpers();
			$key_value_pairs['icon'] = $mh->resizeImageAndEncode( $request ); 
		} else if( $request->icon_delete ) {
			$key_value_pairs['icon'] = null;	
		}
		$status = DB::table('users')->where('id', $request->id)->limit(1)->update( $key_value_pairs );
		return json_encode( array('rows_affected'=>1, 'error_message'=>'') );
	}


	public function deleteUser(Request $request) 
	{
		confirmAdminPrivilegies();
		$refills = DB::select( DB::raw( "SELECT * FROM `refills` WHERE user_id=" . $request->id . " LIMIT 1") );
		if(	sizeof( $refills ) > 0 ) {
			return json_encode( array('rows_affected'=>0, 'error_message'=>'The user has refills. Can not delete!') );
		}
		$debetings = DB::select( DB::raw( "SELECT * FROM `debetings` WHERE user_id=" . $request->id . " LIMIT 1") );
		if(	sizeof( $debetings ) > 0 ) {
			return json_encode( array('rows_affected'=>0, 'error_message'=>'The user has debetings. Can not delete!') );
		}

		$delete_query = "DELETE FROM `users` WHERE id=" . $request->id . " AND admin_privilegies <= 0 AND delivery_point_id <= 0"
			." AND delivery_point_admin <= 0 AND farm_admin <= 0 AND delivery_unit_admin <=0 AND balance=0 AND deposit=0 LIMIT 1";
		$deleted = DB::delete( DB::raw($delete_query) ); 	// Returns "0" if error...
		if( $deleted == 0 ) {	
			return json_encode( array('rows_affected'=>0, 
				'error_message'=>'The user has one of the following: a role, a non zero balance, a non zero deposit. Can not delete') );
		} 
		return json_encode( array('rows_affected'=>$deleted, 'error_message'=>'') );
	}

// **** REFILL AND DEBETING SECTION

	private function refillDebetingAmountHelper( Request $request ) {
		$amount = 0.0;
		if( $request->has( 'amount' ) ) {
			if( strlen($request->amount) > 0 ) { 	
				$amount = floatval( $request->amount );
			}
		} 
		return $amount;
	}

	public function refillUser(Request $request) 
	{
		confirmAdminPrivilegies();

		$amount = $this->refillDebetingAmountHelper( $request );
		if( !($amount > 0) ) { 	// Refilling...
			return json_encode( array('rows_affected'=>0, 'error_message'=>'Invalid refill value!') );
		}

		$result = -1;
		DB::transaction( function() use($request, $amount, &$result) {
			$pairs = [ 'user_id' => $request->id, 'amount' => $amount, 
				'title' => $request->title, 'made_at'=>$request->made_at, 'made_by_id' => Auth::user()->id ];
			$inserted = DB::table('refills')->insert( $pairs );

			// Updating user 
			$update_query = "UPDATE `users` SET balance = balance + " . $amount . " WHERE id=" . $request->id . " LIMIT 1";
			$updated = DB::update( DB::raw($update_query) );
			if( $inserted && $updated == 1 ) {
				$result = 1;
			}
		});
		return json_encode( array('rows_affected'=>$result, 'error_message'=>'') );
	}


	public function updateUserRefill(Request $request) 
	{
		confirmAdminPrivilegies();

		$amount = $this->refillDebetingAmountHelper( $request );
		if( !($amount > 0) ) { 	// Refilling...
			return json_encode( array('rows_affected'=>0, 'error_message'=>'Invalid refill value!') );
		}
		$refill = DB::select( DB::raw( "SELECT amount, user_id FROM `refills` WHERE id=" . $request->id . " LIMIT 1") );
		if(	sizeof( $refill ) == 0 ) {
			return json_encode( array('rows_affected'=>0, 'error_message'=>'Invalid refill Id!') );
		}

		$result = -1;
		DB::transaction( function() use($refill, $request, &$result) {					
			// Updating the refill
			$pairs = [ 'amount' => $request->amount, 'title' => $request->title, 'made_at'=>$request->made_at ];
			$updated_refill = DB::table('refills')->where('id', $request->id)->limit(1)->update( $pairs );
			// Updating user's balance
			$qu = "UPDATE `users` SET balance = balance - ".$refill[0]->amount." + ".$request->amount." WHERE id=".$refill[0]->user_id." LIMIT 1";
			$updated_user = DB::update( DB::raw($qu) );
			if( ($updated_refill && $updated_user) == 1 ) {
				$result = 1;
			}
		});
		return json_encode( array('rows_affected'=>$result, 'error_message'=>'') );
	}

	public function deleteUserRefill(Request $request) 
	{
		confirmAdminPrivilegies();

		if( !($request->has( 'id' ) ) ) {
			return json_encode( array('rows_affected'=>0, 'error_message'=>'Bad refill Id!') );
		}			                                                                             	
		$refill = DB::select( DB::raw( "SELECT * FROM `refills` WHERE id=" . $request->id . " LIMIT 1") );
		if(	!( sizeof( $refill ) > 0 ) ) {
			return json_encode( array('rows_affected'=>0, 'error_message'=>'Refill Id is not found!') );
		}

		$result = -1;
		DB::transaction( function() use($request, $refill, &$result) {
			// Updating user's balance
			$key_value_pairs = [ 'id' => $refill[0]->user_id, 'amount' => $refill[0]->amount ];
			$update_query = "UPDATE `users` SET balance=balance - " . $refill[0]->amount . " WHERE id=" . $refill[0]->user_id . " LIMIT 1";
			$updated = DB::update( DB::raw($update_query) );

			// Deleting the refill
			$delete_query = "DELETE FROM `refills` WHERE id=" . $request->id . " LIMIT 1";
			$deleted = DB::delete( DB::raw($delete_query) );
			$result = $updated;
		});
		return json_encode( array('rows_affected'=>$result, 'error_message'=>'') );
	}


	public function debetUser(Request $request) 
	{
		confirmAdminPrivilegies();

		$amount = $this->refillDebetingAmountHelper( $request );
		if( !($amount > 0) ) { 	// Refilling...
			return json_encode( array('rows_affected'=>0, 'error_message'=>'Invalid debet sum!') );
		}

		$result = -1;
		DB::transaction( function() use($request, &$result) {
			$pairs = [ 'user_id' => $request->id, 'amount' => $request->amount, 
				'title' => $request->title, 'made_at'=>$request->made_at, 'made_by_id' => Auth::user()->id ];
			$inserted = DB::table('debetings')->insert( $pairs );

			// Updating user 
			$update_query = "UPDATE `users` SET balance = balance - " . $request->amount . " WHERE id=" . $request->id . " LIMIT 1";
			$updated = DB::update( DB::raw($update_query) );
			if( $inserted && $updated == 1 ) {
				$result = 1;
			}
		});
		return json_encode( array('rows_affected'=>$result, 'error_message'=>'') );
	}

	public function updateUserDebeting(Request $request) 
	{
		confirmAdminPrivilegies();

		$amount = $this->refillDebetingAmountHelper( $request );
		if( !($amount > 0) ) { 	// Refilling...
			return json_encode( array('rows_affected'=>0, 'error_message'=>'Invalid debet sum!') );
		}
		$debeting = DB::select( DB::raw( "SELECT amount, user_id FROM `debetings` WHERE id=" . $request->id . " LIMIT 1") );
		if(	sizeof( $debeting ) == 0 ) {
			return json_encode( array('rows_affected'=>0, 'error_message'=>'Invalid debeting id!') );
		}

		$result = -1;
		DB::transaction( function() use($debeting, $request, &$result) {					
			// Updating the debeting
			$pairs = [ 'amount' => $request->amount, 'title' => $request->title, 'made_at'=>$request->made_at ];
			$updated_debeting = DB::table('debetings')->where('id', $request->id)->limit(1)->update( $pairs );
			// Updating user's balance
			$qu = "UPDATE `users` SET balance = balance + ".$debeting[0]->amount." - ".$request->amount." WHERE id=".$debeting[0]->user_id." LIMIT 1";
			$updated_user = DB::update( DB::raw($qu) );
			if( ($updated_debeting && $updated_user) == 1 ) {
				$result = 1;
			}
		});
		return json_encode( array('rows_affected'=>$result, 'error_message'=>'') );
	}

	public function deleteUserDebeting(Request $request) 
	{
		confirmAdminPrivilegies();
		if( !($request->has( 'id' ) ) ) {
			return json_encode( array('rows_affected'=>0, 'error_message'=>'Bad debeting id!') );
		}			                                                                             	
		$debeting = DB::select( DB::raw( "SELECT * FROM `debetings` WHERE id=" . $request->id . " LIMIT 1") );
		if(	!( sizeof( $debeting ) > 0 ) ) {
			return json_encode( array('rows_affected'=>0, 'error_message'=>'Debeting Id is not found!') );
		}

		$result = -1;
		DB::transaction( function() use($request, $debeting, &$result) {
			// Updating user's balance
			$key_value_pairs = [ 'id' => $debeting[0]->user_id, 'amount' => $debeting[0]->amount ];
			$update_query = "UPDATE `users` SET balance=balance + " . $debeting[0]->amount . " WHERE id=" . $debeting[0]->user_id . " LIMIT 1";
			$updated = DB::update( DB::raw($update_query) );

			// Deleting the debeting
			$delete_query = "DELETE FROM `debeting` WHERE id=" . $request->id . " LIMIT 1";
			$deleted = DB::delete( DB::raw($delete_query) );
			$result = $updated;
		});
		return json_encode( array('rows_affected'=>$result, 'error_message'=>'') );
	}

    //
	public function newCrop(Request $request) 
	{
		confirmAdminPrivilegies();

		$key_value_pairs = [ 'title'=>$request->title, 'descr'=>$request->descr ];
		if( $request->hasFile('icon') ) {
			$mh = new MyHelpers();
			$key_value_pairs['icon'] = $mh->resizeImageAndEncode( $request ); 
		}  
		$status = DB::table('crops')->insert( $key_value_pairs );
		return json_encode( array('rows_affected'=>1, 'error_message'=>'') );
	}

	public function updateCrop(Request $request) 
	{
		confirmAdminPrivilegies();

		$key_value_pairs = [ 'title'=>$request->title, 'descr'=>$request->descr ];
		//if( property_exists( $request, 'icon' ) ) {
		if( $request->hasFile('icon') ) {
			$mh = new MyHelpers();
			$key_value_pairs['icon'] = $mh->resizeImageAndEncode( $request ); 
		} //else if( property_exists( $request, 'icon_delete' ) ) {
		else if( $request->icon_delete ) {
			$key_value_pairs['icon'] = null;	
		}
		$status = DB::table('crops')->where('id', $request->id)->limit(1)->update( $key_value_pairs );
		return json_encode( array('rows_affected'=>1, 'error_message'=>'') );
	}


	public function newCultivationAssignment(Request $request) 
	{
		confirmAdminPrivilegies();
		$key_value_pairs = [ 'title'=>$request->title, 'descr'=>$request->descr, 
			'farm_id'=>$request->farm_id, 'crop_id'=>$request->crop_id,
			'amount_by_plan'=>$request->amount_by_plan, 'start_by_plan'=>$request->start_by_plan, 'finish_by_plan'=>$request->finish_by_plan,
			'amount_prognosed'=>$request->amount_by_plan, 'start_prognosed'=>$request->start_by_plan, 'finish_prognosed'=>$request->finish_by_plan,
			'amount_actual'=>0, 'start_actual'=>null, 'finish_actual'=>null, 'square'=>$request->square ];
		$status = DB::table('cultivation_assignments')->insert( $key_value_pairs );
		$r = ($status) ? 1 : 0;
		return json_encode( array('rows_affected'=>$r, 'error_message'=>'') );
	}

	public function updateCultivationAssignment(Request $request) 
	{
		confirmAdminPrivilegies();

		$key_value_pairs = [ 'title'=>$request->title, 'descr'=>$request->descr ];
		if( $request->is_accepted == 0 ) {
			$key_value_pairs['crop_id'] = $request->crop_id;
			$key_value_pairs['farm_id'] = $request->farm_id;
			$key_value_pairs['amount_by_plan'] = $request->amount_by_plan;
			$key_value_pairs['start_by_plan'] = $request->start_by_plan;
			$key_value_pairs['finish_by_plan'] = $request->finish_by_plan;
			$key_value_pairs['amount_prognosed'] = $request->amount_by_plan;
			$key_value_pairs['start_prognosed'] = $request->start_by_plan;
			$key_value_pairs['finish_prognosed'] = $request->finish_by_plan;
			$key_value_pairs['square'] = $request->square;
		}	

		$status = DB::table('cultivation_assignments')->where('id', $request->id)->limit(1)->update( $key_value_pairs );
		return json_encode( array('rows_affected'=>1, 'error_message'=>'') );
	}


	public function deleteCultivationAssignment(Request $ca) 
	{
		confirmAdminPrivilegies();

		$query = "SELECT is_accepted, is_finished FROM `cultivation_assignments` WHERE id=" . $ca->id . " LIMIT 1";
		$is = DB::select( DB::raw($query) );
		if( sizeof($is) > 0 ) {
			if( $is[0]->is_accepted == 1 || $is[0]->is_finished == 1 ) {
				return json_encode( array('rows_affected'=>0, 'error_message'=>'Can\'t delete an assignment accepted or finished by a farmer...') );
			}
		}
		// If the assignment has a related harvesting assignment...
		$query = "SELECT id FROM `harvesting_assignments` WHERE cultivation_assignment_id=" . $ca->id . " LIMIT 1";
		$is = DB::select( DB::raw($query) );
		if( sizeof($is) > 0 ) {
			return json_encode( array('rows_affected'=>0, 'error_message'=>'This Cultivation Assignemnt has a related Harvesting Attachment...') );
		}

		// Deleting the cultivation assignment
		$query = "DELETE FROM `operations` WHERE cultivation_assignment_id=" . $ca->id;
		$delete = DB::delete( DB::raw($query) );

		// Deleting the cultivation assignment
		$query = "DELETE FROM `cultivation_assignments` WHERE id=" . $ca->id . " LIMIT 1";
		$delete = DB::delete( DB::raw($query) );
		return json_encode( array('rows_affected'=>1, 'error_message'=>'') );
	}


	public function newOperation(Request $op) 
	{
		confirmAdminPrivilegies();

		$key_value_pairs = [ 'title'=>$op->title, 'descr'=>$op->descr, 'cultivation_assignment_id'=>$op->cultivation_assignment_id,
			'start_by_plan'=>$op->start_by_plan, 'finish_by_plan'=>$op->finish_by_plan ];
		$status = DB::table('operations')->insert( $key_value_pairs );
		$r = ($status) ? 1 : 0;
		return json_encode( array('rows_affected'=>$r, 'error_message'=>'') );
	}

	public function updateOperation(Request $request) 
	{
		confirmAdminPrivilegies();

		$key_value_pairs = [ 'title'=>$request->title, 'descr'=>$request->descr, 
			'start_by_plan'=>$request->start_by_plan, 'finish_by_plan'=>$request->finish_by_plan ];
		$status = DB::table('operations')->where('id', $request->id)->limit(1)->update( $key_value_pairs );
		return json_encode( array('rows_affected'=>1, 'error_message'=>'') );
	}


	public function deleteOperation(Request $op) 
	{
		confirmAdminPrivilegies();

		$query = "DELETE FROM `operations` WHERE id=" . $op->id . " LIMIT 1";
		$delete = DB::delete( DB::raw($query) );
		return json_encode( array('rows_affected'=>$delete, 'error_message'=>'') );
	}


	private function createHarvestingAssignmentKeyValuePairs(Request $ha) {
		$key_value_pairs = [ 'title'=>$ha->title, 'descr'=>$ha->descr, 'cultivation_assignment_id'=>$ha->cultivation_assignment_id, 
			'crop_id'=>$ha->crop_id, 'farm_id'=>$ha->farm_id, 'supply_id'=>$ha->supply_id,
			'amount_by_plan'=>$ha->amount_by_plan, 'start_by_plan'=>$ha->start_by_plan, 'finish_by_plan'=>$ha->finish_by_plan,
			'amount_prognosed'=>$ha->amount_by_plan, 'start_prognosed'=>$ha->start_by_plan, 'finish_prognosed'=>$ha->finish_by_plan ];
		return $key_value_pairs;
	}		

	public function newHarvestingAssignment(Request $ha) 
	{
		confirmAdminPrivilegies();		
		$pairs = $this->createHarvestingAssignmentKeyValuePairs( $ha );
		$status = DB::table('harvesting_assignments')->insert( $pairs );
		$r = ($status) ? 1 : 0;
		return json_encode( array('rows_affected'=>$r, 'error_message'=>'') );
	}

	public function updateHarvestingAssignment( Request $ha ) {
		$pairs = $this->createHarvestingAssignmentKeyValuePairs( $ha );
		$status = DB::table('harvesting_assignments')->where('id',$ha->id)->update( $pairs );
		return json_encode( array('rows_affected'=>1, 'error_message'=>'') );
	}


	public function deleteHarvestingAssignment(Request $ha) 
	{
		confirmAdminPrivilegies();

		// If the assignment is accepted or finished...
		$query = "SELECT is_accepted, is_finished, supply_id FROM `harvesting_assignments` WHERE id=" . $ha->id . " LIMIT 1";
		$is = DB::select( DB::raw($query) );
		if( sizeof($is) > 0 ) {
			if( $is[0]->is_accepted == 1 || $is[0]->is_finished == 1 ) {
				return json_encode( array( 'rows_affected'=>0, 'error_message'=>'Can\'t delete an assignment accepted or finished by a farmer...' ) );
			}
			if( $is[0]->supply_id > -1 ) {
				return json_encode( array( 'rows_affected'=>0, 'error_message'=>'Can\'t delete an assignment invovlved into a supply...' ) );
			}
		}

		// Deleting the harvesting assignment
		$query = "DELETE FROM `harvesting_assignments` WHERE id=" . $ha->id . " LIMIT 1";
		$delete = DB::delete( DB::raw($query) );
		return json_encode( array('rows_affected'=>$delete, 'error_message'=>'') );
	}


	public function newSupply(Request $supply) 
	{
		confirmAdminPrivilegies();

		$key_value_pairs = [ 'title'=>$supply->title, 'descr'=>$supply->descr, 'delivery_info'=>$supply->delivery_info,
			'deliver_from'=>$supply->deliver_from, 'deliver_to'=>$supply->deliver_to ];
		if( strlen($supply->icon) > 0 ) {
			$mh = new MyHelpers();
			$key_value_pairs['icon'] = $mh->resizeImageAndEncode( $supply ); 
		} else {
			$key_value_pairs['icon'] = null; // Config::get('myconstants.emptyIcon');		
		}
		if( is_numeric($supply->price_per_user) ) {
			$key_value_pairs['price_per_user'] = $supply->price_per_user;		
		} else {
			$key_value_pairs['price_per_user'] = null;		
		}
		$status = DB::table('supplies')->insert( $key_value_pairs );
		$r = ($status) ? 1 : 0;
		return json_encode( array('rows_affected'=>$r, 'error_message'=>'') );
	}


	public function updateSupply(Request $supply) 
	{
		confirmAdminPrivilegies();

		$key_value_pairs = [ 'title'=>$supply->title, 'descr'=>$supply->descr,
			'deliver_from'=>$supply->deliver_from, 'deliver_to'=>$supply->deliver_to ];
		if( strlen($supply->icon) > 0 ) {
			$mh = new MyHelpers();
			$key_value_pairs['icon'] = $mh->resizeImageAndEncode( $supply ); 
		} else {
			$key_value_pairs['icon'] = null; // Config::get('myconstants.emptyIcon');		
		}
		if( is_numeric($supply->price_per_user) ) {
			$key_value_pairs['price_per_user'] = $supply->price_per_user;		
		} else {
			$key_value_pairs['price_per_user'] = null;		
		}
		$status = DB::table('supplies')->where('id',$supply->id)->update( $key_value_pairs );
		return json_encode( array('rows_affected'=>1, 'error_message'=>'') );
	}


	private function revokeDebetingsForSupplyHelper( Request $supply ) { 		// $request - a supply 
		$query = "UPDATE `users` INNER JOIN `debetings` ON (users.id=debetings.user_id AND debetings.supply_id=". $supply->id . ")" .
	 		" SET users.balance=users.balance+debetings.amount";
		$updated = DB::update( DB::raw($query) );
		$query = "DELETE FROM `debetings` WHERE (debetings.supply_id=". $supply->id . ")";
		$deleted = DB::delete( DB::raw($query) );
		return $deleted;
	}


	public function deleteSupply(Request $supply) 
	{
		confirmAdminPrivilegies();

		// If the supply is delivered...
		$query = "SELECT is_delivered FROM `supplies` WHERE id=" . $supply->id;
		$da = DB::select( DB::raw($query) );
		if( sizeof($da) > 0 ) {
			if( $da[0]->is_delivered == 1 ) {
				return json_encode( array('rows_affected'=>0, 'error_message'=>'The supply has already been delivered. Can\'t delete...' ) );
			}
		}
		// If the supply is delivered...
		$query = "SELECT is_delivered FROM `debetings` WHERE supply_id=" . $supply->id;
		$da = DB::select( DB::raw($query) );
		if( sizeof($da) > 0 ) {
			if( $da[0]->is_delivered == 1 ) {
				return json_encode( array('rows_affected'=>0, 'error_message'=>'The supply has already (partly) delivered. Can\'t delete...' ) );
			} else {
				return json_encode( array('rows_affected'=>0, 'error_message'=>'The supply has been debeted for. Revoke debetings first...' ) );
			}
		}

		$result = -1;
		DB::transaction( function() use($supply, &$result) {
			// YET WE MUST UNROLL AND DELETE ALL DEBETINGS 
    	    $query = "UPDATE users INNER JOIN debetings ON (users.id=debetings.user_id) SET users.balance = users.balance + debetings.amount".
				" WHERE debetings.supply_id=".$supply->id;
			$update = DB::update( DB::raw($query) );
        
			$query = "DELETE FROM `debetings` WHERE debetings.supply_id=" . $supply->id;
			$update = DB::delete( DB::raw($query) );
		
			// Deleting the supply
			$query = "DELETE FROM `supplies` WHERE id=" . $supply->id;
			$result = DB::delete( DB::raw($query) );
		});
		return json_encode( array('rows_affected'=>$result, 'error_message'=>'') );
	}


	public function debetForSupply( Request $supply ) { 		// $request - a supply 
		confirmAdminPrivilegies();

		$price_per_user = NULL;
		if( $supply->has( 'price_per_user' ) ) {
			if( strlen($supply->price_per_user) > 0 ) {
				$value = (float)$supply->price_per_user;
				if( $value > 0 ) {
					$price_per_user = $value;
				}
			}
		}
		if( $price_per_user == NULL ) {
			return json_encode( array('records_affected' => 0, 'error_message' => 'Invalid value for Price per User' ) );
		}

		$result = -1;
		DB::transaction( function() use($supply, $price_per_user, &$result) {
			$deleted = $this->revokeDebetingsForSupplyHelper( $supply );
			$query =  "INSERT INTO `debetings` (user_id, amount, supply_id, delivery_point_id, title) ".
				"SELECT users.id as user_id, " . $price_per_user . " as amount, " . $supply->id . 
				" as supply_id, users.delivery_point_id, '" . addslashes($supply->title) . "' as title FROM users".
				" WHERE (users.balance > " . $price_per_user . " AND users.is_suspended_for_supply!=1)";
			$inserted = DB::insert( DB::raw($query) );

			$query = "UPDATE `users` INNER JOIN `debetings` ON ".
				"(users.id=debetings.user_id AND debetings.supply_id=" . $supply->id . ") ".
				"SET users.balance=users.balance-" . $price_per_user;
			$result = DB::update( DB::raw($query) );
		});
		return json_encode( array('rows_affected'=>$result, 'error_message'=>'') );
	}


	public function revokeDebetingsForSupply( Request $supply ) { 		// $request - a supply 
		confirmAdminPrivilegies();

		$result = -1;
		DB::transaction( function() use($supply, &$result) {
			$result = $this->revokeDebetingsForSupplyHelper( $supply );
		});
		return json_encode( array('rows_affected'=>$result, 'error_message'=>'') );
	}


	// *********************************************************************************************************************************
	// Farm section 
	private function validateFarmRequestHelper( $farm ) {
		if( !strlen($farm->title) > 0 || !strlen($farm->descr) > 0 || !strlen($farm->address) > 0 || 
			!strlen($farm->latitude) > 0 || !strlen($farm->longitude) > 0 || !strlen($farm->square) > 0 ) {
			return 'Please provide valid values for every required field';
		} 
		return '';
	}

	private function createFarmKeyValuePairsHelper( $farm ) {
		$key_value_pairs = [ 'title'=>$farm->title, 'descr'=>$farm->descr, 'address'=>$farm->address,
			'latitude'=>$farm->latitude, 'longitude'=>$farm->longitude, 'square'=>$farm->square ];
		if( $farm->hasFile('icon') ) {
			$mh = new MyHelpers();
			$key_value_pairs['icon'] = $mh->resizeImageAndEncode( $farm ); 
		}  
		if( strlen($farm->prepared_square) > 0 ) {
			$key_value_pairs['prepared_square'] = $farm->prepared_square;		
		} else {
			$key_value_pairs['prepared_square'] = NULL;		
		}
		return $key_value_pairs;
	}


	public function newFarm(Request $farm) 
	{
		confirmAdminPrivilegies();

		$valid = $this->validateFarmRequestHelper( $farm );
		if( strlen( $valid ) > 0 ) {
			return json_encode( array('rows_affected'=>0, 'error_message'=>$valid) );
		}
		$key_value_pairs = $this->createFarmKeyValuePairsHelper($farm);

		$status = DB::table('farms')->insert( $key_value_pairs );
		$r = ($status) ? 1 : 0;
		return json_encode( array('rows_affected'=>$r, 'error_message'=>'') );
	}


	public function updateFarm(Request $farm) 
	{
		confirmAdminPrivilegies();

		$valid = $this->validateFarmRequestHelper( $farm );
		if( strlen( $valid ) > 0 ) {
			json_encode( array('rows_affected'=>0, 'error_message'=>$valid) );
		}
		$key_value_pairs = $this->createFarmKeyValuePairsHelper($farm);

		$status = DB::table('farms')->where('id',$farm->id)->update( $key_value_pairs );
		return json_encode( array('rows_affected'=>1, 'error_message'=>'') );
	}


	public function deleteFarm(Request $farm) 
	{
		confirmAdminPrivilegies();

		$query = "DELETE FROM `farms` WHERE id=" . $farm->id;
		$deleted = DB::delete( DB::raw($query) );
		return json_encode( array('rows_affected'=>$deleted, 'error_message'=>'') );
	}


	// *********************************************************************************************************************************
	// Delivery Point Section 

	private function validateDeliveryPointRequestHelper( $dp ) {
		if( !strlen($dp->title) > 0 || !strlen($dp->descr) > 0 || !strlen($dp->address) > 0 || 
			!strlen($dp->latitude) > 0 || !strlen($dp->longitude) > 0 ) {
			return 'Please provide valid values for every required field';
		} 
		return '';
	}

	private function createDeliveryPointKeyValuePairsHelper( $dp ) {
		$key_value_pairs = [ 'title'=>$dp->title, 'descr'=>$dp->descr, 'address'=>$dp->address,
			'latitude'=>$dp->latitude, 'longitude'=>$dp->longitude, 'delivery_info'=>$dp->delivery_info, 'pickup_info'=>$dp->pickup_info ];
		if( $dp->hasFile('icon') ) {
			$mh = new MyHelpers();
			$key_value_pairs['icon'] = $mh->resizeImageAndEncode( $dp ); 
		}  else if( $dp->icon_delete ) {
			$key_value_pairs['icon'] = null;	
		}
		return $key_value_pairs;
	}


	public function newDeliveryPoint(Request $dp) 
	{
		confirmAdminPrivilegies();

		$valid = $this->validateDeliveryPointRequestHelper( $dp );
		if( strlen( $valid ) > 0 ) {
			return json_encode( array('rows_affected'=>0, 'error_message'=>$valid) );
		}
		$key_value_pairs = $this->createDeliveryPointKeyValuePairsHelper($dp);

		$status = DB::table('delivery_points')->insert( $key_value_pairs );
		$r = ($status) ? 1 : 0;
		return json_encode( array('rows_affected'=>$r, 'error_message'=>'') );
	}


	public function updateDeliveryPoint(Request $dp) 
	{
		confirmAdminPrivilegies();

		$valid = $this->validateDeliveryPointRequestHelper( $dp );
		if( strlen( $valid ) > 0 ) {
			json_encode( array('rows_affected'=>0, 'error_message'=>$valid) );
		}
		$key_value_pairs = $this->createDeliveryPointKeyValuePairsHelper($dp);

		$status = DB::table('delivery_points')->where('id',$dp->id)->update( $key_value_pairs );
		return json_encode( array('rows_affected'=>1, 'error_message'=>'') );
	}


	public function deleteDeliveryPoint(Request $dp) 
	{
		confirmAdminPrivilegies();

		$query = "DELETE FROM `delivery_points` WHERE id=" . $dp->id;
		$deleted = DB::delete( DB::raw($query) );
		return json_encode( array('rows_affected'=>$deleted, 'error_message'=>'') );
	}
	

	// *********************************************************************************************************************************
	// Delivery Unit Section 

	private function validateDeliveryUnitRequestHelper( $du ) {
		if( !strlen($du->title) > 0 ) {
			return 'Please provide valid values for every required field';
		} 
		return '';
	}

	private function createDeliveryUnitKeyValuePairsHelper( $du ) {
		$key_value_pairs = [ 'title'=>$du->title, 'descr'=>$du->descr, 'tonnage'=>$du->tonnage, 'volume'=>$du->volume ];
		if( $du->hasFile('icon') ) {
			$mh = new MyHelpers();
			$key_value_pairs['icon'] = $mh->resizeImageAndEncode( $du ); 
		}  else if( $du->icon_delete ) {
			$key_value_pairs['icon'] = null;	
		}

		return $key_value_pairs;
	}


	public function newDeliveryUnit(Request $du) 
	{
		confirmAdminPrivilegies();

		$valid = $this->validateDeliveryUnitRequestHelper( $du );
		if( strlen( $valid ) > 0 ) {
			return json_encode( array('rows_affected'=>0, 'error_message'=>$valid) );
		}
		$key_value_pairs = $this->createDeliveryUnitKeyValuePairsHelper($du);

		$status = DB::table('delivery_units')->insert( $key_value_pairs );
		$r = ($status) ? 1 : 0;
		return json_encode( array('rows_affected'=>$r, 'error_message'=>'') );
	}


	public function updateDeliveryUnit(Request $du) 
	{
		confirmAdminPrivilegies();

		$valid = $this->validateDeliveryUnitRequestHelper( $du );
		if( strlen( $valid ) > 0 ) {
			json_encode( array('rows_affected'=>0, 'error_message'=>$valid) );
		}
		$key_value_pairs = $this->createDeliveryUnitKeyValuePairsHelper($du);

		$status = DB::table('delivery_units')->where('id',$du->id)->update( $key_value_pairs );
		return json_encode( array('rows_affected'=>1, 'error_message'=>'') );
	}


	public function deleteDeliveryUnit(Request $du) 
	{
		confirmAdminPrivilegies();

		$query = "DELETE FROM `delivery_units` WHERE id=" . $du->id;
		$deleted = DB::delete( DB::raw($query) );
		return json_encode( array('rows_affected'=>$deleted, 'error_message'=>'') );
	}


	// ************************************************************************************************************************
	// **** Links
	private function validateLinkRequestHelper( $req ) {
		if( !strlen($req->url) > 0 || !strlen($req->title) > 0 ) {
			return 'Please provide valid values for every required field';
		} 
		return '';
	}

	private function createLinkKeyValuePairsHelper( $req ) {
		$key_value_pairs = [ 'url'=>$req->url, 'title'=>$req->title, 'descr'=>$req->descr ];
		if( $req->hasFile('icon') ) {
			$mh = new MyHelpers();
			$key_value_pairs['icon'] = $mh->resizeImageAndEncode( $req ); 
		} else if( $req->icon_delete ) {
			$key_value_pairs['icon'] = null;	
		}

		return $key_value_pairs;
	}

	public function newLink(Request $req) 
	{
		confirmAdminPrivilegies();

		$valid = $this->validateLinkRequestHelper( $req );
		if( strlen( $valid ) > 0 ) {
			return json_encode( array('rows_affected'=>0, 'error_message'=>$valid) );
		}
		$key_value_pairs = $this->createLinkKeyValuePairsHelper($req);

		$status = DB::table('links')->insert( $key_value_pairs );
		$r = ($status) ? 1 : 0;
		return json_encode( array('rows_affected'=>$r, 'error_message'=>'') );
	}


	public function updateLink(Request $req) 
	{
		confirmAdminPrivilegies();

		$valid = $this->validateLinkRequestHelper( $req );
		if( strlen( $valid ) > 0 ) {
			json_encode( array('rows_affected'=>0, 'error_message'=>$valid) );
		}
		$key_value_pairs = $this->createLinkKeyValuePairsHelper($req);

		$status = DB::table('links')->where('id', $req->id)->update( $key_value_pairs );
		return json_encode( array('rows_affected'=>1, 'error_message'=>'') );
	}


	public function deleteLink(Request $req) 
	{
		confirmAdminPrivilegies();

		$query = "DELETE FROM `links` WHERE id=" . $req->id;
		$deleted = DB::delete( DB::raw($query) );
		return json_encode( array('rows_affected'=>$deleted, 'error_message'=>'') );
	}


	// ************************************************************************************************************************
	// **** Slides
	private function validateSlideRequestHelper( $req ) {
		if( !strlen($req->title) > 0 || !strlen($req->image_url) > 0 ) {
			return 'Please provide valid values for every required field';
		} 
		return '';
	}

	private function createSlideKeyValuePairsHelper( $req ) {
		$key_value_pairs = [ 'title'=>$req->title, 'image_url'=>$req->image_url];
		$key_value_pairs['descr'] = ( !strlen($req->descr) > 0 ) ? '' : $req->descr;
		return $key_value_pairs;
	}

	public function newSlide(Request $req) 
	{
		confirmAdminPrivilegies();

		$valid = $this->validateSlideRequestHelper( $req );
		if( strlen( $valid ) > 0 ) {
			return json_encode( array('rows_affected'=>0, 'error_message'=>$valid) );
		}
		$key_value_pairs = $this->createSlideKeyValuePairsHelper($req);

		$status = DB::table('slides')->insert( $key_value_pairs );
		$r = ($status) ? 1 : 0;
		return json_encode( array('rows_affected'=>$r, 'error_message'=>'') );
	}


	public function updateSlide(Request $req) 
	{
		confirmAdminPrivilegies();

		$valid = $this->validateSlideRequestHelper( $req );
		if( strlen( $valid ) > 0 ) {
			json_encode( array('rows_affected'=>0, 'error_message'=>$valid) );
		}
		$key_value_pairs = $this->createSlideKeyValuePairsHelper($req);

		$status = DB::table('slides')->where('id', $req->id)->update( $key_value_pairs );
		return json_encode( array('rows_affected'=>1, 'error_message'=>'') );
	}


	public function deleteSlide(Request $req) 
	{
		confirmAdminPrivilegies();

		$query = "DELETE FROM `slide` WHERE id=" . $req->id;
		$deleted = DB::delete( DB::raw($query) );
		return json_encode( array('rows_affected'=>$deleted, 'error_message'=>'') );
	}


	// ************************************************************************************************************************
	// **** Texts 
	private function validateTextRequestHelper( $req ) {
		if( !strlen($req->title) > 0 ) {
			return 'Please provide valid values for every required field';
		} 
		return '';
	}

	private function createTextKeyValuePairsHelper( $req ) {
		$key_value_pairs = [ 'title'=>$req->title ];
		$key_value_pairs['descr'] = ( !strlen($req->descr) > 0 ) ? '' : $req->descr;
		return $key_value_pairs;
	}

	public function updateText(Request $req) 
	{
		confirmAdminPrivilegies();

		$valid = $this->validateTextRequestHelper( $req );
		if( strlen( $valid ) > 0 ) {
			json_encode( array('rows_affected'=>0, 'error_message'=>$valid) );
		}
		$key_value_pairs = $this->createTextKeyValuePairsHelper($req);
		
		$table = 'texts_en';
		if( $req->has('lang') ) {
			if( $req->lang == 'RU' ) {
				$table = 'texts_ru';
			}
		}
		$status = DB::table( $table )->where('id', $req->id)->update( $key_value_pairs );
		return json_encode( array('rows_affected'=>1, 'error_message'=>'') );
	}


	// ************************************************************************************************************************
	// **** Persons
	private function validatePersonRequestHelper( $req ) {
		if( !strlen($req->name) > 0 ) {
			return 'Please provide valid values for every required field';
		} 
		return '';
	}

	private function createPersonKeyValuePairsHelper( $req ) {
		$key_value_pairs = [ 'name'=>$req->name ];
		$key_value_pairs['position'] = ( !strlen($req->position) > 0 ) ? '' : $req->position;
		$key_value_pairs['descr'] = ( !strlen($req->descr) > 0 ) ? '' : $req->descr;
		if( $req->hasFile('icon') ) {
			$mh = new MyHelpers();
			$key_value_pairs['icon'] = $mh->resizeImageAndEncode( $req ); 
		} else if( $request->icon_delete ) {
			$key_value_pairs['icon'] = null;	
		}
		return $key_value_pairs;
	}

	public function newPerson(Request $req) 
	{
		confirmAdminPrivilegies();

		$valid = $this->validatePersonRequestHelper( $req );
		if( strlen( $valid ) > 0 ) {
			return json_encode( array('rows_affected'=>0, 'error_message'=>$valid) );
		}
		$key_value_pairs = $this->createPersonKeyValuePairsHelper($req);

		$status = DB::table('persons')->insert( $key_value_pairs );
		$r = ($status) ? 1 : 0;
		return json_encode( array('rows_affected'=>$r, 'error_message'=>'') );
	}


	public function updatePerson(Request $req) 
	{
		confirmAdminPrivilegies();

		$valid = $this->validatePersonRequestHelper( $req );
		if( strlen( $valid ) > 0 ) {
			json_encode( array('rows_affected'=>0, 'error_message'=>$valid) );
		}
		$key_value_pairs = $this->createPersonKeyValuePairsHelper($req);

		$status = DB::table('persons')->where('id', $req->id)->update( $key_value_pairs );
		return json_encode( array('rows_affected'=>1, 'error_message'=>'') );
	}


	public function deletePerson(Request $req) 
	{
		confirmAdminPrivilegies();

		$query = "DELETE FROM `persons` WHERE id=" . $req->id;
		$deleted = DB::delete( DB::raw($query) );
		return json_encode( array('rows_affected'=>$deleted, 'error_message'=>'') );
	}


	// **********************************************************************************************************************************
	// **** Image manipulation section
	/*
	private function composeImage() {
		$outputW = 240;
		$outputH = 240;
		$outputImage = imagecreatetruecolor($outputW, $outputH);
	
		imagefill($outputImage, 0, 0, imagecolorallocate($outputImage, 255, 255, 255) );

		$sourceImages = array();

		$emptyImageDecoded = base64_decode( Config::get('myconstants.emptyIcon') );
		$sourceImage = imagecreatefromstring($emptyImageDecoded);
	
		array_push($sourceImages, $sourceImage );
		array_push($sourceImages, $sourceImage );
		array_push($sourceImages, $sourceImage );
		array_push($sourceImages, $sourceImage );
	
		$srcW = 200;
		$srcH = 200;	
		$scaler = 0.6;
		$dstW = (int)($srcW * $scaler);
		$dstH = (int)($srcH * $scaler);
		$marginX = (int)($outputW - $dstW);
		$marginY = (int)($outputH - $dstH);

		imagecopyresized($outputImage,$sourceImages[0], 0, 0, 0, 0, $dstW, $dstH, $srcW, $srcH);
		imagecopyresized($outputImage,$sourceImages[1], $marginX, $marginY, 0, 0, $dstW, $dstH, $srcW, $srcH);
		imagecopyresized($outputImage,$sourceImages[2], $marginX, (int)($marginY/4.0), 0, 0, $dstW, $dstH, $srcW, $srcH);
		imagecopyresized($outputImage,$sourceImages[3], (int)($marginX/4.0), $marginY, 0, 0, $dstW, $dstH, $srcW, $srcH);

		ob_start();
		imagejpeg($outputImage);
		$buffer = ob_get_clean();

		$buffer = base64_encode($buffer);
		return $buffer;
	}

	function resizeImageAndEncode($request) {
		if( !$request->file('icon')->isValid() ) {
			return "";
		}

		$valid_ext = array('png','jpeg','jpg','gif');
		$fileext = strtolower( $request->file('icon')->extension() );
		if( !in_array($fileext, $valid_ext) ) {
			return null;
		}
	
		$tmpname = $request->file('icon')->getPathName();

		$original_info = getimagesize($tmpname);
		//echo($original_info);
		$original_w = $original_info[0];
		$original_h = $original_info[1];
		$original_img = null;	
		if ($original_info['mime'] == 'image/jpeg') {
    		$original_img = imagecreatefromjpeg($tmpname);
		} elseif ($original_info['mime'] == 'image/jpg') {
    		$original_img = imagecreatefromjpeg($tmpname);
		} elseif ($original_info['mime'] == 'image/png') {
    		$original_img = imagecreatefrompng($tmpname);
		} elseif ($original_info['mime'] == 'image/gif') {
    		$original_img = imagecreatefromgif($tmpname);
		}

		$thumb_w = 200;
		$thumb_h = 200;
		$thumb_img = imagecreatetruecolor($thumb_w, $thumb_h);
		imagecopyresampled( $thumb_img, $original_img, 0, 0, 0, 0, $thumb_w, $thumb_h, $original_w, $original_h );

		ob_start();
		imagejpeg($thumb_img);
		$buffer = ob_get_clean();

		$buffer = base64_encode($buffer);
		return $buffer;
	}
	*/
}


/*
function resizeImageAndEncode() {
	if( !isset( $_FILES['icon'] ) ) {
		return "";
	}
	if( !isset( $_FILES['icon']['name'] ) ) {
		return "";
	}
	if( $_FILES["icon"]["error"] != 0 ) {
		return "";
	}
	$filename = $_FILES["icon"]["name"];
	if( !(strlen( $filename ) > 0) ) {
		return "";
	}

	$valid_ext = array('png','jpeg','jpg','gif');
	$fileext = strtolower(pathinfo($filename,PATHINFO_EXTENSION));
	if( !in_array($fileext,$valid_ext) ) {
		return null;
	}
	
	$tmpname = $_FILES['icon']['tmp_name'];

	$original_info = getimagesize($tmpname);
	//echo($original_info);
	$original_w = $original_info[0];
	$original_h = $original_info[1];
	$original_img = null;	
	if ($original_info['mime'] == 'image/jpeg') {
    	$original_img = imagecreatefromjpeg($tmpname);
	} elseif ($info['mime'] == 'image/jpg') {
    	$original_img = imagecreatefromjpeg($tmpname);
	} elseif ($info['mime'] == 'image/png') {
    	$original_img = imagecreatefrompng($tmpname);
	} elseif ($info['mime'] == 'image/gif') {
    	$original_img = imagecreatefromgif($tmpname);
	}

	$thumb_w = 200;
	$thumb_h = 200;
	$thumb_img = imagecreatetruecolor($thumb_w, $thumb_h);
	imagecopyresampled( $thumb_img, $original_img, 0, 0, 0, 0, $thumb_w, $thumb_h, $original_w, $original_h );

	ob_start();
	imagejpeg($thumb_img);
	$buffer = ob_get_clean();
	//ob_end_clean();

	$buffer = base64_encode($buffer);
	return $buffer;
}
*/