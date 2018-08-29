<?php
/* Fichier destiné à être inclus dans ./index.php */
require_once('priv/admin-template.php');
require_once('priv/cool-php-captcha/captcha.php');
$db = new Database();
$donnees = new Data();
ob_start();
?><div class="admin home">
	<form action="login.php" method="post">
		<fieldset>
			<legend>Connexion</legend>
			<div class="table">
			<?php
			echo input_text('Pseudonyme', 'username', 'text');
			echo input_password('Mot de passe', 'password');
			?>
			</div>
			<input type="submit" value="connexion"/>
		</fieldset>
	</form>
	<form action="create-account.php" method="post">
		<fieldset>
			<legend>Cr&eacute;er un compte</legend>
			<div class="table">
			<?php
			echo input_text('Pseudonyme', 'username');
			echo input_password('Mot de passe', 'password');
			echo input_password('Mot de passe (une seconde fois)', 'password-again');
			$captcha = SimpleCaptcha::generate();
			echo input_text('Veuillez entrer le code CAPTCHA ci-dessous<br/><br/><img src="data:image/'.$captcha['type'].';base64,'.$captcha['image'].'"/>', 'captcha');
			$_SESSION['new-account-captcha'] = $captcha['captcha'];
			?>
			</div>
			<input type="submit" value="cr&eacute;er un compte"/>
		</fieldset>
	</form>
</div><?php
$donnees->content = ob_get_contents();
ob_end_clean();
$donnees->title = 'Accueil administration (invité) | Kill Management';
echo template($donnees);
?>
