<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use DB;
use Auth;

function confirmAdminPrivilegies() {
	//if ( !Auth::check() ) {
	//	return false;
	//}
	//if( !( Auth::user()->admin_privilegies > 0) ) {
	//	return false;
	//}
	return true;
}


class WorkflowController extends Controller
{
	public function index($what) {
		$this->indexParam($what, NULL); 
	}

	public function indexParam($what,$param=NULL) 
	{
		// $request->has( 'email' )

		$sus = array(); 	// Supplies...
		if( $what == 'all' ) {
			$sus_query = "SELECT * FROM `supplies` WHERE is_delivered=0";
			$sus = DB::select( DB::raw($sus_query) );
		} elseif( $what == 'by_supply_id' ) {
			$sus_query = "SELECT * FROM `supplies` WHERE id=" . $param . " LIMIT 1";
			$sus = DB::select( DB::raw($sus_query) );
		} elseif( $what == 'by_ca_id' ) {
			$sus_query = "SELECT * FROM `supplies` WHERE id IN (SELECT supply_id FROM `harvesting_assignments` WHERE cultivation_assignment_id=" . $param . ")";
			$sus = DB::select( DB::raw($sus_query) );
		} elseif( $what == 'by_ha_id' ) {
			$sus_query = "SELECT * FROM `supplies` WHERE id IN (SELECT supply_id FROM `harvesting_assignments` WHERE id=" . $param . ")";
			$sus = DB::select( DB::raw($sus_query) );
		} elseif( $what == 'by_farm_id' ) {
			$sus_query = "SELECT * FROM `supplies` WHERE is_delivered=0 AND id IN ".
				"(SELECT farm_id FROM `harvesting_assignments` WHERE is_finished=0 AND farm_id=" . $param . ")";
			$sus = DB::select( DB::raw($sus_query) );
		} 

		$has = array(); 	// // Harvesting assingments...
		if( $what == 'all' ) {
			$has_query = "SELECT * FROM `harvesting_assignments` WHERE is_finished=0 ".
				"OR id IN (SELECT ha.id FROM `harvesting_assignments` as ha, `supplies` as su WHERE su.is_delivered=0 AND ha.supply_id=su.id)";		
			$has = DB::select( DB::raw($has_query) );
		} elseif( $what == 'by_supply_id' ) {
			$has_query = "SELECT * FROM `harvesting_assignments` WHERE supply_id=".$param;		
			$has = DB::select( DB::raw($has_query) );
		} elseif( $what == 'by_ca_id' ) {
			$has_query = "SELECT * FROM `harvesting_assignments` WHERE cultivation_assignment_id=" . $param;
			$has = DB::select( DB::raw($has_query) );
		} elseif( $what == 'by_ha_id' ) {
			$has_query = "SELECT * FROM `harvesting_assignments` WHERE id=" . $param;
			$has = DB::select( DB::raw($has_query) );
		} elseif( $what == 'by_farm_id' ) {
			$has_query = "SELECT * FROM `harvesting_assignments` WHERE is_finished=0 AND farm_id=" . $param;
			$has = DB::select( DB::raw($has_query) );
		} 		

		// Loading cultivations assignments...
		$cas = array();
		if( $what == 'all' ) {
			$cas_query = "SELECT * FROM `cultivation_assignments` WHERE is_finished=0 ".
				"OR id IN (SELECT cultivation_assignment_id FROM `harvesting_assignments` WHERE is_finished=0) ".
				"OR id IN (SELECT cultivation_assignment_id FROM `harvesting_assignments` WHERE id IN (SELECT ha.id FROM `harvesting_assignments` as ha, `supplies` as su WHERE su.is_delivered=0 AND ha.supply_id=su.id))";		
			$cas = DB::select( DB::raw($cas_query) );
		} elseif( $what == 'by_supply_id' ) {
			$cas_query = "SELECT * FROM `cultivation_assignments` WHERE ".
				"id IN (SELECT cultivation_assignment_id FROM `harvesting_assignments` WHERE supply_id=".$param.")";		
			$cas = DB::select( DB::raw($cas_query) );
		} elseif( $what == 'by_ca_id' ) {
			$cas_query = "SELECT * FROM `cultivation_assignments` WHERE id=" . $param;
			$cas = DB::select( DB::raw($cas_query) );
		} elseif( $what == 'by_ha_id' ) {
			$cas_query = "SELECT * FROM cultivation_assignments WHERE id IN ".
				"(SELECT cultivation_assignment_id FROM `harvesting_assignments` WHERE id=". $param . ")";		
			$cas = DB::select( DB::raw($cas_query) );						
		} elseif( $what == 'by_farm_id' ) {
			$cas_query = "SELECT * FROM cultivation_assignments WHERE is_finished=0 OR ".
				"id IN (SELECT cultivation_assignment_id FROM `harvesting_assignments` WHERE is_finished=0 AND farm_id=". $param . ")";		
			$cas = DB::select( DB::raw($cas_query) );						
		}		

		if( sizeof($cas) == 0 ) {
			return view('workflow.aindex')->with('ganttData', $cas_query);
		}
			
        $ganttTitle = ''; // 'The Workflow';
		
		// Loading farms...
		$farm_ids = array();	
		for( $i = 0 ; $i < sizeof($cas) ; $i++ ) {
			array_push( $farm_ids, $cas[$i]->farm_id );
		}
		$farms_query = "SELECT id, title, square FROM `farms` WHERE id IN (" . implode( ",", $farm_ids ) . ")";		
		$farms = DB::select( DB::raw($farms_query) );
		if( sizeof($farms) == 0 ) { 	// If no farms loaded - returning null...
			return view('workflow.aindex')->with('ganttData', '');
		}		

		// Loading operations for each cultivation assignment...
		for( $i = 0 ; $i < sizeof($cas) ; $i++ ) {
			$ops_query = "SELECT * FROM `operations` WHERE cultivation_assignment_id=" . $cas[$i]->id;
			$ops = DB::select( DB::raw($ops_query) );
			$ops_array = array();
			if( sizeof($ops) > 0 ) {
				for( $iO = 0 ; $iO < sizeof($ops) ; $iO++ ) {
					array_push( $ops_array, $ops[$iO] );
				}
			}
			$cas[$i]->operations = $ops_array;	
		}
	
		$crop_ids = array();	
		for( $i = 0 ; $i < sizeof($cas) ; $i++ ) {
			array_push( $crop_ids, $cas[$i]->crop_id );
		}
		$crops_query = "SELECT id, title FROM `crops` WHERE id IN (" . implode( ",", $crop_ids ) . ")";	
		$crops = DB::select( DB::raw($crops_query) );
		if( sizeof($crops) == 0 ) { 
			return view('workflow.aindex')->with('ganttData', '');
		}

		$ganttData = array( 'title'=>$ganttTitle, 'crops'=>$crops, 'farms'=>$farms, 
			'supplies'=>$sus, 'harvesting_assignments'=>$has, 'cultivation_assignments'=>$cas );
		return view('workflow.index')->with('ganttData', $ganttData);
	}


