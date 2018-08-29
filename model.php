<?php
require_once('server_infos.php');
require_once('priv/utils.php');
require_once('priv/template.php');
if(!isset($_SERVER['QUERY_STRING'])) utils_redirection('index.php');
$id = trim($_SERVER['QUERY_STRING']);
if(!ctype_digit($id)) utils_redirection('index.php');
$db = new Database();
$model = $db->model($id);
if(!$model) utils_redirection('index.php');
$data = new FrontData($db);
$data->title = $model->prenom;
//$data->meta_description = $model->prenom . ' | Kill Management model';
$data->meta_description = $data->getMetaDescription($model);
$currentIndex = 0; // pour JavaScript.
capture_start();
?><script type="text/javascript" src="<?php echo server_http(); ?>/js/jquery-3.1.0.min.js"></script><?php
capture_end($data->head);
capture_start();
?>
<div class="model">
	<div class="header">
		<div class="firstname"><span><?php echo $model->prenom;?></span></div>
		<div class="infos">
			<div class="details"><?php $data->horizontalDetails($model);?></div>
			<div class="links">
				<span class="current">PORTFOLIO</span><?php
				if($model->hasPolaroids()) {
				?><span><a href="<?php echo "model/$id/".utils_safe_string($model->prenom)."/polaroids";?>">POLAROIDS</a></span><?php }
				if($model->hasVideos()) {
				?><span><a href="<?php echo "model/$id/".utils_safe_string($model->prenom)."/videos";?>">VIDEOS</a></span><?php }
				if($model->instagram_link) {
				?><span><a target="_blank" href="<?php echo $model->instagram_link;?>">INSTAGRAM</a></span><?php }
				if($model->compcard) {
				?><span><a target="_blank" href="<?php echo $model->compcard;?>">COMPCARD</a></span><?php } ?>
			</div>
		</div>
	</div>
	<?php if($model->featured_portfolio_1 || $model->featured_portfolio_2) { ?>
	<div class="featured" id="featured"><?php
		if($model->featured_portfolio_1 && $model->featured_portfolio_2) {
		$photo1 = $model->getPhotoByBasename($model->featured_portfolio_1);
		$photo2 = $model->getPhotoByBasename($model->featured_portfolio_2);
		?><div class="photo photo1" style="background-image: url('<?php echo $photo1['url'];?>');"></div><?php
		?><div class="photo photo2" style="background-image: url('<?php echo $photo2['url'];?>');"></div><?php
		} else {
		$url = $model->featured_portfolio_1 ? $model->featured_portfolio_1 : $model->featured_portfolio_2;
		$photo = $model->getPhotoByBasename($url);
		?><div class="uniquePhoto" style="background-image: url('<?php echo $photo['url'];?>');"></div><?php
		}
	?></div>
	<?php }
	$photos = $model->portfolio();
	if($photos && !empty($photos)) { ?>
	<div class="photos"><?php
	$countPhotos = count($photos);
	$associations = array();
	foreach($photos as $photo) {
		if($photo['associated']) {
			$assoc = $photo['associated'];
			$associations[$assoc][] = $photo;
		} else {
			$associations['free'][] = array($photo);
		}
	}
	$finalArray = array();
	foreach($associations as $key => &$elements) {
		if($key == 'free') foreach($elements as $element)
			$finalArray[] = $element;
		else {
			usort($elements, function($a, $b) {
				$a_time = $a['time'];
				$b_time = $b['time'];
				$t = bccomp($a_time, $b_time);
				if($t == 0) $t = strcmp($a['url'], $b['url']);
				return $t;
			});
			$finalArray[] = $elements;
		}
	}
	usort($finalArray, function($a, $b) {
		$comparator_a = false;
		$comparator_b = false;
		if(count($a) == 1) $comparator_a = $a[0]['time'];
		else $comparator_a = explode('_', $a[0]['associated'])[0];
		if(count($b) == 1) $comparator_b = $b[0]['time'];
		else $comparator_b = explode('_', $b[0]['associated'])[0];
		$t = bccomp($comparator_a, $comparator_b);
		if($t == 0) $t = strcmp($a[0]['url'], $b[0]['url']);
		return $t;
	});
	//$countUnits = ($countPhotos - ($countPhotos % 2))/2 + ($countPhotos % 2);
	$countUnits = count($finalArray);
	for($i = 0; $i < $countUnits; ++$i) { ?>
		<div class="unit">
		<?php
		$elements = $finalArray[$i];
		foreach($elements as &$e) if($e['basename'] == $model->photo) {
			$currentIndex = $i;
			break;
		}
		switch(count($elements)) {
		case 1: ?>
			<div class="unitContent hoverable" onclick="showUnique(this);" style="background-image:url('<?php echo $elements[0]['url'];?>');"></div>
		<?php break;
		case 2: ?>
			<div class="unitContent">
				<div class="photo" onclick="showDouble(this);" style="background-image:url('<?php echo $elements[0]['url'];?>');">
				</div>
				<div class="photo" onclick="showDouble(this);" style="background-image:url('<?php echo $elements[1]['url'];?>');">
				</div>
			</div>
		<?php break;
		default:break;
		} ?>
		</div>
	<?php } ?>
	</div>
	<?php } ?>
