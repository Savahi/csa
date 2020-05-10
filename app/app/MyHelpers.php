<?php 
namespace App;
//use Illuminate\Http\Request;

class MyHelpers {

	public function __construct() {
		;
	}

	public function composeImage() {
		$outputW = 240;
		$outputH = 240;
		$outputImage = imagecreatetruecolor($outputW, $outputH);
	
		imagefill($outputImage, 0, 0, imagecolorallocate($outputImage, 255, 255, 255) );

		$sourceImages = array();

		$emptyImageDecoded = base64_decode( Config::get('myconstants.emptyIcon') );
		$sourceImage = imagecreatefromstring($emptyImageDecoded);
	
		array_push($sourceImages, $sourceImage );
		array_push($sourceImages, $sourceImage );
		array_push($sourceImages, $sourceImage );
		array_push($sourceImages, $sourceImage );
	
		$srcW = 200;
		$srcH = 200;	
		$scaler = 0.6;
		$dstW = (int)($srcW * $scaler);
		$dstH = (int)($srcH * $scaler);
		$marginX = (int)($outputW - $dstW);
		$marginY = (int)($outputH - $dstH);

		imagecopyresized($outputImage,$sourceImages[0], 0, 0, 0, 0, $dstW, $dstH, $srcW, $srcH);
		imagecopyresized($outputImage,$sourceImages[1], $marginX, $marginY, 0, 0, $dstW, $dstH, $srcW, $srcH);
		imagecopyresized($outputImage,$sourceImages[2], $marginX, (int)($marginY/4.0), 0, 0, $dstW, $dstH, $srcW, $srcH);
		imagecopyresized($outputImage,$sourceImages[3], (int)($marginX/4.0), $marginY, 0, 0, $dstW, $dstH, $srcW, $srcH);

		ob_start();
		imagejpeg($outputImage);
		$buffer = ob_get_clean();

		$buffer = base64_encode($buffer);
		return $buffer;
	}

	function resizeImageAndEncode($request) {
		if( !$request->file('icon')->isValid() ) {
			return "";
		}

		$valid_ext = array('png','jpeg','jpg','gif');
		$fileext = strtolower( $request->file('icon')->extension() );
		if( !in_array($fileext, $valid_ext) ) {
			return null;
		}
	
		$tmpname = $request->file('icon')->getPathName();

		$original_info = getimagesize($tmpname);
		//echo($original_info);
		$original_w = $original_info[0];
		$original_h = $original_info[1];
		$original_img = null;	
		if ($original_info['mime'] == 'image/jpeg') {
    		$original_img = imagecreatefromjpeg($tmpname);
		} elseif ($original_info['mime'] == 'image/jpg') {
    		$original_img = imagecreatefromjpeg($tmpname);
		} elseif ($original_info['mime'] == 'image/png') {
    		$original_img = imagecreatefrompng($tmpname);
		} elseif ($original_info['mime'] == 'image/gif') {
    		$original_img = imagecreatefromgif($tmpname);
		}

		$thumb_w = 200;
		$thumb_h = 200;
		$thumb_img = imagecreatetruecolor($thumb_w, $thumb_h);
		imagecopyresampled( $thumb_img, $original_img, 0, 0, 0, 0, $thumb_w, $thumb_h, $original_w, $original_h );

		ob_start();
		imagejpeg($thumb_img);
		$buffer = ob_get_clean();

		$buffer = base64_encode($buffer);
		return $buffer;
	} // end of function

} // End of Helpers class