<?php
require_once('server_infos.php');
require_once('priv/utils.php');
require_once('priv/template.php');
$db = new Database();
$data = new FrontData($db);
$models = $db->models();
$data->content = 'about';
$data->title = 'ABOUT';
$data->pagename = 'about';
echo template($data);
?>