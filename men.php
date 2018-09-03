<?php
require_once('server_infos.php');
require_once('priv/utils.php');
require_once('priv/template.php');
require_once('priv/models-page-builder.php');
$select_man = function($model) {
	return in_array(strtolower($model->sex), array('homme', 'male', 'm'));
};
$db = new Database();
$data = new FrontData($db);
$models = $db->models();
$data->content = print_models($models, $select_man, 'men');
$data->title = 'MEN';
$data->pagename = 'men';
echo template($data);
?>