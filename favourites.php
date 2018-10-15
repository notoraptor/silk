<?php
require_once('server_infos.php');
require_once('priv/utils.php');
require_once('priv/template.php');
require_once('priv/models-page-builder.php');
if (!session_id()) session_start();
$action = utils_s_get('action');
$id = utils_s_get('id');
$origin = utils_s_get('origin');
$from = utils_s_get('from');
$pagename = $from ? $from : $origin;
$db = new Database();
if ($action && $id && $pagename) {
	if (ctype_digit($id) && file_exists($pagename.'.php') && in_array($action, array('add', 'remove'))) {
		if ($db->model($id)) {
			if ($action == 'add') {
				utils_add_favourite($id);
			} else {
				utils_remove_favourite($id);
			}
			if ($from)
				utils_redirection($from.'.php');
			else
				utils_redirection('model.php?origin='.$origin.'&id='.$id);
		}
	}
};
$select_favourites = function($model) {
	return utils_has_favourite($model->model_id);;
};
$count_favourites = utils_count_favourites();
if (!$count_favourites)
    utils_redirection('index.php');
$data = new FrontData($db);
$models = $db->models();
$data->content = print_models($models, $select_favourites, 'favourites');
$data->title = 'Your favourite models ('.$count_favourites.') | SILK';
$data->pagename = 'favourites';
echo template($data);
?>