<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;
use DB;

class FarmController extends Controller
{
    //
	public function index() {
		$farms = DB::table('farms')->get();
		//$harvesting_reservations = DB::table('harvesting_reservations')->where('supply_id',$id)->get();
		return view( 'farm.index', compact('farms') );
	}

	public function show($id) {
		$farm = DB::table('farms')->find($id);
		return view('farm.show', compact('farm'));
	}
}
