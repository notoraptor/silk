<?php
$success = false;
$id = utils_s_get('id', false);
$rank = utils_s_get('rank', false);
if ($id !== false && ctype_digit($id) && in_array($rank, array(1, 2, 3, 4))) {
	$db = new Database();
	$model = $db->model($id);
	if($model) {
		$directory = utils_model_portfolio_dir($id);
		if(file_exists($directory) && is_dir($directory)) {
			require_once('panels/include/galleryselector.php');
			$fullName = $model->first_name.' '.$model->last_name;
			$field = 'photo_'.$rank;
			if(!empty($_POST)) {
				$selected = getGallerySelected($directory);
				if(!$selected) utils_message_add_error('Erreur interne: impossible de sélectionner la photo '.$rank.'.');
				else {
					$db->model_photo_update($id, $rank, $selected);
					$model->$field = $selected;
					utils_message_add_success('La photo '.$rank.' de '.$fullName.' a été mise à jour.');
				}
			}
			$photoToDisplay = $model->getPhotoByBasename($model->$field);
			?>
			<div class="table breadcumbs">
			<div class="cell main">
			<h2>
				<a href="index.php?panel=models">Modèles</a> /
				<a href="index.php?panel=model&id=<?php echo $id;?>"><?php echo $fullName;?></a> /
				<a href="index.php?panel=modelportfolio&id=<?php echo $id;?>">Portfolio</a> /
					Photo <?php echo $rank;?>
			</h2>
            <p>
                <?php for ($i = 1; $i <= 4; ++$i) if ($i != $rank) { ?>
                    <a <?php echo 'href="index.php?panel=modelphoto&rank='.$i.'&id='.$id.'"';?>>Choisir la <strong>photo <?php echo $i; ?></strong></a>
                    <?php
                } ?>
            </p>
			</div>
			<div class="cell photo"><?php if($photoToDisplay) { ?><img src="<?php echo $photoToDisplay['url'];?>"/><?php } ?></div>
			</div>
			<?php
			gallerySelector($directory, $model->$field);
			$success = true;
		}
	}
}
if(!$success)
	utils_message_add_error("Impossible d'afficher une photo du modèle ayant l'ID $id. Modèle inexistant, ou erreur interne.");
?>