<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use DB;

class SupplySubst {
	public $icon = '';
	public $title = "This is not a Supply, but Simply a \"Plug\"";
	public $desc = "No description yet...";
	public $deliver_to = "";
	public $id = "";		
}

class PublicationSubst {
	public $icon = '';
	public $title = "This is not a Publication, but Simply a \"Plug\"";
	public $desc = "No description yet...";
	public $id = "";		
}

class PluginController extends Controller
{
    //
	public function main() {
		$query = "SELECT id, title, descr, icon, DATE_FORMAT(`deliver_to`, '%Y-%m-%d %H:%i') AS `deliver_to`  FROM `supplies` WHERE DATEDIFF( deliver_to, NOW() ) > 0 ORDER BY DATEDIFF( deliver_to, NOW() ) LIMIT 2";
		$supplies = DB::select( DB::raw( $query ) );	
		/*
		if( sizeof($supplies) < 2 ) {
			array_push( $supplies, new SupplySubst() );
		}
		if( sizeof($supplies) < 2 ) {
			array_push( $supplies, new SupplySubst() );
		}
		if( !( strlen($supplies[0]->icon) > 0 ) ) {
			$supplies[0]->icon = Config::get('myconstants.emptyIcon');			
		}
		if( !( strlen($supplies[1]->icon) > 0 ) ) {
			$supplies[1]->icon = Config::get('myconstants.emptyIcon');			
		}
		*/
		$query = "SELECT id, title, descr, icon, text, DATE_FORMAT(`created_at`, '%Y-%m-%d %H:%i') AS `created_at` FROM `publications` ORDER BY created_at LIMIT 4";
		$publications = DB::select( DB::raw( $query ) );
		/*
		if( sizeof($publications) < 2 ) {
			array_push( $publications, new PublicationSubst() );
		}
		if( sizeof($publications) < 2 ) {
			array_push( $publications, new PublicationSubst() );
		}
		if( !( strlen($publications[0]->icon) > 0 ) ) {
			$publications[0]->icon = Config::get('myconstants.emptyIcon');			
		}
		if( !( strlen($publications[1]->icon) > 0 ) ) {
			$publications[1]->icon = Config::get('myconstants.emptyIcon');			
		}
		*/
		return view( 'plugin.main', compact('supplies','publications') );
	}

}
