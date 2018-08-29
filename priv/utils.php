<?php
require_once(server_dir().'/priv/password.php');
require_once(server_dir().'/priv/Set.php');
require_once(server_dir().'/priv/Data.php');
require_once(server_dir().'/priv/FrontData.php');
require_once(server_dir().'/priv/Admin.php');
require_once(server_dir().'/priv/Config.php');
require_once(server_dir().'/priv/Contact.php');
require_once(server_dir().'/priv/Model.php');

class Database {
	// Dossiers de la BDD sur disque.
	private $dir_db = null;
	private $dir_db_main = null;
	private $dir_db_model = null;
	//.
	private $requetes_tables = array();
	private $bdd = null;
	public function __construct() {
		try {
			$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
			$this->bdd = new PDO('mysql:host='.NOM_HOTE.';dbname='.NOM_BASE, NOM_UTILISATEUR, MOT_DE_PASSE, $pdo_options);
			$this->verifier_existence_tables();
			$this->verifier_existence_bdd_sur_disque();
		} catch(Exception $e) {
			$this->throw_exception($e, 'Erreur');
		}
	}
	private function secure_query($requete, $parametres = array()) {
		try {
			$execution = $this->bdd->prepare($requete);
			$execution->execute($parametres);
			$donnees = array();
			while(($ligne = $execution->fetch())) {
				$donnees_ligne = array();
				foreach($ligne as $key => $value) {
					if(is_string($value)) $value = utils_unescape($value);	//revoir eventuellement.
					if(!is_int($key)) $donnees_ligne[$key] = $value;
				}
				$donnees[] = $donnees_ligne;
			}
			$execution->closeCursor();
			return $donnees;
		} catch(Exception $e) {
			$this->throw_exception($e, 'Erreur pendant une requ&ecirc;te de s&eacute;lection');
		}
	}
	private function oneResult($requete, $parametres = array()) {
		$donnees = $this->secure_query($requete, $parametres);
		return count($donnees) == 1 ? $donnees[0] : false;
	}
	private function secure_modif($requete, $parametres = array()) {
		try {
			$execution = $this->bdd->prepare($requete);
			$execution->execute($parametres);
		} catch(Exception $e) {
			$this->throw_exception($e, 'Erreur pendant une requ&ecirc;te de modification');
		}
	}
	private function throw_exception(Exception &$e, $prefix = '') {
		throw new Exception( ($prefix == '' ? '' : $prefix . ': ') . $e->getMessage() );
	}
	private function verifier_existence_tables() {
		$this->requetes_tables = array(
			'CREATE TABLE IF NOT EXISTS '.DB_PREFIX.'admin ('.
				'admin_id INT UNSIGNED NOT NULL AUTO_INCREMENT,'.
				'username VARCHAR (255) NOT NULL UNIQUE,'.
				'password VARCHAR (255) NOT NULL,'.
				'approved TINYINT NOT NULL DEFAULT 0,'.
				'PRIMARY KEY (admin_id)'.
			') ENGINE = INNODB;',
			'CREATE TABLE IF NOT EXISTS '.DB_PREFIX.'configuration ('.
				'config_id INT UNSIGNED NOT NULL AUTO_INCREMENT,'.
				'home_video_link VARCHAR (255),'.
				'about_page_text TEXT,'.
				'submission_page_text TEXT,'.
				'submission_page_data_sharing_text TEXT,'.
				'site_email TEXT,'.
				'PRIMARY KEY (config_id)'.
			') ENGINE = INNODB;',
			'CREATE TABLE IF NOT EXISTS '.DB_PREFIX.'model ('.
				'model_id INT UNSIGNED NOT NULL AUTO_INCREMENT,'.
				'in_lifestyle TINYINT NOT NULL DEFAULT 0,'.
				'instagram_link VARCHAR (255),'.
				'sex VARCHAR(255),'.
				'first_name VARCHAR(255) NOT NULL,'.
				'last_name VARCHAR(255) NOT NULL,'.
				'address VARCHAR(512),'.
				'height VARCHAR(255),'.
				'bust VARCHAR(255),'.
				'waist VARCHAR(255),'.
				'hips VARCHAR(255),'.
				'shoes VARCHAR(255),'.
				'hair VARCHAR(255),'.
				'eyes VARCHAR(255),'.
				'date_added DATE,'.
				'PRIMARY KEY (model_id)'.
			') ENGINE = INNODB;',
			'CREATE TABLE IF NOT EXISTS '.DB_PREFIX.'model_photo ('.
				'model_id INT UNSIGNED NOT NULL,'.
				'photo_1 VARCHAR(255) NOT NULL,'.
				'photo_2 VARCHAR(255) NOT NULL,'.
				'photo_3 VARCHAR(255) NOT NULL,'.
				'photo_4 VARCHAR(255) NOT NULL,'.
				'PRIMARY KEY (model_id),'.
				'CONSTRAINT fk_model_photos FOREIGN KEY (model_id) REFERENCES model (model_id) ON DELETE CASCADE'.
			') ENGINE = INNODB;',
		);
		$compte = count($this->requetes_tables);
		for($i = 0; $i < $compte; ++$i) $this->secure_modif($this->requetes_tables[$i]);
		$this->alterer_tables();
		$this->autres_verifications();
	}
	private function alterer_tables() {
		$alterations = array(
			array(
				//'SHOW COLUMNS FROM '.DB_PREFIX.'produits LIKE \'prix_de_base\'',
				//'ALTER TABLE '.DB_PREFIX.'produits ADD prix_de_base DECIMAL(14,2) DEFAULT 0',
			)
		);
		$compte = count($alterations);
		for($i = 0; $i < $compte; ++$i) {
			$alteration = $alterations[$i];
			switch(count($alteration)) {
				case 1:
					$this->secure_modif($alteration[0]);
					break;
				case 2:
					$donnees = $this->secure_query($alteration[0]);
					if(count($donnees) == 0) $this->secure_modif($alteration[1]);
					break;
				default:break;
			}
		}
	}
	private function autres_verifications() {
		$data = $this->secure_query('SELECT COUNT(config_id) AS count FROM '.DB_PREFIX.'configuration');
		if($data[0]['count'] == 0)
			$this->secure_modif('INSERT INTO '.DB_PREFIX.'configuration (config_id) VALUES(1)');
	}
	private function verifier_existence_bdd_sur_disque() {
		$this->dir_db = server_dir() . '/data';
		$this->dir_db_main  = $this->dir_db . '/main';
		$this->dir_db_model = $this->dir_db . '/model';
		$folders = array(
			$this->dir_db, $this->dir_db_main, $this->dir_db_model
		);
		$countFolders = count($folders);
		for($i = 0; $i < $countFolders; ++$i) {
			if(!file_exists($folders[$i]))
				mkdir($folders[$i]);
			if(!file_exists($folders[$i]) || !is_dir($folders[$i]))
				throw new Exception('Unable to create database folder: '.$folders[$i]);
		}
	}
	// Méthodes.
	public function admin($id) {
		return $this->oneResult('SELECT admin_id, username, password, approved FROM '.DB_PREFIX.'admin WHERE admin_id = ?', array($id));
	}
	public function admin_login($username, $password) {
		$data = $this->oneResult('SELECT admin_id, username, password, approved FROM '.DB_PREFIX.'admin WHERE username = ?', array($username));
		return $data && password_verify($password, $data['password']) ? new Admin($data) : false;
	}
	public function admin_create($username, $password) {
		$data = $this->oneResult('SELECT admin_id FROM '.DB_PREFIX.'admin WHERE username = ?', array($username));
		if($data) return false;
		$data = $this->oneResult('SELECT COUNT(admin_id) AS count FROM '.DB_PREFIX.'admin');
		$approved = $data['count'] == 0 ? 1 : 0;
		$this->secure_modif('INSERT INTO '.DB_PREFIX.'admin (username, password, approved) VALUES(?,?,?)', array(
			$username, password_hash($password, PASSWORD_DEFAULT), $approved
		));
		$data = $this->oneResult('SELECT admin_id, username, password, approved FROM '.DB_PREFIX.'admin WHERE username = ?', array($username));
		return new Admin($data);
	}
	public function admin_update($username, $oldPassword, $newPassword) {
		$data = $this->oneResult('SELECT password FROM '.DB_PREFIX.'admin WHERE username = ?', array($username));
		if(!$data || !password_verify($oldPassword, $data['password'])) return false;
		$this->secure_modif('UPDATE '.DB_PREFIX.'admin SET password = ? WHERE username = ?', array(
			password_hash($newPassword, PASSWORD_DEFAULT), $username
		));
		return new Admin($this->oneResult('SELECT admin_id, username, password, approved FROM '.DB_PREFIX.'admin WHERE username = ?', array($username)));
	}
	public function admin_approve($id) {
		$data = $this->oneResult('SELECT admin_id, username, password, approved FROM '.DB_PREFIX.'admin WHERE admin_id = ?', array($id));
		if(!$data) return false;
		$this->secure_modif('UPDATE '.DB_PREFIX.'admin SET approved = 1 WHERE admin_id = ?', array($id));
		$data['approved'] = 1;
		return new Admin($data);
	}
	public function admin_delete($id) {
		$data = $this->oneResult('SELECT admin_id, username, password, approved FROM '.DB_PREFIX.'admin WHERE admin_id = ?', array($id));
		if(!$data) return false;
		$this->secure_modif('DELETE FROM '.DB_PREFIX.'admin WHERE admin_id = ?', array($id));
		return new Admin($data);
	}
	public function all_admins_but($id) {
		$rows = $this->secure_query('SELECT admin_id, username, password, approved FROM '.DB_PREFIX.'admin WHERE NOT admin_id = ?', array($id));
		$administrators = array();
		foreach($rows as $row) $administrators[] = new Admin($row);
		return $administrators;
	}
	public function config() {
		$data = $this->oneResult('SELECT home_video_link, about_page_text, submission_page_text, submission_page_data_sharing_text, site_email FROM '.DB_PREFIX.'configuration');
		return $data ? new Config($data) : false;
	}
	public function config_update($config_dict) {
		$this->secure_modif(
			'UPDATE '.DB_PREFIX.'configuration SET home_video_link = ?, about_page_text = ?, submission_page_text = ?, submission_page_data_sharing_text = ?, site_email = ?',
			array($config_dict['home_video_link'], $config_dict['about_page_text'], $config_dict['submission_page_text'], $config_dict['submission_page_data_sharing_text'], $config_dict['site_email'])
		);
	}
	public function model_exists($id) {
		return $this->oneResult('SELECT model_id FROM '.DB_PREFIX.'model WHERE model_id = ?', array($id));
	}
	public function model($id) {
		$data = $this->oneResult(
			'SELECT 
				m.model_id AS model_id,
				m.in_lifestyle AS in_lifestyle,
				m.instagram_link AS instagram_link,
				m.sex AS sex,
				m.first_name AS first_name,
				m.last_name AS last_name,
				m.address AS address,
				m.height AS height,
				m.bust AS bust,
				m.waist AS waist,
				m.hips AS hips,
				m.shoes AS shoes,
				m.hair AS hair,
				m.eyes AS eyes,
				m.date_added AS date_added,
				p.photo_1 AS photo_1,
				p.photo_2 AS photo_2,
				p.photo_3 AS photo_3,
				p.photo_4 AS photo_4
			FROM '.DB_PREFIX.'model AS m
			LEFT JOIN '.DB_PREFIX.'model_photo AS p ON m.model_id = p.model_id
			WHERE m.model_id = ?',
			array($id)
		);
		if(!$data) return false;
		return new Model($data);
	}
	public function model_update($id, $fields) {
		$keys = array_keys($fields);
		$keysForDB = array();
		$values = array();
		$count = count($keys);
		for($i = 0; $i < $count; ++$i) {
			$keysForDB[] = $keys[$i] . '= ?';
			$values[] = $fields[$keys[$i]];
		}
		$values[] = $id;
		$this->secure_modif('UPDATE '.DB_PREFIX.'model SET '.(implode(',', $keysForDB)).' WHERE model_id = ?', $values);
		return $this->model($id);
	}
	public function model_photo_update($model_id, $photo_rank, $photo_name) {
		if(!in_array($photo_rank, array(1, 2, 3, 4))) return false;
		$data = $this->secure_query('SELECT model_id FROM '.DB_PREFIX.'model_photo WHERE model_id = ?', array($model_id));
		if($data)
			$this->secure_modif('UPDATE '.DB_PREFIX.'model_photo SET photo_'.$photo_rank.' = ? WHERE model_id = ?', array($photo_name, $model_id));
		else {
			$photo_values = array(
				'photo_1' => '',
				'photo_2' => '',
				'photo_3' => '',
				'photo_4' => ''
			);
			$photo_values['photo_'.$photo_rank] = $photo_name;
			$this->secure_modif('INSERT INTO ' . DB_PREFIX . 'model_photo (model_id, photo_1, photo_2, photo_3, photo_4) VALUES(?,?,?,?,?)',
				array($model_id, $photo_values['photo_1'], $photo_values['photo_2'], $photo_values['photo_3'], $photo_values['photo_4']));
		}
		return true;
	}
	public function models() {
		$data = $this->secure_query(
			'SELECT 
				m.model_id AS model_id,
				m.in_lifestyle AS in_lifestyle,
				m.instagram_link AS instagram_link,
				m.sex AS sex,
				m.first_name AS first_name,
				m.last_name AS last_name,
				m.address AS address,
				m.height AS height,
				m.bust AS bust,
				m.waist AS waist,
				m.hips AS hips,
				m.shoes AS shoes,
				m.hair AS hair,
				m.eyes AS eyes,
				m.date_added AS date_added,
				p.photo_1 AS photo_1,
				p.photo_2 AS photo_2,
				p.photo_3 AS photo_3,
				p.photo_4 AS photo_4
			FROM '.DB_PREFIX.'model AS m
			LEFT JOIN '.DB_PREFIX.'model_photo AS p ON m.model_id = p.model_id
			ORDER BY m.model_id ASC'
		);
		$models = array();
		// Vidéos.
		$countModels = count($data);
		for($i = 0; $i < $countModels; ++$i) {
			$models[] = new Model($data[$i]);
		}
		return $models;
	}
	public function model_create($mainValues) {
		$data = $this->secure_query('SELECT model_id FROM '.DB_PREFIX.'model WHERE first_name = ? AND last_name = ?', array($mainValues['first_name'], $mainValues['last_name']));
		if(!empty($data)) return false;
		$this->secure_modif('INSERT INTO '.DB_PREFIX.'model (in_lifestyle, instagram_link, sex, first_name, last_name, height, bust, waist, hips, shoes, hair, eyes) VALUES(?,?,?,?,?,?,?,?,?,?,?,?)',
			array($mainValues['in_lifestyle'], $mainValues['instagram_link'], $mainValues['sex'], $mainValues['first_name'], $mainValues['last_name'], $mainValues['height'], $mainValues['bust'], $mainValues['waist'], $mainValues['hips'], $mainValues['shoes'], $mainValues['hair'], $mainValues['eyes']));
		$data = $this->oneResult('SELECT model_id FROM '.DB_PREFIX.'model WHERE first_name = ? AND last_name = ?', array($mainValues['first_name'], $mainValues['last_name']));
		mkdir(utils_model_dir($data['model_id']));
		return $this->model($data['model_id']);
	}
	public function model_delete($id) {
		$this->secure_modif('DELETE FROM '.DB_PREFIX.'model WHERE model_id = ?', array($id));
		delTree(utils_model_dir($id));
		return true;
	}
	public function list_hairs() {
		$data = $this->secure_query('SELECT hair FROM '.DB_PREFIX.'model');
		$set = new Set(array());
		foreach($data as $row) $set->add($row['hair']);
		$set->add(array('black', 'brown', 'blond', 'auburn', 'chestnut', 'red', 'gray', 'white'));
		return $set;
	}
	public function list_eyes() {
		$data = $this->secure_query('SELECT eyes FROM '.DB_PREFIX.'model');
		$set = new Set(array());
		foreach($data as $row) $set->add($row['eyes']);
		$set->add(array('black', 'amber', 'blue', 'brown', 'gray', 'green', 'hazel', 'red', 'violet'));
		return $set;
	}
	public function list_sex() {
		$data = $this->secure_query('SELECT sex FROM '.DB_PREFIX.'model');
		$set = new Set(array());
		foreach($data as $row) $set->add($row['sex']);
		$set->add(array('homme', 'femme'));
		return $set;
	}
}

