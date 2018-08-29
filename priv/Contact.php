<?php
class Contact {
	public $about;
	public $email;
	public $address;
	public $phone;
	public $social;
	public function __construct($data) {
		$this->about = $data['contact_about'];
		$this->email = $data['contact_email'];
		$this->address = $data['contact_address'];
		$this->phone = $data['contact_phone'];
		$this->social = $data['contact_social_links'];
	}
}
?>