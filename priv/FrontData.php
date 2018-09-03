<?php
class FrontData extends Data {
	public $pagename = '';
	public $config = null;
	public $models = null;
	private $db = null;
	public function __construct(&$db) {
		parent::__construct();
		$this->db = $db;
		$this->config = $this->db->config();
		$this->models = $this->db->models();
	}
	public function getConfig() {
		return $this->config;
	}
	public function details($model) {
		$details = $model->getDetails();
		$titles = $details['titles'];
		$keys = $details['order'];
		$count = count($keys);
		for($i = 0; $i < $count; ++$i) {
			$key = $keys[$i];
			if($model->$key) {
				echo '<div class="detail">
					<div class="value">'.$model->$key.'</div>
					<div class="name">'.$titles[$key].'</div>
				</div>';
			}
		}
	}
	public function horizontalDetails($model) {
		$details = $model->getDetails();
		$titles = $details['titles'];
		$keys = $details['order'];
		$html = '';
		$plus = '';
		$showed = 0;
		$count = count($keys);
		for($i = 0; $i < $count; ++$i) {
			//$key = $keys[$count - 1 - $i];
			$key = $keys[$i];
			if($model->$key) {
				++$showed;
				$html .= '<div class="detail">
					<div class="before">/</div>
					<div class="then">
					<div class="value">'.$model->$key.'</div>
					<div class="name">'.$titles[$key].'</div>
					</div>
				</div>';
			}
		}
		if($showed) $plus = '<div class="detail"><div class="before">/</div></div>';
		//echo $plus.$html;
		echo $html.$plus;
	}
	public function getMetaDescription($model, $extension = '') {
		$meta_description = $model->first_name . ' ' . $model->last_name . ' | Silk Management model' . ($extension ? ', '.$extension : '');
		$details = $model->getDetails();
		$titles = $details['titles'];
		$keys = $details['order'];
		$showed = array();
		$count = count($keys);
		for($i = 0; $i < $count; ++$i) {
			$key = $keys[$i];
			if($model->$key) {
				$showed[] = $titles[$key] .': '. $model->$key;
			}
		}
		return htmlentities(
			//str_replace('"',"''",
				(empty($showed) ? $meta_description : $meta_description . '. ' . implode(', ', $showed)) . '.'
			//)
		);
	}
	public function img($i) {
		if(count($this->models) > $i) {
			$model = &$this->models[$i];
			if($model->photo) {
				$photopath = server_dir().'/data/model/'.$model->model_id.'/portfolio/'.$model->photo;
				$photo = utils_local_photo($photopath);
				if($photo) echo ' style="background-image:url(\''.$photo['url'].'\');"';
			}
		}
	}
}
?>