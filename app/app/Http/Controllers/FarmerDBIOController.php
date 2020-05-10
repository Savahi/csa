<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use DB;
use Auth;

function confirmAdminPrivilegies() {
	if ( Auth::check() ) {
		if( Auth::user()->farm_admin > 0 ) {
			return true;
		}
	}
	//response()->json( ['rows_affected'=>0, 'error_message'=>'Authorization error'] )->send();
	echo( json_encode( array('rows_affected'=>0, 'error_message'=>'Authorization error') ) );
	die();
}

class FarmerDBIOController extends Controller
{

	public function updateCultivationAssignment(Request $request) 
	{
		confirmAdminPrivilegies();
		$key_value_pairs = [ 'is_accepted'=>1 ];
		$key_value_pairs['amount_prognosed'] = $request->amount_prognosed;
		$key_value_pairs['amount_actual'] = $request->amount_actual;
		$key_value_pairs['start_prognosed'] = $request->start_prognosed;
		$key_value_pairs['start_actual'] = $request->start_actual;
		$key_value_pairs['finish_prognosed'] = $request->finish_prognosed;
		$key_value_pairs['finish_actual'] = $request->finish_actual;
		if( property_exists( 'request', 'work_time' ) ) {
			if( strlen($request['work_time']) > 0 ) {
				$key_value_pairs['work_time'] = $request->work_time;
			}
		}
		if( strlen( $key_value_pairs['amount_actual'] ) > 0 &&  strlen( $key_value_pairs['finish_actual'] ) > 0 ) {
			$key_value_pairs['is_finished'] = 1;
		}
		if( strlen( $key_value_pairs['amount_actual'] ) > 0 ) {
			$key_value_pairs['amount_prognosed'] = $key_value_pairs['amount_actual'];
		}
		if( strlen( $key_value_pairs['start_actual'] ) > 0 ) {
			$key_value_pairs['start_prognosed'] = $key_value_pairs['start_prognosed'];
		}
		if( strlen( $key_value_pairs['finish_actual'] ) > 0 ) {
			$key_value_pairs['finish_prognosed'] = $key_value_pairs['finish_prognosed'];
		}
		$status = DB::table('cultivation_assignments')->where('id', $request->id)->limit(1)->update( $key_value_pairs );
		return json_encode( array('rows_affected'=>$status, 'error_message'=>'') );
	}


	public function updateOperation(Request $request) 
	{
		confirmAdminPrivilegies();
		$key_value_pairs = [ 'title'=>$request->title, 'descr'=>$request->descr, 
			'start_prognosed'=>$request->start_prognosed, 'finish_prognosed'=>$request->finish_prognosed,
			'start_actual'=>$request->start_actual, 'finish_actual'=>$request->finish_actual ];
		$status = DB::table('operations')->where('id', $request->id)->limit(1)->update( $key_value_pairs );
		return json_encode( array('rows_affected'=>1, 'error_message'=>'') );
	}


	public function updateHarvestingAssignment( Request $request ) {
		confirmAdminPrivilegies();

		$key_value_pairs = [ 'is_accepted'=>1 ];
		$key_value_pairs['amount_prognosed'] = $request->amount_prognosed;
		$key_value_pairs['amount_actual'] = $request->amount_actual;
		$key_value_pairs['start_prognosed'] = $request->start_prognosed;
		$key_value_pairs['start_actual'] = $request->start_actual;
		$key_value_pairs['finish_prognosed'] = $request->finish_prognosed;
		$key_value_pairs['finish_actual'] = $request->finish_actual;
		if( property_exists( 'request', 'work_time' ) ) {
			if( strlen($request['work_time']) > 0 ) {
				$key_value_pairs['work_time'] = $request->work_time;
			}
		}
		if( strlen( $key_value_pairs['amount_actual'] ) > 0 &&  strlen( $key_value_pairs['finish_actual'] ) > 0 ) {
			$key_value_pairs['is_finished'] = 1;
		}
		if( strlen( $key_value_pairs['amount_actual'] ) > 0 ) {
			$key_value_pairs['amount_prognosed'] = $key_value_pairs['amount_actual'];
		}
		if( strlen( $key_value_pairs['start_actual'] ) > 0 ) {
			$key_value_pairs['start_prognosed'] = $key_value_pairs['start_prognosed'];
		}
		if( strlen( $key_value_pairs['finish_actual'] ) > 0 ) {
			$key_value_pairs['finish_prognosed'] = $key_value_pairs['finish_prognosed'];
		}

		$status = DB::table('harvesting_assignments')->where('id', $request->id)->limit(1)->update( $key_value_pairs );
		return json_encode( array('rows_affected'=>$status, 'error_message'=>'') );
	}
}
