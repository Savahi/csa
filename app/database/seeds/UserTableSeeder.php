<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\User;

class UserTableSeeder extends Seeder {

	public function run()
	{
		User::create( [ 'name' => 'Admin', 'email' => 'zharfarm@yandex.ru', 
						'admin_privilegies' => 255, 'password' => Hash::make('admin'), 'contacts'=>'phone: +XXX 123 45 67' ] );

		User::create( [ 'name' => 'Farmer #1', 'email' => 'farmer1@icsa.farm', 
						'farm_admin' => 1, 'password' => Hash::make('farmer1'), 'contacts'=>'phone: +XXX 987 65 43' ] );

		User::create( [ 'name' => 'Farmer #2', 'email' => 'farmer2@icsa.farm', 
						'farm_admin' => 2, 'password' => Hash::make('farmer2'), 'contacts'=>'phone: +XXX 987 65 43' ] );

		//User::create( [ 'name' => 'User #1 (DPA)', 'email' => 'user1@icsa.farm', 'delivery_point_id' => 1, 
		//				'delivery_point_admin' => 1, 'password' => Hash::make('user1'), 'contacts'=>'phone: +XXX 987 65 43' ] );

		User::create( [ 'name' => 'User #2', 'email' => 'user2@icsa.farm', 'delivery_point_id' => 1,
						'password' => Hash::make('user2'), 'contacts'=>'phone: +XXX 123 45 67' ] );

		User::create( [ 'name' => 'Sorting Man', 'email' => 'sortingman@icsa.farm', 
						'sorting_station_admin' => 1, 'password' => Hash::make('sortingman'), 'contacts'=>'phone: +XXX 987 65 43' ] );

		User::create( [ 'name' => 'Logistician', 'email' => 'logistician@icsa.farm', 
						'delivery_unit_admin' => 1, 'password' => Hash::make('logistician'), 'contacts'=>'phone: +XXX 987 65 43' ] );
	
		//User::create( [ 'name' => 'User #4 (sorting station admin)', 'email' => 'user4@icsa.farm', 
		//				'sorting_station_admin' => 1, 'password' => Hash::make('user') ] );
	}
}