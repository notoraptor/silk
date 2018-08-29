<?php
$db = new Database();
$model = false;
if(!empty($_POST)) {
	$prenom = strip_tags(utils_safe_post('prenom', ''));
	$nom = strip_tags(utils_safe_post('nom', ''));
	$instagram = strip_tags(utils_safe_post('instagram', ''));
	$lifestyle = strip_tags(utils_safe_post('lifestyle', ''));
	$hauteur = strip_tags(utils_safe_post('hauteur', ''));
	$taille = strip_tags(utils_safe_post('taille', ''));
	$taille_chaussures = strip_tags(utils_safe_post('taille-chaussures', ''));
	$taille_poitrine = strip_tags(utils_safe_post('taille-poitrine', ''));
	$taille_hanches = strip_tags(utils_safe_post('taille-hanches', ''));
	$couleur_cheveux = strip_tags(utils_safe_post('couleur-cheveux', ''));
	$couleur_yeux = strip_tags(utils_safe_post('couleur-yeux', ''));
	$sexe = strip_tags(utils_safe_post('sexe', ''));
	$lifestyle = $lifestyle == '' ? 0 : 1;
	if($prenom == '') utils_message_add_error('Veuillez indiquer un prénom !');
	else if($nom == '') utils_message_add_error('Veuillez indiquer un nom !');
	else {
		$model = $db->model_create(array(
			'first_name'=> $prenom,
			'last_name'=> $nom,
			'instagram_link'=> $instagram,
			'in_lifestyle'=> $lifestyle,
			'height'=> $hauteur,
			'waist'=> $taille,
			'shoes'=> $taille_chaussures,
			'bust'=> $taille_poitrine,
			'hips'=> $taille_hanches,
			'hair'=> $couleur_cheveux,
			'eyes'=> $couleur_yeux,
			'sex'=> $sexe
		));
		if(!$model) utils_message_add_error('Ce prénom et ce nom correspondent déjà à un modèle.');
		else utils_message_add_success('Le modèle '.$prenom.' '.$nom.' a été créé. <a href="index.php?panel=newmodel">Créer un autre modèle.</a>');
	}
	$_POST['instagram'] = $instagram;
}
if($model) {
	utils_request_redirection('index.php?panel=model&id='.$model->model_id);
} else {
?><h2><a href="index.php?panel=models">Modèles</a> / Créer un nouveau modèle</h2>
<div class="newmodel">
<form method="post">
<fieldset>
<legend>Création d'un nouveau modèle.</legend>
<p>Veuillez créer un nouveau modèle en définissant les infos élémentaires. Vous pourrez ensuite modifier toutes les données du modèle après sa création.</p>
<div class="table">
	<?php
	$help = '(navigateurs récents) appuyez sur la touche BAS dans le champ pour afficher des valeurs prédéfinies proposées.';
	echo utils_required_input('Prénom', 'prenom', 'text', '');
	echo utils_required_input('Nom', 'nom', 'text', '');
	echo utils_input('Pseudonyme Instagram (exemple: killmanagement)', 'instagram', 'text', '');
	echo utils_checkbox('Apparait sur la page LIFESTYLE?', 'lifestyle');
	echo utils_input('Hauteur [height]', 'hauteur', 'text', '');
	echo utils_input('Taille [waist]', 'taille', 'text', '');
	echo utils_input('Taille chaussures [shoes]', 'taille-chaussures', 'text', '');
	echo utils_input('Taille poitrine [bust]', 'taille-poitrine', 'text', '');
	echo utils_input('Taille hanches [hips]', 'taille-hanches', 'text', '');
	echo utils_input('Couleur des cheveux [hair]', 'couleur-cheveux', 'text', 'list="hairs"', $help);
	echo utils_input('Couleur des yeux [eyes]', 'couleur-yeux', 'text', 'list="eyes"', $help);
	echo utils_input('Sexe', 'sexe', 'text', 'list="sex"', $help);
	echo utils_datalist('hairs', $db->list_hairs());
	echo utils_datalist('eyes', $db->list_eyes());
	echo utils_datalist('sex', $db->list_sex());
	?>
</div>
<input type="submit" value="créer le modèle"/>
</fieldset>
</form>
</div>
<?php } ?>