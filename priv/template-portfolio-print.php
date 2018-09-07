<?php
function template($data) {
if (!session_id()) session_start();
if ($data->meta_description == '')
	$data->meta_description = 'SILK fashion modeling agency, official website';
if ($data->meta_keywords == '') {
	$data->meta_keywords = implode(',', array(
		'fashion', 'modeling agency', 'silk', 'photography', 'booking', 'montreal', 'modeling', 'agency', 'model',
		'models', 'silk girl', 'silk man', 'silk team', 'silkgirl', 'silkteam'
	));
}
$model = $data->content;
$origin = utils_s_get('origin');
$in_favourites = utils_has_favourite($model->model_id);
ob_start();
?><!DOCTYPE html>
<html lang="en">
<head>
    <base href="<?php echo server_http(); ?>/"/>
    <meta charset="UTF-8"/>
    <meta name="description" content="<?php echo $data->meta_description; ?>"/>
    <meta name="keywords" content="<?php echo $data->meta_keywords; ?>"/>
    <meta name="author" content="Steven Bocco"/>
    <title><?php echo $data->title; ?></title>
    <link rel="shortcut icon" type="image/x-icon" href="data/main/favicon.ico"/><!-- TODO favicon -->
    <link rel="icon" type="image/x-icon" href="data/main/favicon.ico"/>
    <link rel="stylesheet" href="css/bootstrap.css"/>
    <link rel="stylesheet" href="css/style.css"/>
	<?php if ($data->head != '') echo $data->head; ?>
</head>
<body <?php if ($data->pagename != '') echo 'id="' . $data->pagename . '"'; ?>>
<div id="content" class="container">
    <div id="page-title" class="mb-4">
        <div id="line-1">
			<?php if ($data->pagename != 'home') { ?><a href="index.php"><?php } ?>
                SILK
				<?php if ($data->pagename != 'home') { ?></a><?php } ?>
        </div>
        <div id="line-2">MANAGEMENT</div>
    </div>
    <div>
        <div class="row">
            <div class="col-sm">
				<?php if ($model->photo_1) {
					?><img class="img-fluid" src="<?php echo $model->getPhotoByBasename($model->photo_1)['url'];?>"/><?php
				} ?>
            </div>
            <div class="col-sm">
				<?php if ($model->photo_2) {
					?><img class="img-fluid" src="<?php echo $model->getPhotoByBasename($model->photo_2)['url'];?>"/><?php
				} ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm">
				<?php if ($model->photo_3) {
					?><img class="img-fluid" src="<?php echo $model->getPhotoByBasename($model->photo_3)['url'];?>"/><?php
				} ?>
            </div>
            <div class="col-sm">
				<?php if ($model->photo_4) {
					?><img class="img-fluid" src="<?php echo $model->getPhotoByBasename($model->photo_4)['url'];?>"/><?php
				} ?>
            </div>
        </div>
        <p class="mt-3 model-name"><?php echo $model->first_name.' '.$model->last_name;?></p>
        <p class="model-details">
			<?php
			$detail_names = array('height', 'bust', 'waist', 'hips', 'shoes', 'hair', 'eyes');
			$detail_titles = ARRAY('Height', 'Bust', 'Waist', 'Hips', 'Shoes', 'Hair', 'Eyes');
			for ($i = 0; $i < count($detail_names); ++$i) {
				$detail_name = $detail_names[$i];
				$detail_title = $detail_titles[$i];
				if ($i) {
					?><span class="detail-separator">&#8226;</span><?php
				}
				?>
                <span class="detail">
                <span class="detail-title"><?php echo $detail_title;?>:</span>
                <span class="detail-value"><?php echo $model->$detail_name;?></span>
            </span>
				<?php
			}
			?>
        </p>
        <div class="mb-4 model-address"><?php echo $model->address; ?></div>
    </div>
</div>
<script type="text/javascript" src="js/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="js/popper.min.js"></script>
<script type="text/javascript" src="js/bootstrap.js"></script>
<?php if ($data->scripts != '') echo $data->scripts; ?>
</body>
</html><?php
$content = ob_get_contents();
ob_end_clean();
return $content;
}
?>