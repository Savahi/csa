<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use DB;
use Auth;


function confirmAdminPrivilegies() {
	if ( Auth::check() ) {
		if( Auth::user()->delivery_unit_admin > 0 ) {
			return true;
		}
	}
	echo( json_encode( array('rows_affected'=>0, 'error_message'=>'Authorization error') ) );
	die();
}


class DeliveryDBIOController extends Controller
{
    //
	public function updateDeliveryStatus(Request $request) 
	{
		confirmAdminPrivilegies();

		$status = ($request->status == 'delivered' ) ? 1 : 0;
		$problem = ($status == 1) ? ", problem=NULL" : '';
		
		$query = "UPDATE `debetings` SET is_delivered=" . $status . $problem . " WHERE".
			" delivery_point_id=". $request->dp_id . " AND supply_id=" . $request->su_id;

		$updated = DB::update( DB::raw($query) );

		return json_encode( array('rows_affected'=>$updated, 'error_message'=>'') );
	}


	public function updateDeliveryProblem(Request $request) 
	{
		confirmAdminPrivilegies();
		$problem = NULL;
		$is_problem = 0;
		if( strlen( trim($request->problem) ) > 0 ) {
			$problem = $request->problem;
			$is_problem = 1;
		}			

		$query = "UPDATE `debetings` SET problem=\"" . $problem . "\", is_problem=". $is_problem . " WHERE".
			" delivery_point_id=". $request->dp_id . " AND supply_id=" . $request->su_id;

		$updated = DB::update( DB::raw($query) );

		return json_encode( array('rows_affected'=>$updated, 'error_message'=>'') );
	}

}