function utils_message_add_error($msg) {$_SESSION['messages']['errors'][] = $msg;}
function utils_message_add_attention($msg) {$_SESSION['messages']['attentions'][] = $msg;}
function utils_message_add_success($msg) {$_SESSION['messages']['successes'][] = $msg; return true;}
function utils_redirection($chemin) {
	header('Location: '.$chemin);
	exit();
}
function utils_request_redirection($link) {$GLOBALS['redirection'] = $link;}
function utils_has_redirection() {return isset($GLOBALS['redirection']);}
function utils_execute_redirection() {
	if(utils_has_redirection()) {
		$the_redirection = $GLOBALS['redirection'];
		unset($GLOBALS['redirection']);
		header('Location: '.$the_redirection);
	}
}

function utils_safe_string($s) {
	//$chaine = htmlentities(trim($s), ENT_NOQUOTES, 'ISO-8859-1');
	$chaine = htmlentities(trim($s), ENT_NOQUOTES, 'UTF-8');
	$chaine = str_replace("'","-",$chaine);
	$chaine = str_replace('"',"-",$chaine);
	$chaine = str_replace('$',"-",$chaine);
	$chaine = str_replace("&amp;","&",$chaine);
	$chaine = preg_replace("/&([A-Za-z])(acute|cedil|circ|grave|orn|ring|slash|th|tilde|uml);/","$1",$chaine);
	$chaine = preg_replace("/&([A-Za-z]{2})(lig);/","$1",$chaine);
	$chaine = str_replace("&","-et-",$chaine);
	$chaine = preg_replace("/&[^;]+;/","",$chaine);
	$chaine = preg_replace("/[^A-Za-z0-9-]/","-",$chaine);
	$chaine = preg_replace("/-+/","-",$chaine);
	$chaine = trim($chaine,"-");
	$chaine = strtolower($chaine);
	return $chaine;
};

