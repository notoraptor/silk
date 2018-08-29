<?php
class Utils {
	// Curl helper function (from VIMEO)
	static public function curl_get($url) {
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		$return = curl_exec($curl);
		curl_close($curl);
		return $return;
	}
	static public function valid_url($url) {
		return !filter_var($url, FILTER_VALIDATE_URL) === false;
	}
}
?>