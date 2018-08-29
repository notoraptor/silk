<?php
if(!session_id()) session_start();
require_once('../server_infos.php');
require_once('../priv/utils.php');
if(isset($_POST['username']) && isset($_POST['password'])) {
	$db = new Database();
	$admin = $db->admin_login(utils_safe_post('username'), utils_safe_post('password'));
	if($admin) {
		if($admin->approved())
			$_SESSION['admin'] = $admin;
		else
			utils_message_add_attention("Attention: votre compte est en attente d'approbation. Vous ne pouvez pas vous connecter.");
	} else {
		utils_message_add_error('Connexion: erreur: pseudonyme et/ou mot de passe invalide(s).');
	}
}
utils_redirection('index.php');
?>