<?php
function geocodeLocation($loc, $city, $state)
{
	global $cfg;

	$res   = "";

	$query = urlencode("$loc,$city,$state,Indonesia");
	$url   = "http://maps.googleapis.com/maps/api/geocode/json?address=$query&sensor=false";
	$agent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1";

	$ch    = curl_init();

	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_USERAGENT, $agent);

	$response = curl_exec($ch);

	$content  = json_decode($response);

	if ($content->ResultSet->Error == 0) {
		$lat = $content->results[0]->geometry->location->lat;
		$lon = $content->results[0]->geometry->location->lng;

		$res = array($lat, $lon);
	}

	curl_close($ch);

	return $res;
}

?>