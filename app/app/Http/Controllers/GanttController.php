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


class GanttController extends Controller
{
	public function index($what, $param) 
	{
		if( !confirmAdminPrivilegies() ) {
			return;
		}

		// Loading cultivation assignments...
		if( $what == 'by_ca_id' ) {
			$cas_query = "SELECT * FROM cultivation_assignments WHERE id=" . $param . " LIMIT 1";		
			$cas = DB::select( DB::raw($cas_query) );
		} elseif( $what == 'by_farm' ) {
			$cas_query = "SELECT * FROM cultivation_assignments WHERE farm_id=" . $param . " AND (is_finished=0 OR ".
			"id IN (SELECT cultivation_asisgnment_id FROM `harvesting_assignments` WHERE is_finished=0) OR ".
			"id IN (SELECT cultivation_assignment_id FROM `harvesting_assignments` WHERE id IN (SELECT harvesting_assignment_id FROM `supplies` WHERE is_delivered=0)))";		
			$cas = DB::select( DB::raw($cas_query) );						
		} elseif( $what == 'by_supply' ) {
			$cas_query = "SELECT * FROM cultivation_assignments WHERE id IN ".
				"(SELECT cultivation_assignment_id FROM `harvesting_assignments` WHERE supply_id=". $param . ")";		
			$cas = DB::select( DB::raw($cas_query) );						
		} elseif( $what == 'undelivered' ) {
			$cas_query = "SELECT * FROM cultivation_assignments WHERE (is_finished=0 OR ".
			"id IN (SELECT cultivation_asisgnment_id FROM `harvesting_assignments` WHERE is_finished=0) OR ".
			"id IN (SELECT cultivation_assignment_id FROM `harvesting_assignments` WHERE id IN (SELECT harvesting_assignment_id FROM `supplies` WHERE is_delivered=0)))";		
			$cas = DB::select( DB::raw($cas_query) );						
		}
		if( sizeof($cas) == 0 ) {
			return view('workflow.aindex')->with('ganttData', 'null');
		}
		
		$ganttTitle = '';
		if( sizeof($cas) == 1 ) {
			$ganttTitle = 'Cultivation Assignment: ' .  $cas[0]->title . ',  ' . $cas[0]->finish_by_plan;
		} else {
			$ganttTitle = 'Cultivation Assignments (' .  sizeof($cas) . ')';
		}
		
		// Loading farms...
		$farm_ids = array();	
		for( $i = 0 ; $i < sizeof($cas) ; $i++ ) {
			array_push( $farm_ids, $cas[$i]->farm_id );
		}
		$farms_query = "SELECT id, title, square FROM `farms` WHERE id IN (" . implode( ",", $farm_ids ) . ")";		
		$farms = DB::select( DB::raw($farms_query) );
		if( sizeof($farms) == 0 ) {
			return view('workflow.aindex')->with('ganttData', 'null');
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
			return view('workflow.aindex')->with('ganttData', 'null');
		}
		
		$ganttData = array( 'title'=>$ganttTitle, 'crops'=>$crops, 'farms'=>$farms, 'cultivation_assignments'=>$cas );
		return view('workflow.aindex')->with('ganttData', $ganttData);
	}
}
