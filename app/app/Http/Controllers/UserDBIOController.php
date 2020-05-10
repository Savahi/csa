<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use DB;
use Auth;
use App\MyHelpers;

function confirmAuth() {
	if ( Auth::check() ) {
		return true;
	}
	echo( json_encode( array('rows_affected'=>0, 'error_message'=>'Authorization error') ) );
	die();
}


function confirmDPAdmin() {
	if ( Auth::check() ) {
		if( Auth::user()->delivery_point_admin > 0 ) {
			return true;
		}
	}
	echo( json_encode( array('rows_affected'=>0, 'error_message'=>'Authorization error') ) );
	die();
}


class UserDBIOController extends Controller
{

	public function update(Request $request) 
	{
		confirmAuth();
		$key_value_pairs = [ 'name' => $request->name, 'contacts' => $request->contacts ];

		if( $request->hasFile('icon') ) {
			$mh = new MyHelpers();
			$key_value_pairs['icon'] = $mh->resizeImageAndEncode($request);
		} else if( $request->icon_delete ) {
			$key_value_pairs['icon'] = null;	
		}

		$updated = DB::table('users')->where('id', Auth::user()->id)->limit(1)->update( $key_value_pairs );
		if( $updated ) {
			return json_encode( array('rows_affected'=>1, 'name'=>$request->name, 'contacts'=>$request->contacts, 'error_message'=>'') );
		} 	
		return json_encode( array('rows_affected'=>0, 'error_message'=>'') );
	}

	public function updateDeliveryPoint(Request $request) 
	{
		confirmDPAdmin();
		$key_value_pairs = [ 'title' => $request->title, 'address' => $request->address, 'descr' => $request->descr, 
			'latitude' => $request->latitude, 'longitude' => $request->longitude, 
			'pickup_info' => $request->pickup_info, 'delivery_info' => $request->delivery_info ];
		$updated = DB::table('delivery_points')->where('id', Auth::user()->delivery_point_admin)->limit(1)->update( $key_value_pairs );
		if( $updated ) {
			return json_encode( array('rows_affected'=>1, 'error_message'=>'') );
		} 	
		return json_encode( array('rows_affected'=>0, 'error_message'=>'') );
	}
}
