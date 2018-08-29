<?php
/* Fichier destiné à être inclus dans ./index.php */
require_once('priv/admin-template.php');
$allowedPages = array(
	'config',
	'deletemodel',
	'model',
	'modelphoto',
	'modelportfolio',
	'models',
	'newmodel',
	'users'
);
$querystring =  utils_s_get('panel', '');
ob_start();
if(in_array($querystring, $allowedPages)) {
	echo '<h1>&larr; <a href="index.php">Administration</a></h1>';
	include('panels/'.$querystring . '.php');
} else if($querystring == '') {
	include('panels/menu.html');
} else {
	utils_redirection('index.php');
}
$content = ob_get_contents();
ob_end_clean();
if(utils_has_redirection()) {
	utils_execute_redirection();
} else {
	$donnees = new Data();
	$the_title = '';
	if(isset($GLOBALS['title'])) {
		$the_title = $GLOBALS['title'].' | ';
		unset($GLOBALS['title']);
	}
	$donnees->title = $the_title.($_SESSION['admin']->username()).' | Administration | Kill Management';
	$donnees->content = $content;
	echo template($donnees);
}
?>
