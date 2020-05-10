<?php

$errorSpanStyle='color:red;';

header('Content-Type: application/json');
//$txt = "Name:" . $_POST['name'] . "\r\nEmail:" . $_POST['email'] . "\r\nPhone:" . $_POST['phone'] . "\r\nText:\r\n" . $_POST['text'];
//echo( json_encode( array('status'=>'error', 'error_message'=>$txt) ) );

if( !isset($_POST['name']) || !isset($_POST['email']) || !isset($_POST['text']) || !isset($_POST['captcha']) ) {
	return;
} 

$nameIsEmpty = ( strlen( $_POST['name'] ) == 0 ) ? TRUE : FALSE;
$emailIsEmpty = ( strlen( $_POST['email'] ) == 0 ) ? TRUE : FALSE;
$textIsEmpty = ( strlen( $_POST['text'] ) == 0 ) ? TRUE : FALSE;

$empty = array();
if( $nameIsEmpty ) {
	array_push( $empty, "name" );
} 
if( $emailIsEmpty ) {
	array_push( $empty, "email" );
} 
if( $textIsEmpty ) {
	array_push( $empty, "text" );
} 
if( sizeof($empty) > 0 ) {
	$emptyStr = implode(',', $empty); 
	echo( json_encode( array( 'status'=>'error', 
		'error_message'=>"<span style='".$errorSpanStyle."'>The following positions are empty: " . $emptyStr . "</span>") ) );
	return;
}

if ( !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) ) {
	echo( json_encode( array( 'status'=>'error', 
		'error_message'=>"<span style='".$errorSpanStyle."'>The email address entered is invalid</span>" ) ) );
	return;
}

echo( json_encode( array( 'status'=>'ok', 'error_message'=>"<span style='color:green;'>Your message has been sent.</span>" ) ) );

$to = "club@ourcsa.ru";
$subject = "A Message Sent from OURCSA.RU";
$txt = "Name:" . $_POST['name'] . "\r\nEmail:" . $_POST['email'] . "\r\nPhone:" . $_POST['phone'] . "\r\nText:\r\n" . $_POST['text'];
$headers = "Content-Type: text/plain; charset=utf-8\r\nFrom: " . $_POST['email'] . "\r\n"; // . "CC: somebodyelse@example.com";

mail( $to, $subject, $txt, $headers );
?>