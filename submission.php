<?php
require_once('server_infos.php');
require_once('priv/utils.php');
require_once('priv/template.php');
$db = new Database();
$data = new FrontData($db);
$models = $db->models();
$data->content = 'submission';
$data->title = 'SUBMISSION';
$data->pagename = 'submission';
echo template($data);
?>