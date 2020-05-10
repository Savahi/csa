<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use DB;

class CropController extends Controller
{
    //
	public function index() {
		//$crops = DB::table('crops')->get();
		//$harvesting_reservations = DB::table('harvesting_reservations')->where('supply_id',$id)->get();
		//return view( 'crop.index', compact('crops') );

		$q = "SELECT cr.id AS id, cr.title AS title, cr.descr AS descr, cr.icon AS icon, COUNT(ca.crop_id) AS count FROM `crops` AS cr".
			" LEFT JOIN `cultivation_assignments` AS ca ON cr.id=ca.crop_id GROUP BY cr.id ORDER BY COUNT(ca.crop_id) DESC";		
		$crops = DB::select( DB::raw($q) );
		return view( 'crop.index', compact('crops') );
	}

	public function show($id) {
		$crop = DB::table('crops')->find($id);

		return view('crop.show', compact('crop'));
	}
}
