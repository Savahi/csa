<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
		DB::table('settings')->delete();
        DB::table('settings')->insert(
			[ 'title' => 'Language', 'code' => 'language', 'value' => 'en'],
			[ 'title' => 'Currency', 'code' => 'currency', 'value' => 'rur'],
			[ 'title' => 'User Can Cancel Supply Not Later Than (Hours)', 'code'=>'supply_cancelling_hours', 'value'=>'96'],
			[ 'title' => 'Debeting For Supply Allowed Not Sooner Than (Hours)', 'code'=>'supply_debeting_hours', 'value'=>'95']
		);

		DB::table('crops')->delete();
		$this->seedTableFromFile('crops', 'csv/crops.csv');

		DB::table('crop_groups')->delete();
		$this->seedTableFromFile('crop_groups', 'csv/crop_groups.csv');

		DB::table('image_gallery_albums')->delete();
        DB::table('image_gallery_albums')->insert(
			[   "title" => "Публикации", 'descr' => 'Фото для раздела "Publications"' ]
		);
        DB::table('image_gallery_albums')->insert(
			[   "title" => "Культуры", 'descr' => 'Фото для раздела "Crops"' ]
		);

		DB::table('publications')->delete();
		$this->seedTableFromFile('publications', 'csv/publications.csv');

		DB::table('persons')->delete();
		$this->seedTableFromFile('persons', 'csv/persons.csv');

		DB::table('faq')->delete();
		$this->seedTableFromFile('faq', 'csv/faq.csv');

		DB::table('slides')->delete();
		$this->seedTableFromFile('slides', 'csv/slides.csv');

		DB::table('texts')->delete();
		$this->seedTableFromFile('texts', 'csv/texts.csv');

		DB::table('links')->delete();
		$this->seedTableFromFile('links', 'csv/links.csv');

		DB::table('farms')->delete();
		$this->seedTableFromFile('farms', 'csv/farms.csv');

		DB::table('supplies')->delete();
		$this->seedTableFromFile('supplies', 'csv/supplies.csv');

		DB::table('cultivation_assignments')->delete();
		$this->seedTableFromFile('cultivation_assignments', 'csv/cultivation_assignments.csv');

		DB::table('harvesting_assignments')->delete();

		/*
		DB::table('supplies')->delete();
		DB::table('supplies')->insert(
			[ "title"=>"Урожай для Клуба #1", "descr"=>"помидоры, тыквы, капуста, укроп",
			"deliver_from"=>"2020-04-07 10:00", "deliver_to"=>"2020-04-07 11:00" ] );
        DB::table('supplies')->insert(
			[ "title"=>"Урожай для Клуба #2", "descr"=>"помидоры, тыквы, капуста, укроп",
			"deliver_from"=>"2020-04-14 10:00", "deliver_to"=>"2020-04-14 11:00" ] );
		*/

		DB::table('locations')->delete();
		$this->seedTableFromFile('locations', 'csv/locations.csv');

		DB::table('users')->delete();
        $this->call(UserTableSeeder::class);
		$this->seedTableFromFile('users', 'csv/users.csv');

		DB::table('delivery_points')->delete();
		$this->seedTableFromFile('delivery_points', 'csv/delivery_points.csv');

		DB::table('delivery_units')->delete();
        DB::table('delivery_units')->insert(
			[ "user_id"=>2, "title"=>"Грузовик", "descr"=>"Some desc!" ]
		);

		DB::table('operations')->delete();
		$this->seedTableFromFile('operations', 'csv/operations.csv');
    }


	public function seedTableFromFile( $tableName, $fileName ) {

		$file = fopen('database/seeds/'.$fileName, "r");
		if( !$file ) {
			return;
		}

		$line = fgets($file);
		if( feof($file) ) {
			return;
		}
		
		$keys = explode("\t", $line);
		$num_keys = sizeof($keys);
		if( $num_keys == 0 ) {
			return;
		}
		$keys[ $num_keys-1 ] = rtrim( $keys[ $num_keys-1 ] );

		while( !feof($file) ) {
			$line = fgets( $file );
			$line = rtrim($line);
	
			$fields = explode("\t", $line);
			//if( sizeof($fields) < $num_keys ) {
			//	continue;
			//}
		
			$key_value_pairs = [];
			for( $i = 0 ; $i < sizeof($fields) /*$num_keys*/ ; $i++ ) {
				if( $keys[$i] == 'icon' || $keys[$i] == 'image' ) {
					if( strlen($fields[$i]) > 0 ) {				
						$key_value_pairs[ 'icon' ] = $this->resizeImageAndEncode( 'database/seeds/'.$tableName, $fields[$i] );							
					}
				} else {
					if( $keys[$i] == 'id' ) {				
						$key_value_pairs[ $keys[$i] ] = (int)$fields[$i];
					} else  {		
						$key_value_pairs[ $keys[$i] ] = $fields[$i];		
					}
				}
				//echo( $i . "/" . $num_keys . ": " . $keys[$i] . " : " . $fields[$i] . "\n");
			}
			DB::table($tableName)->insert( $key_value_pairs );
		}
		fclose($file);
	}


	function resizeImageAndEncode( $dir, $fileName ) {

		// Validating the extension...
		$validExt = array( 'jpg', 'jpeg', 'png', 'gif' );
		$fileExt = pathinfo( $fileName, PATHINFO_EXTENSION ); // getting ext. from $fileName
		if( !in_array( $fileExt, $validExt ) ) {
			return null;
		}
		$fileName = $dir . "/" . $fileName;
	
		// Getting size and dimensions
		$original_info = getimagesize( $fileName );
		if( $original_info == 0 ) {
			return null;
		}
		$original_w = $original_info[0];
		$original_h = $original_info[1];
		$original_img = null;	
		if ($fileExt == 'jpeg' || $fileExt == 'jpg') {
    		$original_img = imagecreatefromjpeg($fileName);
		} elseif ($fileExt == 'png') {
    		$original_img = imagecreatefrompng($fileName);
		} elseif ($fileExt == 'gif') {
    		$original_img = imagecreatefromgif($fileName);
		}
		if( $original_img == null ) {
			return null;
		}

		$thumb_h = 200;
		$thumb_w = $thumb_h * $original_w / $original_h;
		$thumb_img = imagecreatetruecolor($thumb_w, $thumb_h);
		imagecopyresampled( $thumb_img, $original_img, 0, 0, 0, 0, $thumb_w, $thumb_h, $original_w, $original_h );

		ob_start();
		imagejpeg($thumb_img);
		$buffer = ob_get_clean();

		$buffer = base64_encode($buffer);
		return $buffer;
	}
}
