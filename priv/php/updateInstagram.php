<?php
// Update Instagram Photos.
require_once(__DIR__ . '/../utils_instagram.php');
$url = 'http://killmanagement.com';
$lastPhotos = null;
try {
	$lastPhotos = utils_instagram_photos($url);
} catch(Exception $ignored) {
	echo $ignored;
}
if($lastPhotos) {
	$message = '';
	$instagramDir = realpath(__DIR__ . '/../../data/photo/instagram');
	if(isset($argv[1]) && $argv[1] == 'checkOld') {
		$removed = 0;
		$toIgnore = array('.' => true, '..' => true);
		foreach($lastPhotos as $lastPhoto) $toIgnore[$lastPhoto['time'].'.instagram'] = true;
		foreach(scandir($instagramDir) as $fileToCheck) if(!isset($toIgnore[$fileToCheck])) {
			$toVerify = explode('.', $fileToCheck);
			//if(preg_match("/^[0-9]+\.instagram$/", $fileToCheck)) {
			if(count($toVerify) == 2 && ctype_digit($toVerify[0]) && $toVerify[1] == 'instagram') {
				echo "$fileToCheck\r\n";
				$filePath = $instagramDir.'/'.$fileToCheck;
				$content = @file_get_contents($filePath);
				if($content) {
					$lines = explode("\r\n", $content);
					$info = array();
					foreach($lines as $line) {
						$pieces = explode("\t", $line, 2);
						if (count($pieces) === 2)
							$info[$pieces[0]] = $pieces[1];
					}
					if (isset($info["path"])) {
						$path_url = $info["path"];
						$headers = get_headers($path_url);
						if(!$headers || strstr($headers[0], '404')) {
							$removed += @unlink($filePath) ? 1 : 0;
							echo "\tBad\r\n";
						} else if (isset($info["url"])) {
                            $to_remove = false;
                            $headers = get_headers($info["url"]);
							if(!$headers || !strstr($headers[0], '200')) {
								// Try another solution, thanks to (2018/08/19):  https://stackoverflow.com/a/49524165
								$to_remove = true;
								$json_string = file_get_contents("http://api.instagram.com/oembed/?url=".$info["path"]);
								if ($json_string) {
									$json_content = json_decode($json_string);
									if (isset($json_content->thumbnail_url)) {
										$info["url"] = $json_content->thumbnail_url;
										$new_content = '';
										foreach($info as $key => $value) $new_content .= $key."\t".$value."\r\n";
										@file_put_contents($filePath, $new_content);
										$message .= ' !'.$lastPhoto['time'];
										$to_remove = false;
										echo "\tUpdated\r\n";
									}
								}
							}
							if ($to_remove) {
								$removed += @unlink($filePath) ? 1 : 0;
								echo "\tBad\r\n";
							}
						}
					}
				}
			}
		}
		$message .= ' -'.$removed;
	}
	if(file_exists($instagramDir) && is_dir($instagramDir)) {
		foreach($lastPhotos as &$lastPhoto) {
			$filename = $instagramDir.'/'.$lastPhoto['time'].'.instagram';
			if(!file_exists($filename)) {
				$content = '';
				foreach($lastPhoto as $key => $value) $content .= $key."\t".$value."\r\n";
				@file_put_contents($filename, $content);
			} else {
				$content = @file_get_contents($filename);
				if ($content) {
					$lines = explode("\r\n", $content);
					foreach($lines as $line) if(strpos($line, "url\t") === 0) {
						$photo_url = explode("\t", $line, 2)[1];
						if ($lastPhoto['url'] != $photo_url) {
							$new_content = '';
							foreach($lastPhoto as $key => $value) $new_content .= $key."\t".$value."\r\n";
							@file_put_contents($filename, $new_content);
							$message .= ' !'.$lastPhoto['time'];
						}
					}
				}
			}
		}
		file_put_contents(__DIR__ .'/instacron.log', date('d/m/Y H:i:s', time())."$message\r\n",  FILE_APPEND);
	}
}

?>