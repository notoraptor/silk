<?php
require_once('server_infos.php');
require_once('priv/utils.php');
require_once('priv/template-portfolio.php');
$id = utils_s_get('id');
$origin = utils_s_get('origin');
if (!ctype_digit($id) || !file_exists($origin.'.php'))
	utils_redirection('index.php');
$db = new Database();
$model = $db->model($id);
if(!$model) utils_redirection('index.php');
$data = new FrontData($db);
$data->title = $model->first_name.' '.$model->last_name.' | PORTFOLIO';
$data->content = $model;
$data->meta_description = $data->getMetaDescription($model, 'portfolio image images photos');
$data->pagename = 'portfolio';
echo template($data);
?>