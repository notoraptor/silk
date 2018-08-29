<?php
session_start();
require_once('../server_infos.php');
require_once('../priv/utils.php');
if(isset($_POST['username']) && isset($_POST['password']) && isset($_POST['password-again'])) {
	$captcha = utils_safe_post('captcha');
	$username = utils_safe_post('username');
	$password = utils_safe_post('password');
	$passwordAgain = utils_safe_post('password-again');
	if(isset($_SESSION['new-account-captcha']) && $_SESSION['new-account-captcha'] !== $captcha)
		utils_message_add_error("Vous n'avez pas entré le bon code CAPTCHA.");
	else if($username == '')
		utils_message_add_error("Veuillez entrer un pseudonyme !");
	else if(!utils_valid_username($username))
		utils_message_add_error(utils_username_error());
	else if($password != $passwordAgain)
		utils_message_add_error("Vous n'avez pas tapé deux fois le même mot de passe.");
	else if(!utils_valid_password($password))
		utils_message_add_error(utils_password_error());
	else {
		$db = new Database();
		$admin = $db->admin_create($username, $password);
		if($admin) {
			if($admin->approved())
				$_SESSION['admin'] = $admin;
			else
				utils_message_add_attention("Attention: votre compte doit être maintenant approuvé par un administrateur. Vous ne pouvez pas vous connecter pour le moment.");
		} else {
			utils_message_add_error('Ce pseudonyme est déjà utilisé.');
		}
	}
}
utils_redirection('index.php');
?>