<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;

class HarvestController extends Controller
{
    //
	public function index() {
		$list = App\Harvest::all();
		return view('harvest.index', compact('list'));
	}

	public function show($id) {
		$item = App\Harvest::find($id);
		return view('harvest.show', compact('item'));
	}

}
