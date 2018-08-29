<?php
/*
Client ID :  5b2032e940ae4e64962566d8ec76c0cb
Client Secret :  881d7dc498ae4dfcb55f4185f65bb18f
*/
require_once(server_dir().'/priv/instalib/vendor/autoload.php');
function check_instagram_token() {
	$auth_config = array(
		'client_id'         => '5b2032e940ae4e64962566d8ec76c0cb',
		'client_secret'     => '881d7dc498ae4dfcb55f4185f65bb18f',
		//'redirect_uri'      => 'http://notoraptor.net/killmodels/index.php',
		'redirect_uri'      => 'http://killmanagement.com/index.php',
		'scope'             => array( 'basic' )
	);
	$auth = new Instagram\Auth( $auth_config );
	$token_file = server_dir().'/priv/instagram.token';
	$token = file_exists($token_file) ? file_get_contents($token_file) : false;
	if(!$token) {
		if(!isset($_GET['code'])) {
			$auth->authorize();
		} else {
			$token = $auth->getAccessToken($_GET['code']);
			file_put_contents($token_file, $token);
		}
	}
}
?>