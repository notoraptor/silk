<?php
if(!session_id()) session_start();
require_once('server_infos.php');
require_once('priv/utils.php');
require_once('priv/template.php');

$db = new Database();
$models = $db->models();
$models_with_articles = get_models_for_articles($models);
$data = new FrontData($db);
$config = $data->getConfig();
$videoInfo = false;
if($config && $config->home_video_link) {
	require_once('priv/videodetection.php');
	$videoInfo = Video::parse($config->home_video_link);
	if($videoInfo) {
		capture_start(); ?>
        <div class="welcome" id="videoWelcome">
            <div class="welcomeElement" id="homeVideo"><?php echo Video::getHomeCode($videoInfo); ?></div>
        </div>
        <?php echo print_models_for_articles($models_with_articles); ?>
		<?php capture_end($data->content);
		capture_start();
		switch($videoInfo[0]) {
			case 'vimeo': ?>
                <script src="https://player.vimeo.com/api/player.js"></script>
                <script type="text/javascript">//<!--
                    var iframe = document.querySelector("#homeVideo iframe");
                    var player = new Vimeo.Player(iframe);
                    //--></script>
				<?php break;
			case 'youtube': ?>
                <script type="text/javascript">//<!--
                    // 2. This code loads the IFrame Player API code asynchronously.
                    var tag = document.createElement('script');
                    tag.src = "https://www.youtube.com/iframe_api";
                    var firstScriptTag = document.getElementsByTagName('script')[0];
                    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
                    // 3. This function creates an <iframe> (and YouTube player)
                    //    after the API code downloads.
                    var player;
                    function onYouTubeIframeAPIReady() {
                        player = new YT.Player('youtubeVideo', {
                            videoId: "<?php echo $videoInfo[1]; ?>",
                            playerVars: {
                                'autoplay': 1, 'controls': 0, 'rel': 0, 'start': 0
                            }
                        });
                    }
                    //--></script>
				<?php break;
			case 'dailymotion': ?>
                <script type="text/javascript">//<!--
                    window.dmAsyncInit = function() {
                        DM.init({
                            apiKey: 'e03e24146b2cdb739cf8',
                            status: false, // check login status
                            cookie: false // enable cookies to allow the server to access the session
                        });
                        var player = DM.player(document.getElementById('dailymotionVideo'), {
                            video: "<?php echo $videoInfo[1]; ?>",
                            width: '100%',
                            height: '100%',
                            params: {
                                autoplay: true,
                                controls: false,
                                "endscreen-enable": false
                            }
                        });
                    };
                    (function() {
                        var e = document.createElement('script');
                        e.async = true;
                        e.src = 'https://api.dmcdn.net/all.js';
                        var s = document.getElementsByTagName('script')[0];
                        s.parentNode.insertBefore(e, s);
                    }());
                    //--></script>
				<?php break;
			default:break;
		}
		capture_end($data->scripts);
	}
}

$data->title = 'SILK';
$data->pagename = 'home';
echo template($data);
?>