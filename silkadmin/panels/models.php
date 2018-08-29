<?php
$db = new Database();
?><h2>Modèles</h2>
<h3><a href="index.php?panel=newmodel">Créer un nouveau modèle</a></h3>
<?php
$models = $db->models();
if(!empty($models)) { ?>
<h3>Gestion des modèles</h3>
<div class="models"><?php
foreach($models as &$model) {
	$fullName = $model->first_name.' '.$model->last_name;
	$photo = null;
	$photopath = utils_model_portfolio_dir($model->model_id.'/'.$model->photo);
	if(is_file($photopath)) {
		$photoinfos = getimagesize($photopath);
		if($photoinfos) $photo = array(
			'path' => $photopath,
			'url' => str_replace(server_dir(),server_http(),$photopath),
			'width' => $photoinfos[0],
			'height' => $photoinfos[1]
		);
	}
	?><div class="model">
		<div class="profilePhoto"<?php if($photo) { ?> style="background-image:url('<?php echo $photo['url'];?>');"<?php } ?>>
			<?php if($photo) { ?>
			<a href="index.php?panel=model&id=<?php echo $model->model_id;?>"></a>
			<?php } else { ?>
			<div style="color:rgb(240,240,240);">&nbsp;</div>
			<?php } ?>
		</div>
		<div class="editionLinks">
			<div>
                <strong style="font-size:1.2rem;"><a href="index.php?panel=model&id=<?php echo $model->model_id;?>"><?php echo $fullName;?></a></strong>
				<?php if($model->instagram_link) { ?>
				<span class="instalink"><a target="_blank" href="<?php echo $model->instagram_link;?>"><img src="<?php echo server_http()?>/data/main/instagram-gold.svg"/></a></span>
				<?php } ?>
			</div>
			<div><a href="index.php?panel=modelportfolio&id=<?php echo $model->model_id;?>">Portfolio</a></div>
			<div>
				<a style="color:red;" href="index.php?panel=deletemodel&id=<?php echo $model->model_id;?>" onclick="return confirm('Voulez-vous vraiment supprimer le modèle <?php echo $fullName;?> ?');"><strong>Supprimer</strong></a>
			</div>
		</div>
	</div><?php
}
?></div>
<?php }
?>