<?php
/*
Merci à: https://lmorel3.fr/2013/08/recuperer-lid-dune-video-dailymotion-youtube-ou-vimeo-via-son-lien-en-php/
(Page consultée le 20 Juillet 2016 à 16h24 heure de l'Est (GMT-5))
*/
require_once(__DIR__.'/php/Utils.php');
class Video {
	// Détection de l'ID.
	static public function parse($link) {
		//DAILYMOTION
		preg_match('#<object[^>]+>.+?http://www.dailymotion.com/swf/video/([A-Za-z0-9]+).+?</object>#s', $link, $matches);
		if(!isset($matches[1])) {
			preg_match('#http://www.dailymotion.com/video/([A-Za-z0-9]+)#s', $link, $matches);
			if(!isset($matches[1])) {
				preg_match('#http://www.dailymotion.com/embed/video/([A-Za-z0-9]+)#s', $link, $matches);
				if(isset($matches[1]) && strlen($matches[1])) {
					return array('dailymotion', $matches[1]);
				}
			} else if(strlen($matches[1])) {
					return array('dailymotion', $matches[1]);
			}
		} else if(strlen($matches[1])) {
			return array('dailymotion', $matches[1]);
		}
		//YOUTUBE
		if(preg_match('#(?<=(?:v|i)=)[a-zA-Z0-9-]+(?=&)|(?<=(?:v|i)/)[^&n]+|(?<=embed/)[^"&n]+|(?<=(?:v|i)=)[^&n]+|(?<=youtu.be/)[^&n]+#', $link, $videoid)) {
			if(strlen($videoid[0])) {
				return array('youtube', $videoid[0]);
			}
		}
		//VIMEO
		if(preg_match('#(https?://)?(www.)?(player.)?vimeo.com/([a-z]*/)*([0-9]{6,11})[?]?.*#', $link, $videoid)) {
			if(strlen($videoid[5])) {
				return array('vimeo', $videoid[5]);
			}
		}
		return false;
	}
	// Affichage de la vidéo.
	static public function getCode($exp) {
		$html = "";
		switch ($exp[0]) {
			case 'youtube':
				$html = '<iframe class="killvideo" src="//www.youtube.com/embed/'.$exp[1].'" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowfullscreen></iframe>';
				break;
			case 'vimeo':
				$html = '<iframe class="killvideo" src="http://player.vimeo.com/video/'.$exp[1].'" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
				break;
			case 'dailymotion':
				$html = '<iframe class="killvideo" src="http://www.dailymotion.com/embed/video/'.$exp[1].'" frameborder="0" allowfullscreen></iframe>';
				break;
			default:
				break;
		}
		return $html;
	}
	static public function getHomeCode($exp) {
		$html = "";
		switch ($exp[0]) {
			case 'youtube':
				$html = '<div id="youtubeVideo"></div>';
				break;
			case 'vimeo':
				$html = '<iframe class="killvideo" src="http://player.vimeo.com/video/'.$exp[1].'?autoplay=1" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
				break;
			case 'dailymotion':
				$html = '<div id="dailymotionVideo"></div>';
				break;
			default:
				break;
		}
		return $html;
	}
	static public function oEmbed($infos) {
		$data = null;
		$endpoint = null;
		$embed = null;
		switch($infos[0]) {
			case 'vimeo':
				$url = 'http://vimeo.com/'.$infos[1];
				$endpoint = 'http://vimeo.com/api/oembed.json?url='.rawurlencode($url).'&autoplay=true';
				$embed = '<iframe class="killvideo" src="http://player.vimeo.com/video/'.$infos[1].'?autoplay=1" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
				break;
			case 'youtube':
				$url = 'https://www.youtube.com/watch?v='.$infos[1];
				$endpoint = 'http://www.youtube.com/oembed?url='.rawurlencode($url).'&autoplay=true&format=json';
				$embed = '<iframe class="killvideo" src="//www.youtube.com/embed/'.$infos[1].'?autoplay=1" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowfullscreen></iframe>';
				break;
			case 'dailymotion':
				$url = 'http://www.dailymotion.com/video/'.$infos[1];
				$endpoint = 'http://www.dailymotion.com/services/oembed?url='.rawurlencode($url).'&autoplay=true';
				$embed = '<iframe class="killvideo" src="http://www.dailymotion.com/embed/video/'.$infos[1].'?autoplay=1" frameborder="0" allowfullscreen></iframe>';
				break;
			default:break;
		}
		if($endpoint) {
			$file = Utils::curl_get($endpoint);
			if($file) {
				$content = json_decode($file);
				if($content) {
					$keys = array('title', 'author_name', 'html', 'thumbnail_url', 'thumbnail_width', 'thumbnail_height');
					$data = array();
					foreach($keys as $key) $data[$key] = $content->$key;
					$data['embed'] = $embed;
				}
			}
		}
		return $data;
	}
}

/*
$link = "http://www.dailymotion.com/video/x458bdl_chroma-s01-04-signes_shortfilms";
$link = "https://www.youtube.com/watch?v=KN9gceK0N_k";
$link = "https://youtu.be/KN9gceK0N_k";
$link = "https://vimeo.com/116330406";
$infos = parse($link);
if($infos) echo getCode($infos);
*/
?>