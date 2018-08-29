<?php
$success = false;
$id = utils_s_get('id', false);
if($id !== false && ctype_digit($id)) {
	$db = new Database();
	if($db->model_exists($id)) {
		$modelDir = utils_model_dir($id);
		$modelPortfolioDir = utils_model_portfolio_dir($id);
		if(!file_exists($modelDir)) mkdir($modelDir);
		if(!file_exists($modelPortfolioDir)) mkdir($modelPortfolioDir);
		if(file_exists($modelPortfolioDir) && is_dir($modelPortfolioDir)) {
			$success = true;
			$galleryContent = '';
			require_once('panels/include/gallery.php');
			$model = $db->model($id);
			capture_start();
			gallery($modelPortfolioDir, $model);
			capture_end($galleryContent);
			$fullName = $model->first_name.' '.$model->last_name;
			$profilePhoto = $model->getPhotoByBasename($model->photo); ?>
			<div class="table breadcumbs">
			<div class="cell main">
			<h2>
				<a href="index.php?panel=models">Modèles</a> /
				<a href="index.php?panel=model&id=<?php echo $id;?>"><?php echo $fullName;?></a> /
				Portfolio
			</h2>
			<p>
				<a href="index.php?panel=modelphoto&rank=1&id=<?php echo $id;?>">Choisir la <strong>photo 1</strong> parmi les photos du porfolio</a> |
				<a href="index.php?panel=modelphoto&rank=2&id=<?php echo $id;?>">Choisir la <strong>photo 2</strong> parmi les photos du porfolio</a> |
				<a href="index.php?panel=modelphoto&rank=3&id=<?php echo $id;?>">Choisir la <strong>photo 3</strong> parmi les photos du porfolio</a> |
				<a href="index.php?panel=modelphoto&rank=4&id=<?php echo $id;?>">Choisir la <strong>photo 4</strong> parmi les photos du porfolio</a>
			</p>
			</div>
			<div class="cell photo"><?php if($profilePhoto) { ?><img src="<?php echo $profilePhoto['url'];?>"/><?php } ?></div>
			</div>
			<?php
			echo $galleryContent;
		}
	}
}
if(!$success)
	utils_message_add_error("Impossible d'afficher le portfolio du modèle ayant pour ID $id. Modèle inexistant, ou erreur interne.");
?>