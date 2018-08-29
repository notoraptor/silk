<?php
require_once('server_infos.php');
require_once('priv/utils.php');
require_once('priv/template.php');
require_once('priv/models-page-builder.php');
function select_man($model) {
	return in_array(strtolower($model->sex), array('homme', 'male', 'm'));
}
$db = new Database();
$data = new FrontData($db);
$models = $db->models();
$data->content = print_models($models, select_man);
$data->title = 'MEN';
$data->pagename = 'men';
echo template($data);
?>