<?php
if(utils_has_s_get('id')) {
	$id = utils_s_get('id');
	if(ctype_digit($id)) {
		$db = new Database();
		$deleted = $db->model_delete($id);
		if($deleted)
			utils_message_add_success('Modèle supprimé.');
		else
			utils_message_add_error("Il n'existe aucun modèle ayant l'ID $id.");
		utils_request_redirection('index.php?panel=models');
	} else {
		utils_request_redirection('index.php');
	}
}
?>