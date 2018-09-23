<?php
require_once('../priv/videodetection.php');
$db = new Database();
if(!empty($_POST)) {
	$home_video_link = utils_safe_post('home_video_link');
	$about_page_text = utils_safe_post('about_page_text');
	$about_contact_text = utils_safe_post('about_contact_text');
	$submission_page_text = utils_safe_post('submission_page_text');
	$submission_page_data_sharing_text = utils_safe_post('submission_page_data_sharing_text');
	$site_email = utils_safe_post('site_email');
	$home_video_link = hex2bin($home_video_link);
	if($home_video_link && (!utils_valid_url($home_video_link) || !Video::parse($home_video_link)))
		utils_message_add_error("Le lien vidéo est invalide.");
	else if ($site_email && !utils_valid_email($site_email))
	    utils_message_add_error('Le compte courriel est invalide.');
	else {
		$db->config_update(array(
		        'home_video_link' => $home_video_link,
		        'about_page_text' => $about_page_text,
		        'about_contact_text' => $about_contact_text,
		        'submission_page_text' => $submission_page_text,
		        'submission_page_data_sharing_text' => $submission_page_data_sharing_text,
		        'site_email' => $site_email
        ));
		utils_message_add_success("La configuration du site a été mise à jour.");
	}
}
$config = $db->config();
if(!$config) die("Erreur interne: impossible de charger la configuration du site.");
$post_video_link = utils_safe_post('home_video_link');
if ($post_video_link)
    $post_video_link = hex2bin($post_video_link);
else
    $post_video_link = $config->home_video_link;
$_POST = array(
	'home_video_link' => $post_video_link,
	'about_page_text' => utils_safe_post('about_page_text', $config->about_page_text),
	'about_contact_text' => utils_safe_post('about_contact_text', $config->about_contact_text),
	'submission_page_text' => utils_safe_post('submission_page_text', $config->submission_page_text),
	'submission_page_data_sharing_text' => utils_safe_post('submission_page_data_sharing_text', $config->submission_page_data_sharing_text),
	'site_email' => utils_safe_post('site_email', $config->site_email)
);
?>
<div class="configuration">
<form action="index.php?panel=config" method="post" onsubmit="wrap();">
<fieldset>
	<legend>Configuration du site</legend>
	<div class="table">
		<?php
		echo input_url("Lien vers la vidéo de la page d'accueil", 'home_video_link');
		echo utils_textarea('À propos','about_page_text');
		echo utils_textarea('Contact','about_contact_text');
		echo utils_textarea('Politique de recrutement','submission_page_text');
		echo utils_textarea('Politique de partage des donn&eacute;es','submission_page_data_sharing_text');
		echo input_text('Courriel du site', 'site_email');
		?>
	</div>
	<div><input type="submit" value="Mettre à jour"/></div>
    <script type="text/javascript">//<!--
        function wrap() {
            careful(['home_video_link']);
            const indices = ['about_page_text', 'submission_page_text', 'submission_page_data_sharing_text'];
            for(let i = 0; i < indices.length; ++i) {
                const text_area = document.getElementById(indices[i]);
                text_area.value = text_area.value.trim();
                if(!text_area.value.startsWith('<')) {
                    text_area.value = '<div>' + text_area.value + '</div>';
                }
            }
        }
        //--></script>
    <script src="nicEdit/nicEdit.js" type="text/javascript"></script>
    <script type="text/javascript">//<!--
        bkLib.onDomLoaded(function() {
            const indices = ['about_page_text', 'about_contact_text', 'submission_page_text', 'submission_page_data_sharing_text'];
            for (let index of indices)
                new nicEditor({iconsPath: 'nicEdit/nicEditorIcons.gif', buttonList: ['fontSize','bold','italic','underline','left','center','right','justify','link','unlink','removeformat','xhtml']}).panelInstance(index);
        });
        //--></script>
</fieldset>
</form>
</div>