<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use DB;

class PageController extends Controller
{
    //
	public function index() {
		$slides = DB::table('slides')->get();
		$persons = DB::table('persons')->get();
		$links = DB::table('links')->get();
		return view( 'page.index', compact('slides','persons','links') );
	}

	public function participation() {
		return view( 'page.participation' );
	}

	public function faq() {
		$faq = DB::table('faq')->get();
		return view( 'page.faq', compact('faq') );
	}
}
