<?php
////
function getGallerySelected($folder) {
	$path = realpath($folder);
	if(SERVER_IS_WINDOWS) $path = str_replace('\\','/',$path);
	// Gestion du formulaire.
	if(file_exists($path) && is_dir($path) && utils_has_s_post('selected')) {
		$selected = utils_safe_post('selected');
		if($selected != '') {
			$photopath = $path.'/'.$selected;
			if(file_exists($photopath) && is_file($photopath)) {
				$photoinfos = getimagesize($photopath);
				if($photoinfos) return $selected;
			}
		}
	}
	return false;
}
function gallerySelector($folder, $selected = null) {
//$path = server_dir().'/'.$folder;
$path = realpath($folder);
if(SERVER_IS_WINDOWS) $path = str_replace('\\','/',$path);
$photos = array();
if(file_exists($path) && is_dir($path)) {
	// Affichage des photos.
	$list = scandir($path);
	foreach($list as $file) {
		$photopath = $path.'/'.$file;
		if(is_file($photopath)) {
			$photoinfos = getimagesize($photopath);
			if($photoinfos) {
				$photos[] = array(
					'path' => $photopath,
					'basename' => $file,
					'url' => str_replace(server_dir(),server_http(),$photopath),
					'width' => $photoinfos[0],
					'height' => $photoinfos[1]
				);
			}
		}
	}
?>
<div class="gallery">
<div class="view"><?php if(!empty($photos)) { ?>
	<form id="selector" method="post">
		<input type="hidden" name="selected" id="selected"/>
		<script type="text/javascript">//<!--
		function selectPhoto(photoId) {
			var form = document.getElementById('selector');
			var input = document.getElementById('selected');
			input.value = photoId;
			form.submit();
		}
		//--></script>
	</form>
	<p style="text-align:center;"><strong>Cliquez sur une photo pour la sélectionner.</strong></p>
	<div class="photos"><?php
	foreach($photos as &$photo) {
		?><div class="<?php echo $selected === $photo['basename'] ? 'photoSelected' : 'photoToSelect';?>" onclick="selectPhoto('<?php echo $photo['basename'];?>');"><div style="background-image:url('<?php echo $photo['url'];?>');"></div></div><?php
	} ?></div>
<?php } else { ?>
	<h3 style="text-align:center;">Aucune photo dans ce portfolio!</h3>
	<h3 style="text-align:center;">Veuillez ajouter des photos dans le portfolio du modèle! Vous pourrez ensuite sélectionner la photo de profil parmi les photos du porfolio.</h3>
<?php } ?></div>
</div>
<?php
}
}
?>