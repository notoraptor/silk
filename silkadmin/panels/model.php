<?php
$db = new Database();
if(!utils_has_s_get('id')) utils_request_redirection('index.php?panel=models');
else {
$id = utils_s_get('id');
$model = $db->model($id);
if(!$model) utils_request_redirection('index.php?panel=models');
else {
if(!empty($_POST)) {
	$prenom                  = strip_tags(utils_safe_post('first-name', $model->first_name));
	$nom                     = strip_tags(utils_safe_post('last-name', $model->last_name));
	$instagram_link          = strip_tags(utils_safe_post('instagram-link', $model->instagram_link));
	$lifestyle               = strip_tags(utils_safe_post('in-lifestyle', false));
	$hauteur                 = strip_tags(utils_safe_post('height', $model->height));
	$taille                  = strip_tags(utils_safe_post('waist', $model->waist));
	$taille_chaussures       = strip_tags(utils_safe_post('shoes', $model->shoes));
	$taille_poitrine         = strip_tags(utils_safe_post('bust', $model->bust));
	$taille_hanches          = strip_tags(utils_safe_post('hips', $model->hips));
	$couleur_cheveux         = strip_tags(utils_safe_post('hair', $model->hair));
	$couleur_yeux            = strip_tags(utils_safe_post('eyes', $model->eyes));
	$sexe                    = strip_tags(utils_safe_post('sex', $model->sex)); //..
	$adresse                 = strip_tags(utils_safe_post('address',$model->address));
	$date_inscription_day    = strip_tags(utils_safe_post('date-added-day',$model->date_added_day));
	$date_inscription_month  = strip_tags(utils_safe_post('date-added-month',$model->date_added_month));
	$date_inscription_year   = strip_tags(utils_safe_post('date-added-year',$model->date_added_year));
	//
	if($lifestyle)
		$lifestyle = 1;
	else
		$lifestyle = 0;
	if($prenom == '') utils_message_add_error('Veuillez indiquer un prénom !');
	else if($nom == '') utils_message_add_error('Veuillez indiquer un nom !');
	else if($date_inscription_day && !utils_check_day($date_inscription_day)) utils_message_add_error('Date d\'inscription: jour invalide.');
	else if($date_inscription_month && !utils_check_month($date_inscription_month)) utils_message_add_error('Date d\'inscription: mois  invalide.');
	else if($date_inscription_year && !utils_check_year($date_inscription_year)) utils_message_add_error('Date d\'inscription: année invalide.');
	else {
		$model = $db->model_update($model->model_id, array(
			'first_name'     => $prenom,
			'last_name'      => $nom,
			'instagram_link' => $instagram_link,
			'in_lifestyle'   => $lifestyle,
			'height'         => $hauteur,
			'waist'          => $taille,
			'shoes'          => $taille_chaussures,
			'bust'           => $taille_poitrine,
			'hips'           => $taille_hanches,
			'hair'           => $couleur_cheveux,
			'eyes'           => $couleur_yeux,
			'sex'            => $sexe,
			'address'        => $adresse,
			'date_added'     => utils_get_date($date_inscription_year, $date_inscription_month, $date_inscription_day)
		));
		if(!$model) utils_message_add_error('Erreur interne: impossible de mettre à jour les informations de ce modèle.');
		else utils_message_add_success('Le modèle '.$prenom.' '.$nom.' a été modifié.');
	}
}
$_POST = $model->toPOST();

$fullName = $model->first_name.' '.$model->last_name;
$profilePhoto = $model->getPhotoByBasename($model->photo);
?>
<div class="modelEdition">
<div class="table breadcumbs">
	<div class="cell main">
		<h2><a href="index.php?panel=models">Modèles</a> / <?php echo $fullName;?></h2>
		<p>
            <a href="index.php?panel=modelarticle&id=<?php echo $model->model_id;?>">Modifier l'article du modèle</a> |
            <a href="index.php?panel=modelportfolio&id=<?php echo $model->model_id;?>">Portfolio</a> |
            <a style="color:red;"
               href="index.php?panel=deletemodel&id=<?php echo $model->model_id;?>"
               onclick="return confirm('Voulez-vous vraiment supprimer le modèle <?php echo $fullName;?> ?');">
                <strong>Supprimer ce modèle</strong>
            </a>
		</p>
	</div>
	<div class="cell photo"><?php if($profilePhoto) { ?><img src="<?php echo $profilePhoto['url'];?>"/><?php } ?></div>
</div>
<form method="post">
<fieldset>
<legend>Modifier ses infos.</legend>
<div class="table">
	<?php
	$help = '(navigateurs récents) appuyez sur la touche BAS dans le champ pour afficher des valeurs prédéfinies proposées.';
	echo utils_required_input('Prénom', 'first-name', 'text', '');
	echo utils_required_input('Nom', 'last-name', 'text', '');
	echo utils_input('Pseudonyme Instagram (exemple: killmanagement)', 'instagram-link', 'text', '');
	echo utils_checkbox('Afficher sur la page LIFESTYLE?', 'in-lifestyle');
	echo utils_input('Hauteur [height]', 'height', 'text', '');
	echo utils_input('Taille [waist]', 'waist', 'text', '');
	echo utils_input('Taille chaussures [shoes]', 'shoes', 'text', '');
	echo utils_input('Taille poitrine [bust]', 'bust', 'text', '');
	echo utils_input('Taille hanches [hips]', 'hips', 'text', '');
	echo utils_input('Couleur des cheveux [hair]', 'hair', 'text', 'list="dl-hair"', $help);
	echo utils_input('Couleur des yeux [eyes]', 'eyes', 'text', 'list="dl-eyes"', $help);
	echo utils_input('Sexe', 'sex', 'text', 'list="dl-sex"', $help);
	// ...
	echo utils_input('Adresse', 'address', 'text', '');
	echo utils_date_input("Date d'inscription", 'date-added', $model->date_added_year, $model->date_added_month, $model->date_added_day);
	echo utils_datalist('dl-hair', $db->list_hairs());
	echo utils_datalist('dl-eyes', $db->list_eyes());
	echo utils_datalist('dl-sex', $db->list_sex());
	?>
</div>
<p><input type="submit" value="modifier ce modèle"/></p>
</fieldset>
</form>
</div>
<?php
}
}
 ?>