function utils_valid_url($url) {return !filter_var($url, FILTER_VALIDATE_URL) === false;}
function utils_valid_email($url) {return !filter_var($url, FILTER_VALIDATE_EMAIL) === false;}
function utils_valid_username($username) {return preg_match("/^[A-Za-z0-9]{5,}$/", $username);}
function utils_valid_password($password) {
	return preg_match("/^[A-Za-z0-9]{8,}$/", $password)
		&& preg_match("/[A-Z]/", $password)
		&& preg_match("/[0-9]/",$password)
		&& preg_match("/[a-z]/", $password);
}
function utils_password_error($newPassword = false) {
	return "Votre ".($newPassword ? 'NOUVEAU ' : '')."mot de passe doit comporter au moins 8 caractères composés de chiffres (au moins 1), lettres majuscules (au moins 1) et lettres minuscules (au moins 1) non accentuées de l'alphabet latin.";
}
function utils_username_error() {
	return "Votre pseudonyme doit comporter au moins 5 caractères composés de chiffres et lettres (majuscules ou minuscules) non accentuées de l'alphabet latin.";
}

function utils_posted($name) {
	if(utils_has_s_post($name) && utils_s_post($name)) return ' value="'.htmlentities(utils_safe_post($name)).'"';
	return '';
}
function utils_input($title, $name, $type = 'text', $others = '', $help = '') {
	return '<div class="row">'.
		'<div class="cell name"><label for="'.$name.'">'.$title.'</label></div>'.
		'<div class="cell value"><input type="'.$type.'" id="'.$name.'" name="'.$name.'"'.utils_posted($name).' '.$others.'/>'.($help == '' ? '' : ' <span class="help">'.$help.'</span>').'</div>'.
	'</div>';
}
function utils_required_input($title, $name, $type = 'text', $others = '', $help = '') {
	return utils_input($title.' <span class="required">*</span>', $name, $type, $others, $help);
}
function input_text($title, $name, $others = '', $help = '') {
	return utils_input($title, $name, 'text', $others, $help);
}
function input_url($title, $name, $others = '', $help = '') {
	return utils_input($title, $name, 'url', $others, $help);
}
function input_password($title, $name, $others = '', $help = '') {
	return utils_input($title, $name, 'password', $others, $help);
}
function utils_textarea($title, $name, $others = '', $help = '') {
	return '<div class="row">'.
		'<div class="cell name"><label for="'.$name.'">'.$title.'</label></div>'.
		'<div class="cell value"><textarea'.(utils_s_post($name, '') !== '' ? ' class="inputed"': '').' id="'.$name.'" name="'.$name.'" '.$others.'>'.(utils_has_s_post($name) ? htmlentities(utils_safe_post($name)) : '').'</textarea>'.($help == '' ? '' : ' <span class="help">'.$help.'</span>').'</div>'.
	'</div>';
}
function utils_checkbox($title, $name) {
	return '<div class="row">'.
		'<div class="cell name"><label for="'.$name.'">'.$title.'</label></div>'.
		'<div class="cell value"><input type="checkbox" id="'.$name.'" name="'.$name.'"'.(utils_has_s_post($name) ? ' checked="checked"' : '').'/></div>'.
	'</div>';
}
function utils_date_input($title, $name, $y = 0, $m = 0, $d = 0) {
	$currentYear = intval(ltrim(date("Y"), '0'));
	if($d < 1 || $d > 31) $d = intval(date("j"));
	if($m < 1 || $m > 12) $m = intval(date("n"));
	if($y < 1850 || $y > $currentYear) $y = $currentYear;
	$selectDay = '<select id="'.$name.'" name="'.$name.'-day">';
		for($i = 1; $i <= 31; ++$i) $selectDay .= '<option value="'.$i.'"'.($d == $i ? ' selected="selected"' : '').'>'.$i.'</option>';
		$selectDay .= '</select>';
	$selectMonth = '<select name="'.$name.'-month">';
		for($i = 1; $i <= 12; ++$i) $selectMonth .= '<option value="'.$i.'"'.($m == $i ? ' selected="selected"' : '').'>'.$i.'</option>';
		$selectMonth .= '</select>';
	$selectYear = '<select name="'.$name.'-year">';
		for($i = min($y, $currentYear - 100); $i <= $currentYear; ++$i) $selectYear .= '<option value="'.$i.'"'.($y == $i ? ' selected="selected"' : '').'>'.$i.'</option>';
		$selectYear .= '</select>';
	return '<div class="row">'.
		'<div class="cell name"><label for="'.$name.'">'.$title.'</label></div>'.
		'<div class="cell value date">'."JJ $selectDay /MM $selectMonth /AAAA $selectYear".'</div>'.
	'</div>';
}
function utils_datalist($name, Set $set) {
	return '<datalist id="'.$name.'"><option value="'.implode('"/><option value="', $set->values()).'"/></datalist>';
}

