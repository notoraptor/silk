<?php
class Set {
	private $set = array();
	public function __construct($list) {
		$this->add($list);
	}
	public function add($list) {
		if(is_array($list)) foreach($list as $element) if($element != '') $this->set[$element] = null;
		else if($list != '') $this->set[$list] = null;
	}
	public function values() {
		$values = array_keys($this->set);
		sort($values);
		return $values;
	}
}
?>