<?php
if(!session_id()) session_start();
require_once('../server_infos.php');
require_once('../priv/utils.php');
if(isset($_SESSION['admin'])) {
	$_SESSION = array();
	session_destroy();
}
utils_redirection('index.php');
?>