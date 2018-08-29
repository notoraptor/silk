<?php
function addMissingInstagram($url) {
	if(strstr($url, '?taken-by'))
		$url = explode('?taken-by', $url)[0];
	$caller = 'https://api.instagram.com/oembed?url=' . $url;
	$result = @file_get_contents($caller);
	if($result && ($result = json_decode($result))) {
		$matches = array();
		if(preg_match("#datetime *= *\"([^\"]*)\"#", $result->html, $matches)) {
			$createdTime = strtotime($matches[1]);
			if($createdTime !== false) {
				$photo = array(
					'instagram' => true,
					'path' => $url,
					'url' => $result->thumbnail_url,
					'width' => intval($result->thumbnail_width),
					'height' => intval($result->thumbnail_height),
					'time' => bcmul($createdTime, 1000000),
					'date' => date('d/m/Y', $createdTime)
				);
				$instagramDir = realpath(__DIR__ . '/../../data/photo/instagram');
				if(file_exists($instagramDir) && is_dir($instagramDir)) {
					$filename = $instagramDir.'/'.$photo['time'].'.instagram';
					if(!file_exists($filename)) {
						$content = '';
						foreach($photo as $key => $value) $content .= $key."\t".$value."\r\n";
						@file_put_contents($filename, $content);
					};
					// error_log('instagram-photo-added');
					return $photo;
				}
			}
		}
	}
	return null;
}
?>