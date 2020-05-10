<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use DB;

class PublicationController extends Controller
{
    //
	public function index() {
		$query = "SELECT id, title, descr, icon, text, DATE_FORMAT(`created_at`, '%Y-%m-%d %H:%i') AS `created_at` FROM `publications` WHERE is_hidden=0 ORDER BY created_at";
		$publications = DB::select( DB::raw( $query ) );
		foreach( $publications as $p ) {
			if( strlen( $p->descr ) > 0 ) {
				$p->descr = preg_replace("/\r\n|\r|\n/",'<br/>', $p->descr);
			}
		}		
		return view( 'publication.index', compact('publications') );
	}

	public function show($id) {
		$query = "SELECT id, title, descr, icon, text, DATE_FORMAT(`created_at`, '%Y-%m-%d %H:%i') AS `created_at` FROM `publications` WHERE id=" . $id . " LIMIT 1";
		$publications = DB::select( DB::raw( $query ) );
		if( sizeof($publications) == 1 ) {
			$publication = $publications[0];			
			if( strlen( $publication->descr ) > 0 ) {
				$publication->descr = preg_replace("/\r\n|\r|\n/",'<br/>', $publication->descr);
			}
			return view('publication.show', compact('publication'));
		}
	}
}
