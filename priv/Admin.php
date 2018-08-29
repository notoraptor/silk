<?php
class Admin {
	private $admin = array();
	public function __construct($data) {
		$this->admin = $data;
	}
	public function username() {
		return $this->admin['username'];
	}
	public function password() {
		return $this->admin['password'];
	}
	public function is_valid() {
		return $this->admin !== false;
	}
	public function approved() {
		return $this->admin['approved'] != '0';
	}
	public function id() {
		return $this->admin['admin_id'];
	}
}
?>