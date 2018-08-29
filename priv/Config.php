<?php
class Config {
	public $home_video_link;
	public $about_page_text;
	public $submission_page_text;
	public $submission_page_data_sharing_text;
	public $site_email;
	public function __construct($data) {
		$this->home_video_link = $data['home_video_link'];
		$this->about_page_text = $data['about_page_text'];
		$this->submission_page_text = $data['submission_page_text'];
		$this->submission_page_data_sharing_text = $data['submission_page_data_sharing_text'];
		$this->site_email = $data['site_email'];
	}
}
?>