function utils_get_integer($s) {return ctype_digit($s) ? intval(ltrim($s, '0')) : false;}
function utils_check_day($s) {
	$v = utils_get_integer($s);
	return $v === false ? false : ($v >= 1 && $v <= 31);
}
function utils_check_month($s) {
	$v = utils_get_integer($s);
	return $v === false ? false : ($v >= 1 && $v <= 12);
}
function utils_check_year($s) {
	$v = utils_get_integer($s);
	$currentYear = intval(ltrim(date("Y"), '0'));
	return $v === false ? false : ($v >= 1850 && $v <= $currentYear);
}
function utils_get_date($y, $m, $d) {
	$y = $y.'';
	$m = $m.'';
	$d = $d.'';
	$strlen_y = strlen($y);
	$strlen_m = strlen($m);
	$strlen_d = strlen($d);
	if($strlen_y == 1) $y = '000'.$y;
	if($strlen_y == 2) $y = '00'.$y;
	if($strlen_y == 3) $y = '0'.$y;
	if($strlen_m == 1) $m = '0'.$m;
	if($strlen_d == 1) $d = '0'.$d;
	return $y.'-'.$m.'-'.$d;
}

function utils_unescape($texte) {
	$texte = str_replace("\\\"","\"",$texte);
	$texte = str_replace("\\'","'",$texte);
	return $texte;
}
function utils_unescape_s_post($texte) {
	$texte = utils_unescape($texte);
	$texte = str_replace("\\\\","\\",$texte);
	return $texte;
}
function utils_has_s_get($key) {
	return isset($_GET[$key]);
}
function utils_has_s_post($key) {
	return isset($_POST[$key]);
}
function utils_s_get($key, $default = '') {
	return trim(isset($_GET[$key]) ? $_GET[$key] : $default);
}
function utils_s_post($key, $default = '') {
	return isset($_POST[$key]) ? $_POST[$key] : $default;
}
function utils_safe_post($name, $alt = '') {
	return trim(utils_unescape_s_post(utils_s_post($name, $alt)));
}

