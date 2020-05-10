<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;
use DB;

class DeliveryPointController extends Controller
{
    //
	public function index() {
		$dps = DB::table('delivery_points')->get();
		//$harvesting_reservations = DB::table('harvesting_reservations')->where('supply_id',$id)->get();
		return view( 'delivery_point.index', compact('dps') );
	}

	public function show($id) {
		$dp = DB::table('delivery_points')->find($id);
		return view('delivery_point.show', compact('dp'));
	}
}
