<?php
require_once(__DIR__ .'/php/Utils.php');
require_once(__DIR__ .'/instalib/vendor/autoload.php');

function utils_update_api_infos($redirectUrl) {
	// Infos par défaut (mon compte Instagram).
	$apiInfos['client_id'] = '25cae6c6ef8d433a88fb34a1ec057195';
	$apiInfos['client_secret'] = '460cd24d6c44468b94381fa743aff064';
	$apiInfos['instagram_token'] = '3576730736.25cae6c.300e4d5cb10846eab583ce301f3f6328';
	// Infos du compte à afficher.
	$official_id_file = __DIR__ .'/instagram.id';
	$official_secret_file = __DIR__ .'/instagram.secret';
	$official_token_file = __DIR__ .'/instagram.token';
	$official_id = file_exists($official_id_file) ? file_get_contents($official_id_file) : false;
	$official_secret = file_exists($official_secret_file) ? file_get_contents($official_secret_file) : false;
	$official_token = file_exists($official_token_file) ? file_get_contents($official_token_file) : false;
	if($official_id && $official_secret && $official_token) {
		$apiInfos['client_id'] = $official_id;
		$apiInfos['client_secret'] = $official_secret;
		$apiInfos['instagram_token'] = $official_token;
	}
	if(Utils::valid_url($redirectUrl)) {
		$auth_config = array(
			'client_id'         => $apiInfos['client_id'],
			'client_secret'     => $apiInfos['client_secret'],
			'redirect_uri'      => $redirectUrl,
			'scope'             => array( 'basic' )
		);
		$auth = new Instagram\Auth( $auth_config );
		if(!isset($apiInfos['instagram_token'])) {
			if(!isset($_GET['code'])){
				$auth->authorize();
			} else {
				$access_token = $auth->getAccessToken($_GET['code']);
				$apiInfos['instagram_token'] = $access_token;
			}
		}
	}
	return isset($apiInfos['instagram_token']) ? $apiInfos['instagram_token'] : null;
}
function utils_instagram_photos($redirectUrl) {
	echo "here";
	if (true) {
		$access_token = utils_update_api_infos($redirectUrl);
		$instagram = new Instagram\Instagram;
		$instagram->setAccessToken( $access_token );
		$user = $instagram->getUserByUsername('killmanagement');
		echo "we are here\r\n";
		if ($user) {
			echo "we are here 2\r\n";
			print_r($user->getCounts());
		}
		return false;
	}
	if(Utils::valid_url($redirectUrl)) {
		$access_token = utils_update_api_infos($redirectUrl);
		$instagram = new Instagram\Instagram;
		$instagram->setAccessToken( $access_token );
		$currentUser = $instagram->getCurrentUser();
		$images = array();
		$photos = array();
		$collection = false;
		$run = true;
		$userCounts = $currentUser->getCounts();
		$mediaCount = $userCounts ? $userCounts->media : false;
		do {
			if($collection) {
				$nextPage = $collection->getNext();
				if($nextPage === null)
					$run = false;
				else {
					$collection = $currentUser->getMedia(
						array('max_id' => $nextPage)
					);
					foreach($collection as $media) if($media->getType() == 'image' || $media->getType() == 'video')
						$images[] = $media;
				}
			} else {
				$collection = $currentUser->getMedia( $mediaCount ? array('count' => $mediaCount) : null );
				foreach($collection as $media) if($media->getType() == 'image' || $media->getType() == 'video')
					$images[] = $media;
			}
		} while($run);
		foreach($images as $media) {
			$standardResImage = $media->getStandardResImage();
			$createdTime = $media->getCreatedTime();
			$link = $media->getLink();
			$photos[] = array(
				'instagram' => true,
				'path' => $link,
				'url' => $standardResImage->url,
				'width' => intval($standardResImage->width),
				'height' => intval($standardResImage->height),
				'time' => bcmul($createdTime, 1000000),
				'date' => date('d/m/Y', $createdTime)
			);
		}
		return $photos;
	}
	return false;
}

?>