</div>
<?php
capture_end($data->content);
capture_start();
?><script type="text/javascript">//<!--
var currentIndex = <?php echo $currentIndex;?>;
function setPhotoIndex(element) {
	var units = document.getElementsByClassName('unitContent');
	for(var i = 0; i < units.length; ++i) {
		if(units[i] == element) {
			currentIndex = i;
			//console.log(currentIndex);
			break;
		}
	}
}
function showUnique(component) {
	setPhotoIndex(component);
	var element = document.createElement('div');
	element.className = 'uniquePhoto';
	element.style.backgroundImage = component.style.backgroundImage;
	var featured = document.getElementById('featured');
	for(var i = 0; i < featured.children.length; ++i) {
		featured.children[i].className += ' toRemove';
	}
	featured.insertBefore(element, featured.firstChild);
	backTop('featured');
	$('#featured .toRemove').fadeOut();
}
function showDouble(child) {
	var parent = child.parentNode;
	setPhotoIndex(parent);
	var photos = [];
	for(var i = 0; i < parent.children.length; ++i) {
		var element = parent.children[i];
		if(element.tagName.toUpperCase() == child.tagName.toUpperCase() && element.className == child.className)
			photos.push(parent.children[i]);
	}
	if(photos.length == 2) {
		var element1 = document.createElement('div');
		var element2 = document.createElement('div');
		element1.className = 'photo photo1';
		element2.className = 'photo photo2';
		element1.style.backgroundImage = photos[0].style.backgroundImage;
		element2.style.backgroundImage = photos[1].style.backgroundImage;
		var featured = document.getElementById('featured');
		for(var i = 0; i < featured.children.length; ++i) {
			featured.children[i].className += ' toRemove';
		}
		featured.insertBefore(element2, featured.firstChild);
		featured.insertBefore(element1, featured.firstChild);
		backTop('featured');
		$('#featured .toRemove').fadeOut();
	}
}
//--></script>
<script type="text/javascript">//<!--
(function() {
	var units = document.getElementsByClassName('unitContent');
	var photoCount = units.length;
	var featured = document.getElementById('featured');
	featured.onclick = function() {
		currentIndex = (currentIndex + 1) % photoCount;
		var unit = document.getElementsByClassName('unitContent')[currentIndex];
		if(unit.children.length == 0) {
			showUnique(unit);
		} else {
			showDouble(unit.getElementsByClassName('photo')[0]);
		}
	}
})();
//--></script><?php
capture_end($data->scripts);

echo template($data);
?>