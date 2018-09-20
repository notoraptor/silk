<?php
function template($data) {
if (!session_id()) session_start();
if ($data->meta_description == '')
	$data->meta_description = 'SILK fashion modeling agency, official website';
if ($data->meta_keywords == '') {
	$data->meta_keywords = implode(',', array(
		'fashion', 'modeling agency', 'silk', 'photography', 'booking', 'montreal', 'modeling', 'agency', 'model',
		'models', 'silk girl', 'silk man', 'silk team', 'silkgirl', 'silkteam'
	));
}
$count = count($data->models);
$menu_titles = array('MEN', 'WOMEN', 'LIFESTYLE', 'SUBMISSION', 'ABOUT');
$menu_names = array('men', 'women', 'lifestyle', 'submission', 'about');
$count_favourites = utils_count_favourites();
if ($count_favourites) {
	$menu_titles[] = '&hearts; ('.$count_favourites.')';
	$menu_names[] = 'favourites';
}
$count_menu = count($menu_titles);
ob_start();
?><!DOCTYPE html>
<html lang="en">
<head>
    <base href="<?php echo server_http(); ?>/"/>
    <meta charset="UTF-8"/>
    <meta name="description" content="<?php echo $data->meta_description; ?>"/>
    <meta name="keywords" content="<?php echo $data->meta_keywords; ?>"/>
    <meta name="author" content="Steven Bocco"/>
    <title><?php echo $data->title; ?></title>
    <link rel="shortcut icon" type="image/x-icon" href="data/main/favicon.ico"/><!-- TODO favicon -->
    <link rel="icon" type="image/x-icon" href="data/main/favicon.ico"/>
    <link rel="stylesheet" href="css/bootstrap.css"/>
    <link rel="stylesheet" href="css/style.css"/>
	<?php if ($data->head != '') echo $data->head; ?>
    <script src="js/mq.genie.min.js"></script>
</head>
<body <?php if ($data->pagename != '') echo 'id="' . $data->pagename . '"'; ?>>
<?php if ($data->messages != '') { ?>
    <div class="messages"><?php echo $data->messages; ?></div><?php } ?>
<div id="content" class="container">
    <div id="page-title" class="mt-3 mb-4">
        <div id="line-1">
			<?php if ($data->pagename != 'home') { ?><a href="index.php"><?php } ?>
                SILK
				<?php if ($data->pagename != 'home') { ?></a><?php } ?>
        </div>
        <div id="line-2">MANAGEMENT</div>
    </div>
    <nav class="row menu-bar text-center">
		<?php for ($i = 0; $i < $count_menu; ++$i) {
			$menu_title = $menu_titles[$i];
			$menu_name = $menu_names[$i];
			?>
            <div class="col-md menu py-2 py-md-0">
				<?php if ($data->pagename != $menu_name) { ?>
                <a href="<?php echo $menu_name.'.php'; ?>"><?php } ?>
					<?php echo $menu_title; ?>
					<?php if ($data->pagename != $menu_name) { ?></a><?php } ?>
            </div>
			<?php
		} ?>
    </nav>
    <div class="mt-5 mb-3 page">
		<?php echo $data->content; ?>
        <div class="footer">
            <a id="footer" href="#page" onclick="backTop('content'); return false;">/BACK TO TOP</a>
        </div>
    </div>
</div>
<script type="text/javascript" src="js/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="js/popper.min.js"></script>
<script type="text/javascript" src="js/bootstrap.js"></script>
<?php if ($data->scripts != '') echo $data->scripts; ?>
<script type="text/javascript" src="js/AnimateScroll.min.js"></script>
<script type="text/javascript"><!--
    function backTop(id, onFinish) {
        var element = document.getElementById(id);
        if (element) {
            var totalOffset = 0;
            var current = element;
            do {
                totalOffset += current.offsetTop;
                current = current.offsetParent;
            } while (current);
            var offset = window.pageYOffset - totalOffset;
            if (offset == 0) {
                if (onFinish) onFinish();
            } else {
                animateScroll(element, 1000, 'easeOutQuint', 0, 'top', onFinish);
            }
        }
    }

    //--></script>
<script type="text/javascript"><!--
    function bouton_haut_de_page() {
        var bouton = document.getElementById('footer');
        if (bouton) {
            if (window.pageYOffset > 200) bouton.style.display = "inline-block";
            else bouton.style.display = "none";
        }
    }
    bouton_haut_de_page();
    window.onscroll = bouton_haut_de_page;
    //--></script>
<script type="text/javascript"><!--
    document.body.onload = function () {
        // Gestion des éléments survolables pour iPhone et les appareils mobiles.
        var hoverables = document.getElementsByClassName('hoverable');
        console.log(hoverables.length + ' éléments explicitement survolables.');
        for (var x = 0; x < hoverables.length; ++x) {
            var hoverable = hoverables[x];
            if (!hoverable.onclick) {
                hoverable.onclick = function () {
                    void(0);
                    //console.log('clicked');
                };
            }
        }
    }
    //--></script>
</body>
</html><?php
$content = ob_get_contents();
ob_end_clean();
return $content;
}
?>