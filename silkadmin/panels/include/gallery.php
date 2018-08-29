<?php
////
function gallery($folder, $model) {
//$path = server_dir().'/'.$folder;
$path = realpath($folder);
if(SERVER_IS_WINDOWS) $path = str_replace('\\','/',$path);
$photos = array();
if(!file_exists($path) || !is_dir($path)) return;
// Gestion des formulaires.
switch(utils_s_post('gallery-action','')) {
	case 'upload':
		if(!empty($_FILES)) {
			if(count($_FILES) != 1)
				utils_message_add_error("Vous devez envoyer 1 seule photo.");
			else if(isset($_FILES['upload']) && $_FILES['upload']['name']) {
				$uploaded = utils_upload('upload', $path);
				if($uploaded)
					utils_message_add_success('Votre photo a été mise en ligne.');
				else
					utils_message_add_error('Erreur interne: impossible de mettre en ligne votre fichier.');
			}
		}
		break;
	case 'delete':
		if(isset($_POST['to-delete']) && is_array($_POST['to-delete'])) {
			// Récupérer toutes les photos à supprimer.
			$to_delete = array();
			foreach($_POST['to-delete'] as $element) {
				$pathToDelete = $path.'/'.$element;
				$to_delete[] = $pathToDelete;
			}
			$initialCount = count($to_delete);
			$finalCount = $initialCount;
			foreach($to_delete as $pathToDelete) {
				if(@unlink($pathToDelete)) --$finalCount;
			}
			if($finalCount == 0)
				utils_message_add_success('Les photos ont été supprimées.');
			else if($finalCount == $initialCount)
				utils_message_add_error('Erreur interne: impossible de supprimer les photos sélectionnées.');
			else
				utils_message_add_error("Erreur interne: certaines photos n'ont pu être supprimées.");
		}
		break;
	default:
		break;
}
// Affichage des photos.
$list = scandir($path);
$photos = array();
foreach($list as $file) {
	$photopath = $path.'/'.$file;
	$photo = utils_local_photo($photopath);
	if($photo) $photos[] = $photo;
} ?>
<div class="gallery table">
<div class="upload cell">
	<form method="post" enctype="multipart/form-data">
		<fieldset>
		<legend>Ajouter une photo</legend>
		<div class="table" style="width:100%;">
			<input type="hidden" name="gallery-action" value="upload"/>
			<div class="row">
				<div class="cell"><label for="upload">Photo</label></div>
				<div class="cell"><input style="padding-bottom:10px;" type="file" name="upload" id="upload" accept="image/*"/></div>
			</div>
		</div>
		<input type="submit" value="ajouter"/>
		</fieldset>
	</form>
</div>
<div class="cell">
<div class="view"><?php if(!empty($photos)) { ?>
	<form method="post" onsubmit="return confirm('Voulez-vous vraiment supprimer les photos sélectionnées ?');">
	<fieldset>
	<legend>Gestion des photos</legend>
	<div class="content">
	<p style="text-align:center;"><strong>Cliquez sur une photo pour la sélectionner. Vous pouvez sélectionner plusieurs photos.</strong></p>
	<input type="hidden" name="gallery-action" value="delete"/>
	<div class="photos"><?php
	$photoID = -1;
	foreach($photos as &$photo) {
		++$photoID;
		$photo['rank'] = 0;
		if ($model->photo_1 == $photo['basename'] && !$photo['rank']) $photo['rank'] = 1;
		if ($model->photo_2 == $photo['basename'] && !$photo['rank']) $photo['rank'] = 2;
		if ($model->photo_3 == $photo['basename'] && !$photo['rank']) $photo['rank'] = 3;
		if ($model->photo_4 == $photo['basename'] && !$photo['rank']) $photo['rank'] = 4;
		?><label for="d-<?php echo $photoID;?>">
			<div class="photo" style="background-image:url('<?php echo $photo['url'];?>');"></div>
			<div class="selector">
				<input type="checkbox" id="d-<?php echo $photoID;?>" name="to-delete[]" value="<?php echo $photo['basename'];?>"/>
                <?php if ($photo['rank']) echo '(photo '.$photo['rank'].')';?>
			</div>
		</label><?php
	} ?></div>
	<p><input type="submit" value="supprimer les photos sélectionnées"/></p>
	</div>
	</fieldset>
	</form>
<?php } ?></div>
</div>
</div><?php
} ?>