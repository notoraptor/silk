<?php
// À inclure avant de démarrer la session, pour que la classe Admin soit facilement utilisable dans admin(_not)_connected.php :
require_once('../server_infos.php');
require_once('../priv/utils.php');
if(!session_id()) session_start();
if(isset($_SESSION['admin']))
	include('priv/admin_connected.php');
else if($_SERVER['QUERY_STRING'])
	utils_redirection('index.php');
else
	include('priv/admin_not_connected.php');
?>