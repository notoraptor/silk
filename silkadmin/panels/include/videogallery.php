<?php
////
require_once('../priv/videodetection.php');
function videoGallery($model, Database $db) {
	// Gestion des formulaires.
	$success = false;
	switch(utils_s_post('gallery-action','')) {
		case 'add':
			if(utils_has_s_post('video')) {
				$link = utils_safe_post('video');
				$link = hex2bin($link);
				if(!utils_valid_url($link) || !Video::parse($link))
					utils_message_add_error('Le nouveau lien vidéo indiqué est invalide ou non pris en charge.');
				else if(!$db->model_video_add($model->model_id, $link))
					utils_message_add_error('Ce lien vidéo a déjà été enregistré pour ce modèle.');
				else
					$success = utils_message_add_success('Nouvelle vidéo ajoutée!');
			}
			break;
		case 'delete':
			if(isset($_POST['todelete']) && is_array($_POST['todelete'])) {
				$todelete = $_POST['todelete'];
				// ...
				$db->model_video_delete($model->model_id, $todelete);
				$success = utils_message_add_success('Les vidéos ont été supprimées.');
			}
			break;
		default:
			break;
	}
	// Affichage des vidéos.
	if($success) $model = $db->model($model->model_id);
	$videos = array();
	foreach($model->videos as $video) {
		$videoInfos = Video::parse($video['video_link']);
		if($videoInfos) {
			$video['code'] = Video::getCode($videoInfos);
			$videos[] = $video;
		}
	}
?>
<div class="videoGallery gallery table">
<div class="upload cell">
	<form method="post" onsubmit="careful(['video']);">
		<fieldset>
		<legend>Ajouter une vidéo</legend>
		<p>
			<input type="hidden" name="gallery-action" value="add"/>
			<div class="table" style="text-align:center;">
				<div class="row">
				<div class="cell"><label for="video">Lien vers la vidéo: </label></div>
				</div>
				<div class="row">
				<div class="cell"><input style="width:80%;" type="url" id="video" name="video"/></div>
				</div>
				<div class="row">
				<div class="cell"><input type="submit" value="ajouter"/></div>
				</div>
			</div>
		</p>
		</fieldset>
	</form>
</div>
<div class="cell">
<div class="view"><?php if(!empty($videos)) { ?>
	<form method="post" onsubmit="return confirm('Voulez-vous vraiment supprimer les vidéos sélectionnées ?');">
	<fieldset>
	<legend>Gestion des vidéos</legend>
	<div class="content">
	<p style="text-align:center;"><strong>Cochez la case d'une vidéo pour la sélectionner. Vous pouvez sélectionner plusieurs vidéos.</strong></p>
	<input type="hidden" name="gallery-action" value="delete"/>
	<div class="photos videos">
	<?php $videoID = -1; foreach($videos as &$video) {
		++$videoID;
		?><label for="d-<?php echo $videoID;?>" style="display:inline-block; margin:2px; padding:5px; background-color:rgb(250,250,250);border:1px solid rgb(240,240,240);">
			<div style="position:relative; width:200px; height:200px; background-color:rgb(245,245,245);">
				<?php echo $video['code']; ?>
			</div>
			<div style="width:190px; padding: 5px 5px 5px 5px; text-align:center;">
				<input type="checkbox" id="d-<?php echo $videoID;?>" name="todelete[]" value="<?php echo $video['video_id'];?>"/>
			</div>
		</label><?php
	} ?>
	</div>
	<p><input type="submit" value="supprimer les vidéos sélectionnées"/></p>
	</div>
	</fieldset>
	</form>
<?php } ?></div>
<div class="options"></div>
</div>
</div>
<?php
}
?>