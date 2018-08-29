<?php
require_once('server_infos.php');
require_once('priv/utils.php');
require_once('priv/template.php');
require_once('priv/models-page-builder.php');
function select_woman($model) {
	return in_array(strtolower($model->sex), array('femme', 'female', 'f'));
}
$db = new Database();
$data = new FrontData($db);
$models = $db->models();
$data->content = print_models($models, select_woman);
$data->title = 'WOMEN';
$data->pagename = 'women';
echo template($data);
?>