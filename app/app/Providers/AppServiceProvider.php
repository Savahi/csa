<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use DB;
use View;
use App;
use Cookie;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

if (App::runningInConsole() ) {
     return;
}    

	// Reading language and texts
	$texts = null;
	$lang = Cookie::get('lang');
	if( $lang == 'RU' )	{
	    $texts = DB::table('texts_ru')->get();
	} elseif( $lang == 'EN' ) {
	    $texts = DB::table('texts_en')->get();
	} else {
	    $texts = DB::table('texts_ru')->get();
		$lang = 'RU';
	}				
	
	$hashed_texts = array();
	$hashed_texts['lang'] = $lang;

	foreach( $texts as $text ) {
		if( strlen($text->descr) > 0 ) {
			$text->descr = nl2br($text->descr); //preg_replace("/\r\n|\r|\n/", "<br/>", $text->descr);
		}
		$hashed_texts[$text->code] = $text;		
	}

    // Sharing...
    View::share('texts', $texts);
    View::share('htexts', $hashed_texts);
    }
}
