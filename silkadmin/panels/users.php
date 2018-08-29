<?php
$admin = $_SESSION['admin'];
$db = new Database();
switch(utils_s_get('action','')) {
	case 'changePassword':
		if(!utils_has_s_post('old-password')) utils_message_add_error('Veuillez entrer votre ancien mot de passe.');
		else if(!utils_has_s_post('new-password')) utils_message_add_error('Veuillez entrer votre nouveau mot de passe.');
		else if(!utils_has_s_post('new-password-again'))
			utils_message_add_error('Veuillez entrer votre nouveau mot de passe une seconde fois.');
		else {
			$oldPassword = utils_safe_post('old-password');
			$newPassword = utils_safe_post('new-password');
			$newPasswordAgain = utils_safe_post('new-password-again');
			if(!password_verify($oldPassword, $admin->password()))
				utils_message_add_error('Votre ancien mot de passe est incorrect.');
			else if($newPassword != $newPasswordAgain)
				utils_message_add_error("Vous n'avez pas tapé deux fois le même NOUVEAU mot de passe.");
			else if(!utils_valid_password($newPassword))
				utils_message_add_error(utils_password_error(true));
			else {
				$admin = $db->admin_update($admin->username(), $oldPassword, $newPassword);
				if($admin) {
					$_SESSION['admin'] = $admin;
					utils_message_add_success("Votre mot de passe a été mis à jour.");
				} else
					utils_message_add_error('Une erreur interne est survenue: impossible de mettre à jour votre mot de passe.');
			}
		}
		break;
	case 'approve':
		if(!utils_has_s_get('id'))
			utils_message_add_error("Aucun ID d'administrateur à approuver !");
		else {
			$other = $db->admin_approve(utils_s_get('id'));
			if($other)
				utils_message_add_success("L'administrateur \"" . $other->username() . "\" a été approuvé.");
			else
				utils_message_add_error("Erreur interne: impossible d'approuver l'administrateur ayant pour ID \"".utils_s_get('id')."\".");
		}
		break;
	case 'delete':
		if(!utils_has_s_get('id'))
			utils_message_add_error("Aucun ID d'administrateur à supprimer !");
		else {
			$other = $db->admin_delete(utils_s_get('id'));
			if($other) {
				utils_message_add_success("L'administrateur \"" . $other->username() . "\" a été supprimé.");
			} else
				utils_message_add_error("Erreur interne: impossible de supprimer l'administrateur ayant pour ID \"".utils_s_get('id')."\".");
		}
		break;
	default:
		break;
}
$administrators = $db->all_admins_but($admin->id());
?>
<div class="current-admin">
<h2>Gestion de votre compte</h2>
<form action="index.php?panel=users&action=changePassword" method="post">
	<fieldset>
		<legend>Modifier votre mot de passe</legend>
		<div class="table">
		<?php
		echo utils_input('Ancien mot de passe:', 'old-password', 'password');
		echo utils_input('Nouveau mot de passe:', 'new-password', 'password');
		echo utils_input('Nouveau mot de passe (une 2<sup>nde</sup> fois):', 'new-password-again', 'password');
		?>
		</div>
		<p><input type="submit" value="modifier le mot de passe"/></p>
	</fieldset>
</form>
</div>
<?php
if(!empty($administrators)) { ?>
<div class="other-admins">
<h2>Gestion des autres administrateurs</h2>
	<table cellspacing="0">
		<thead>
			<tr>
				<th>Pseudonyme</th>
				<th>Approbation</th>
				<th>Suppression</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach($administrators as $administrator) { ?>
			<tr>
				<td><?php echo $administrator->username();?></td>
				<td><?php if($administrator->approved()) { ?>
					<strong>approuvé</strong>
				<?php } else { ?>
					<a href="index.php?panel=users&action=approve&id=<?php echo $administrator->id();?>">approuver</a>
				<?php } ?></td>
				<td><a href="index.php?panel=users&action=delete&id=<?php echo $administrator->id();?>" onclick="return confirm('Voulez-vous vraiment supprimer l\'administrateur &quot;<?php echo $administrator->username();?>&quot; ?');" style="color:red;"><strong>supprimer</strong></a></td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
</div>
<?php } ?>