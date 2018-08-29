<?php
header("Content-type: application/xml");
require_once("server_infos.php");
require_once("priv/utils.php");
$db = new Database();
$models = $db->models();
echo "<?xml version='1.0' encoding='UTF-8'?>";
?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	<url><loc><?php echo server_http();?></loc></url>
	<url><loc><?php echo server_http();?>/contact</loc></url><?php
if($models) foreach($models as $m) {
?><url><loc><?php echo server_http().'/model/'.$m->model_id.'/'.utils_safe_string($m->prenom);?></loc></url><?php
if($m->hasPolaroids()) {
?><url><loc><?php echo server_http().'/model/'.$m->model_id.'/'.utils_safe_string($m->prenom).'/polaroids';?></loc></url><?php
}; if($m->hasVideos()) {
?><url><loc><?php echo server_http().'/model/'.$m->model_id.'/'.utils_safe_string($m->prenom).'/videos';?></loc></url><?php
}
} ?>
</urlset>