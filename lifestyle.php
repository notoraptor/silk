<?php
require_once('server_infos.php');
require_once('priv/utils.php');
require_once('priv/template.php');
require_once('priv/models-page-builder.php');
function select_lifestyle($model) {
	return $model->in_lifestyle;
}
$db = new Database();
$data = new FrontData($db);
$models = $db->models();
$data->content = print_models($models, select_lifestyle);
$data->title = 'LIFESTYLE';
$data->pagename = 'lifestyle';
echo template($data);
?>