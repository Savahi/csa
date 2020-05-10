<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use DB;
use Auth;
use App\MyHelpers;


function confirmAdminPrivilegies() {
	if ( Auth::check() ) {
		if( Auth::user()->admin_privilegies > 1 ) {
			return true;
		}
	}
	echo( json_encode( array('rows_affected'=>0, 'error_message'=>'Authorization error') ) );
	die();
}


class EditorDBIOController extends Controller
{

	private function resizeImage( $request ) {
		$mh = new MyHelpers();
		return $mh->resizeImageAndEncode($request);
	}

    //
	public function newPublication(Request $request) 
	{
		confirmAdminPrivilegies();

		$key_value_pairs = [ 'title'=>$request->title, 'descr'=>$request->descr, 'text'=>$request->text, 
			'is_hidden'=> ($request->is_hidden == 'false') ? 0 : 1, 'created_at'=>$request->created_at ];
		if( $request->hasFile('icon') ) {
			$key_value_pairs['icon'] = $this->resizeImage($request);
		}  
		$status = DB::table('publications')->insert( $key_value_pairs );
		$r = ($status) ? 1 : 0;
		return json_encode( array('rows_affected'=>$r, 'error_message'=>'') );
	}


	public function updatePublication(Request $request) 
	{
		confirmAdminPrivilegies();

		$key_value_pairs = [ 'title'=>$request->title, 'descr'=>$request->descr, 'text'=>$request->text, 
			'is_hidden'=>($request->is_hidden == 'false') ? 0 : 1, 'created_at'=>$request->created_at ];
		if( $request->hasFile('icon') ) {
			$key_value_pairs['icon'] = $this->resizeImage($request);
		} else if( $request->icon_delete ) {
			$key_value_pairs['icon'] = null;	
		}
		$status = DB::table('publications')->where('id', $request->id)->limit(1)->update( $key_value_pairs );
		return json_encode( array('rows_affected'=>1, 'error_message'=>'') );
	}


	public function deletePublication(Request $request) 
	{
		confirmAdminPrivilegies();

		$query = "DELETE FROM `publications` WHERE id=" . $request->id . " LIMIT 1";
		$delete = DB::delete( DB::raw($query) );
		return json_encode( array('rows_affected'=>1, 'error_message'=>'') );
	}

}
