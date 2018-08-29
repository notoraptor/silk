<?php
error_reporting(-1);
ini_set("display_errors", 0);
ini_set("log_errors", 1);
ini_set("error_log", __DIR__ ."/priv/php_errors.txt");

// Actuellement inutile.
define('SERVER_IS_WINDOWS', true);
// Actuellement utiles.
//// define('SERVER_ADDRESS'   , 'http://localhost/libersaurus');
define('NOM_HOTE'         , 'localhost');
define('NOM_BASE'         , 'silk');
define('NOM_UTILISATEUR'  , 'root');
define('MOT_DE_PASSE'     , '');
define('DB_PREFIX'        , '');

function server_dir() {
	$filepath = __FILE__;
	$filedir = str_replace(basename($filepath),'',$filepath);
	if(SERVER_IS_WINDOWS) $filedir = str_replace('\\','/',$filedir);
	if($filedir[strlen($filedir)-1] == '/')
		$filedir = substr($filedir,0,strlen($filedir)-1); //exclusion du dernier slash.
	return $filedir;
}

function server_get_protocol() {
	$protocol = 'http';
	if((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== '') || ($_SERVER['SERVER_PORT'] == 443))
		$protocol .= 's';
	return $protocol;
}

function server_http() {
	$document_root = $_SERVER['DOCUMENT_ROOT'];
	$http_host = $_SERVER['HTTP_HOST'];
	if(SERVER_IS_WINDOWS) $document_root = str_replace('\\','/',$document_root);
	if($document_root[strlen($document_root)-1] != '/') $document_root .= '/';
	if($http_host[strlen($http_host)-1] != '/') $http_host .= '/';
	$adresse = server_get_protocol().'://'.str_replace($document_root, $http_host, server_dir().'/');
	return substr($adresse,0,strlen($adresse)-1);
}

function server_http_path($path) {
	$server_http = server_http();
	return strstr($path, $server_http) ? $path : $server_http.'/'.$path;
}

function server_dir_path($path) {
	$server_dir = server_dir();
	return strstr($path, $server_dir) ? $path : $server_dir.'/'.$path;
}
?>