	public function aindex($what) {
		$this->aindexParam($what, NULL);
	} 

	public function aindexParam($what, $param=NULL) 
	{
		if( !confirmAdminPrivilegies() ) {
			return;
		}

		// Loading cultivation assignments...
		$cas = array();	
		if( $what == 'by_ca_id' ) {
			$cas_query = "SELECT * FROM cultivation_assignments WHERE id=" . $param . " LIMIT 1";		
			$cas = DB::select( DB::raw($cas_query) );
		} elseif( $what == 'by_ha_id' ) {
			$cas_query = "SELECT * FROM cultivation_assignments WHERE id IN ".
				"(SELECT cultivation_assignment_id FROM `harvesting_assignments` WHERE id=". $param . ")";		
			$cas = DB::select( DB::raw($cas_query) );						
		} elseif( $what == 'by_farm_id' ) {
			$cas_query = "SELECT * FROM cultivation_assignments WHERE farm_id=" . $param . " AND (is_finished=0 OR ".
			"id IN (SELECT cultivation_assignment_id FROM `harvesting_assignments` WHERE is_finished=0) OR ".
			"id IN (SELECT cultivation_assignment_id FROM `harvesting_assignments` WHERE id IN (SELECT ha.id FROM `harvesting_assignments` as ha, `supplies` as su WHERE su.is_delivered=0 AND ha.supply_id=su.id)))";		
			$cas = DB::select( DB::raw($cas_query) );						
		} elseif( $what == 'by_supply_id' ) {
			$cas_query = "SELECT * FROM cultivation_assignments WHERE id IN ".
				"(SELECT cultivation_assignment_id FROM `harvesting_assignments` WHERE supply_id=". $param . ")";		
			$cas = DB::select( DB::raw($cas_query) );						
		} elseif( $what == 'undelivered' ) {
			$cas_query = "SELECT * FROM cultivation_assignments WHERE (is_finished=0 OR ".
			"id IN (SELECT cultivation_assignment_id FROM `harvesting_assignments` WHERE is_finished=0) OR ".
			"id IN (SELECT cultivation_assignment_id FROM `harvesting_assignments` WHERE id IN (SELECT ha.id FROM `harvesting_assignments` as ha, `supplies` as su WHERE su.is_delivered=0 AND ha.supply_id=su.id)))";		
			$cas = DB::select( DB::raw($cas_query) );						
		}
		if( sizeof($cas) == 0 ) {
			return view('workflow.aindex')->with('ganttData','');
		}
		
		$ganttTitle = '';
		if( $what == 'by_ca_id' ) {
			if( sizeof($cas) == 1 ) {
				$ganttTitle = 'Cultivation Assignment: ' .  $cas[0]->title . ',  ' . $cas[0]->finish_by_plan;
			} else {
				$ganttTitle = 'Cultivation Assignments (' .  sizeof($cas) . ')';
			}		
		} elseif( $what == 'by_supply_id' ) {
			$title_query = "SELECT title, descr FROM `supplies` WHERE id=" . $param . " LIMIT 1";
			$title = DB::select( DB::raw($title_query) );
			$ganttTitle = $title[0]->title . " / " . $title[0]->descr;			
		} 
		
		// Loading farms...
		$farm_ids = array();	
		for( $i = 0 ; $i < sizeof($cas) ; $i++ ) {
			array_push( $farm_ids, $cas[$i]->farm_id );
		}
		$farms_query = "SELECT id, title, square FROM `farms` WHERE id IN (" . implode( ",", $farm_ids ) . ")";		
		$farms = DB::select( DB::raw($farms_query) );
		if( sizeof($farms) == 0 ) {
			return view('workflow.aindex')->with('ganttData', '');
		}

		// Loading operations for each cultivation assignment...
		for( $i = 0 ; $i < sizeof($cas) ; $i++ ) {
			$ops_query = "SELECT * FROM `operations` WHERE cultivation_assignment_id=" . $cas[$i]->id;
			$ops = DB::select( DB::raw($ops_query) );
			if( sizeof($ops) > 0 ) {
				$ops_array = array();
				for( $iO = 0 ; $iO < sizeof($ops) ; $iO++ ) {
					array_push( $ops_array, $ops[$iO] );
				}
				$cas[$i]->operations = $ops_array;	
			}
		}
	
		$crop_ids = array();	
		for( $i = 0 ; $i < sizeof($cas) ; $i++ ) {
			array_push( $crop_ids, $cas[$i]->crop_id );
		}
		$crops_query = "SELECT id, title FROM `crops` WHERE id IN (" . implode( ",", $crop_ids ) . ")";	
		$crops = DB::select( DB::raw($crops_query) );
		if( sizeof($crops) == 0 ) { 
			return view('workflow.aindex')->with('ganttData', '');
		}
		
		$ganttData = array( 'title'=>$ganttTitle, 'crops'=>$crops, 'farms'=>$farms, 'cultivation_assignments'=>$cas );
//echo($ganttData['title']);
//echo($ganttData['crops']);
//echo($ganttData['farms']);
//echo($ganttData['cultivation_assignments']);
		return view('workflow.aindex')->with('ganttData', $ganttData);
	}
}
