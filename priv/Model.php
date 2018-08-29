<?php
class Model {
	private $infos;
	public function __construct($data) {
		$this->infos = $data;
		$this->manage_date('date_added');
		if(isset($this->infos['model_id'])) {
			if($this->infos['first_name']) $this->infos['first_name'] = ucwords($this->infos['first_name']);
			if($this->infos['last_name']) $this->infos['last_name'] = ucwords($this->infos['last_name']);
			$photos = $this->portfolio();
			if (!(isset($this->infos['photo'])) && $this->infos['photo_1']) $this->infos['photo'] = $this->infos['photo_1'];
			if (!(isset($this->infos['photo'])) && $this->infos['photo_2']) $this->infos['photo'] = $this->infos['photo_2'];
			if (!(isset($this->infos['photo'])) && $this->infos['photo_3']) $this->infos['photo'] = $this->infos['photo_3'];
			if (!(isset($this->infos['photo'])) && $this->infos['photo_4']) $this->infos['photo'] = $this->infos['photo_4'];
			if (!isset($this->infos['photo']) || !$this->infos['photo'])
				$this->infos['photo'] = false;
		}
	}
	public function __get($key) {
		return $this->infos[$key];
	}
	public function toPOST() {
		$post = array();
		foreach($this->infos as $key => $value) {
			$post[str_replace('_','-',$key)] = $value;
		}
		if(!$this->infos['in_lifestyle'])
			unset($post['in-lifestyle']);
		return $post;
	}
	public function portfolio() {
		return isset($this->infos['model_id']) ? utils_local_photos(utils_model_portfolio_dir($this->infos['model_id'])) : false;
	}
	public function getPhotoByBasename($basename) {
		return isset($this->infos['model_id']) ? utils_local_photo(utils_model_portfolio_dir($this->infos['model_id']).'/'.$basename) : false;
	}
	public function getDetails() {
		$sexe = isset($this->infos['sex']) ? strtolower(trim($this->infos['sex'])) : 'femme';
		if(!$sexe) $sexe = 'femme';
		$titles = null;
		$order = null;
		if($sexe == 'femme') {
			// femme
			$titles = array(
				'hauteur' => 'HEIGHT',
				'taille_poitrine' => 'BUST',
				'taille' => 'WAIST',
				'taille_hanches' => 'HIPS',
				'taille_chaussures' => 'SHOES',
				'couleur_cheveux' => 'HAIR',
				'couleur_yeux' => 'EYES'
			);
			$order = array('hauteur', 'taille_poitrine', 'taille', 'taille_hanches', 'taille_chaussures', 'couleur_cheveux', 'couleur_yeux');
		} else {
			// homme
			/* height, suit, waist, shoe, hair, eyes */
			$titles = array(
				'hauteur' => 'HEIGHT',
				'taille_veston' => 'SUIT',
				'taille' => 'WAIST',
				'taille_chaussures' => 'SHOES',
				'couleur_cheveux' => 'HAIR',
				'couleur_yeux' => 'EYES'
			);
			$order = array('hauteur', 'taille_veston', 'taille', 'taille_chaussures', 'couleur_cheveux', 'couleur_yeux');
		}
		return array('titles' => $titles, 'order' => $order);
	}
	private function manage_date($name) {
		if(array_key_exists($name, $this->infos)) {
			if(!$this->infos[$name])
				$this->infos[$name] = '1900-01-01';
			$pieces = preg_split('/[^0-9]+/', $this->infos[$name]);
			if(count($pieces) == 3) {
				$year = utils_get_integer($pieces[0]);
				$month = utils_get_integer($pieces[1]);
				$day = utils_get_integer($pieces[2]);
				if($year && $month && $day) {
					$this->infos[$name.'_year'] = $year;
					$this->infos[$name.'_month'] = $month;
					$this->infos[$name.'_day'] = $day;
				}
			};
		}
	}
}

function utils_compcard_path($id) {
	return utils_model_dir($id).'/compcard.pdf';
}
function utils_model_dir($id) {
	return server_dir().'/data/model/'.$id;
}
function utils_model_portfolio_dir($id) {
	return server_dir().'/data/model/'.$id;
}
function utils_model_polaroid_dir($id) {
	return server_dir().'/data/model/'.$id.'/polaroid';
}
?>