function utils_microtime() {
	list($micro, $sec) = explode(" ", microtime());
	$realsec = bcmul($sec, "1000000");
	$realmicro = $micro * 1000000;
	return bcadd($realsec, $realmicro, 0);
}

function utils_upload($name, $updir) {
	// Testons si le fichier a bien été envoyé et s'il n'y a pas d'erreur
	if (isset($_FILES[$name]) AND $_FILES[$name]['error'] == UPLOAD_ERR_OK) {
		// Testons si le fichier n'est pas trop gros.
		$tailleMaximale = 64*1024*1024; // 64 Mo.
		if ($_FILES[$name]['size'] <= $tailleMaximale) {
			// Testons si l'extension est autorisée
			$infosfichier = pathinfo($_FILES[$name]['name']);
			$extension_upload = "";
			if(isset($infosfichier['extension'])) $extension_upload = strtolower($infosfichier['extension']);
			$extensions_autorisees = array('jpg', 'jpeg', 'gif', 'png', 'tif', 'bmp');
			if (in_array($extension_upload, $extensions_autorisees)) {
				// On peut valider le fichier et le stocker définitivement
				if($extension_upload == 'jpeg')	$extension_upload = 'jpg';
				else if($extension_upload == 'tif')	$extension_upload = 'tiff';
				if($extension_upload != '') $extension_upload = '.'.$extension_upload;
				$time = utils_microtime();
				$nom = $updir.'/'.$time.$extension_upload;
				if(move_uploaded_file($_FILES[$name]['tmp_name'], $nom))
					return $nom; //return str_replace(server_dir().'/','',$nom);
				else return false;
			} else utils_message_add_error( "Erreur lors de l'envoi de fichier : extension (.".$extension_upload.") non autoris&eacute;e.<br/>".( empty($extensions_autorisees) ? "" : "Les fichiers accept&eacute;s doivent avoir une des extensions suivantes :<br/>.".implode(', .',$extensions_autorisees)."<br/>" ).( empty($extensions_interdites) ? "" : "Les extensions suivantes sont refus&eacute;es :<br/>.".implode(', .', $extensions_interdites) ) );
		} else utils_message_add_error("Votre fichier est trop gros (".($tailleMaximale/1024/1024)." Mo au maximum).");
	} else utils_message_add_error("Erreur ".$_FILES[$name]['error']." lors de l'envoi de fichier : champ ($name) inexistant.");
	return false;
}
function utils_upload_compcard($model_id) {
	$name = 'compcard';
	// Testons si le fichier a bien été envoyé et s'il n'y a pas d'erreur
	if (isset($_FILES[$name]) AND $_FILES[$name]['error'] == UPLOAD_ERR_OK) {
		// Testons si le fichier n'est pas trop gros.
		$tailleMaximale = 64*1024*1024; // en Mo.
		if ($_FILES[$name]['size'] <= $tailleMaximale) {
			// Testons si l'extension est autorisée
			$infosfichier = pathinfo($_FILES[$name]['name']);
			$extension_upload = "";
			if(isset($infosfichier['extension'])) $extension_upload = strtolower($infosfichier['extension']);
			$extensions_autorisees = array('pdf');
			if (in_array($extension_upload, $extensions_autorisees)) {
				// On peut valider le fichier et le stocker définitivement
				$extension_upload = '.'.$extension_upload;
				$nom = utils_compcard_path($model_id);
				if(move_uploaded_file($_FILES[$name]['tmp_name'], $nom))
					return $nom; //return str_replace(server_dir().'/','',$nom);
				else return false;
			} else utils_message_add_error( "Erreur lors de l'envoi de fichier : extension (.".$extension_upload.") non autoris&eacute;e.<br/>".( empty($extensions_autorisees) ? "" : "Les fichiers accept&eacute;s doivent avoir une des extensions suivantes :<br/>.".implode(', .',$extensions_autorisees)."<br/>" ).( empty($extensions_interdites) ? "" : "Les extensions suivantes sont refus&eacute;es :<br/>.".implode(', .', $extensions_interdites) ) );
		} else utils_message_add_error("Votre fichier est trop gros (".($tailleMaximale/1024/1024)." Mo au maximum).");
	} else utils_message_add_error("Erreur ".$_FILES[$name]['error']." lors de l'envoi de fichier : champ ($name) inexistant.");
	return false;
}
function delTree($dir) {
	if(!file_exists($dir))
		return true;
	if(is_file($dir))
		return unlink($dir);
	$files = array_diff(scandir($dir), array('.','..'));
	foreach ($files as $file) {
		(is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
	}
	return rmdir($dir);
} 

function utils_local_photo($photopath) {
	if(is_file($photopath)) {
		$photoinfos = getimagesize($photopath);
		if($photoinfos) {
			$filename = pathinfo($photopath, PATHINFO_FILENAME);
			if (ctype_digit($filename)) {
				$time = $filename;
				return array(
					'path' => $photopath,
					'basename' => basename($photopath),
					'url' => str_replace(server_dir(),server_http(),$photopath),
					'width' => $photoinfos[0],
					'height' => $photoinfos[1],
					'time' => $time,
					'date' => date('d/m/Y', bcdiv($time, 1000000))
				);
			}
		}
	}
	return false;
}
function utils_local_photos($path) {
	if(file_exists($path) && is_dir($path)) {
		$list = scandir($path);
		$photos = array();
		foreach($list as $file) {
			$photopath = $path.'/'.$file;
			$photo = utils_local_photo($photopath);
			if($photo) $photos[] = $photo;
		}
		return $photos;
	}
	return false;
}

function capture_start() {
	ob_start();
}
function capture_end(&$content) {
	$content .= ob_get_contents();
	ob_end_clean();
}

function get_nb_followers($instagram_username) {
	$json_string = file_get_contents('https://www.instapi.io/u/'.$instagram_username);
	if ($json_string) {
		$json_content = json_decode($json_string);
		if (isset($json_content->graphql->user->edge_followed_by->count)) {
			$nb_followers = $json_content->graphql->user->edge_followed_by->count;
			$text = ''.$nb_followers;
			if ($nb_followers % 1000000 != $nb_followers)
				$text = round($nb_followers / 1000000., 1).'M';
			else if ($nb_followers % 1000 != $nb_followers)
				$text = round($nb_followers / 1000., 1).'K';
			return $text;
		}
	}
	return false;
}

?>