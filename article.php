<?php
require_once('server_infos.php');
require_once('priv/utils.php');
require_once('priv/template.php');
$id = utils_s_get('id');
$db = new Database();
$model = $db->model($id);
if(!$model) utils_redirection('index.php');
$article = $model->article_content;
if (!$article)
    $article = $model->article;

for($i = 1; $i <= 4; ++$i) {
    $to_search = '{photo '.$i.'}';
    $photo_id = 'photo_'.$i;
    if ($model->$photo_id) {
        $article = str_replace($to_search, '<div class="text-center" style="height: 400px;"><img style="max-height: 100%; width: auto;" src="'.($model->getPhotoByBasename($model->$photo_id)['url']).'"/></div>', $article);
    }
}

$data = new FrontData($db);
$data->title = $model->first_name.' '.$model->last_name;
$data->content = '<h2 class="article-title">'.$data->title.'</h2><div class="article-content">'.$article.'</div>';
$data->meta_description = $data->getMetaDescription($model);
$data->pagename = 'article';
echo template($data);
?>