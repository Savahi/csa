<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Redirect;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // A "NO email verification" code: 
		// $this->middleware('auth');

		// A "WITH email verification" code:
		$this->middleware(['auth', 'verified']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function index()
    {
		//if( !\Auth::user()->email_verified_at ) {
		//	return view('tasks.index', compact('tasks'));
		//}
		if( \Auth::user()->admin_privilegies > 0 ) {
			$q0 = "SELECT COUNT(id) as users_with_nonzero_deposit, SUM(deposit) as total_deposit FROM `users` WHERE deposit > 0";
			$q1 = "SELECT COUNT(id) as users_with_nonzero_balance, SUM(balance) as total_balance FROM `users` WHERE balance > 0";
			$q2 = "SELECT COUNT(id) as users_attached_to_delivery_point FROM `users` WHERE delivery_point_id!=-1";
			$q3 = "SELECT COUNT(id) as users_registered FROM `users` WHERE (farm_admin<0) AND (delivery_unit_admin<0) AND (sorting_station_admin<0) AND (admin_privilegies=0)";
			$q4 = "SELECT COUNT(id) as farms_count FROM `farms`";
			$q5 = "SELECT COUNT(id) as cultivation_assignments_count FROM `cultivation_assignments` WHERE is_finished=0";
			$q6 = "SELECT COUNT(id) as delivery_points_count FROM `delivery_points`";
			$q = sprintf("SELECT * FROM ((%s) AS q0 JOIN(%s) AS q1 JOIN (%s) AS q2 ON 1 JOIN (%s) AS q3 ON 1 JOIN (%s) AS q4 ON 1 JOIN (%s) AS q5 ON 1 JOIN (%s) AS q6 ON 1)", 
				$q0, $q1, $q2, $q3, $q4, $q5, $q6 );
			$users = DB::select( DB::raw($q) );
	        return view('user', 
				['users_attached_to_delivery_point'=>$users[0]->users_attached_to_delivery_point, 
				'users_with_nonzero_deposit'=>$users[0]->users_with_nonzero_deposit,
				'total_deposit'=>$users[0]->total_deposit, 
				'users_with_nonzero_balance'=>$users[0]->users_with_nonzero_balance,
				'total_balance'=>$users[0]->total_balance, 
				'users_registered'=>$users[0]->users_registered, 
				'farms_count'=>$users[0]->farms_count, 'cultivation_assignments_count'=>$users[0]->cultivation_assignments_count,
				'delivery_points_count'=>$users[0]->delivery_points_count ] );
		}
        return view('user');
    }

	public function resetPassword() {
		\Auth::logout();
		return Redirect::to('/password/reset');
	}
}
