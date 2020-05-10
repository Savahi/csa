<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;
use DB;

class SupplyController extends Controller
{
    //
	public function index() {

		$query = "SELECT id, title, descr, icon, DATE_FORMAT(`deliver_to`, '%Y-%m-%d %H:%i') AS `deliver_to` FROM `supplies` WHERE is_delivered=0 ORDER BY deliver_to";
		$supplies = DB::select( DB::raw( $query ) );

		$query = "SELECT id, title, descr, icon, DATE_FORMAT(`deliver_to`, '%Y-%m-%d %H:%i') AS `deliver_to` FROM `supplies` WHERE is_delivered=1 ORDER BY deliver_to DESC LIMIT 50";
		$supplies_delivered = DB::select( DB::raw( $query ) );

		return view( 'supply.index', compact('supplies', 'supplies_delivered') );

		//$supplies = DB::table('supplies')->orderBy('deliver_to')->get();
		//$harvesting_reservations = DB::table('harvesting_reservations')->where('supply_id',$id)->get();
		//return view( 'supply.index', compact('supplies') );
	}

	public function show($id) {
		//$supply = DB::table('supplies')->find($id);

		$query = "SELECT id, title, descr, icon, DATE_FORMAT(`deliver_to`, '%Y-%m-%d %H:%i') AS `deliver_to` FROM `supplies` WHERE id={$id} LIMIT 1";
		$supply_array = DB::select( DB::raw( $query ) );
		if( sizeof($supply_array) < 1 ) {
			return	redirect("/");
		}
		$supply = $supply_array[0];
		
		$supply_details_query = "SELECT cr.id as crop_id, cr.title as crop_title, cr.descr as crop_descr, ".
			"ha.farm_id as farm_id, fa.title as farm_title, ".
			"ha.amount_prognosed as amount_prognosed, DATE_FORMAT(ha.finish_prognosed,'%Y-%m-%d') AS `finish_prognosed` ".
			"FROM `harvesting_assignments` as ha, `crops` as cr, `farms` as fa WHERE ha.supply_id=".$id." AND ha.crop_id=cr.id AND ha.farm_id=fa.id";
		$supply_details = DB::select( DB::raw($supply_details_query) );

		return view('supply.show', compact('supply','supply_details'));
	}
}
