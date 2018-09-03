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
$model = $data->content;
$origin = utils_s_get('origin');
$in_favourites = utils_has_favourite($model->model_id);
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
    <link rel="stylesheet" href="silk-css/bootstrap.css"/>
    <link rel="stylesheet" href="silk-css/style.css"/>
    <link rel="stylesheet" href="silk-css/template.css"/>
	<?php if ($data->head != '') echo $data->head; ?>
</head>
<body <?php if ($data->pagename != '') echo 'id="' . $data->pagename . '"'; ?>>
<div id="content" class="container-fluid d-flex flex-column">
    <div class="row align-items-center title-bar">
        <div class="col-sm model-name"><?php echo $model->first_name.' '.$model->last_name;?></div>
        <div class="col-sm-1 close-button"><a href="<?php echo $origin;?>.php">&#215;</a></div>
    </div>
    <div class="row align-items-center flex-grow-1">
        <div class="col-sm model-buttons">
            <a href="portfolio.php?origin=<?php echo $origin;?>&id=<?php echo $model->model_id;?>">PORTFOLIO</a>
            <a class="ml-5" href="favourites.php?origin=<?php echo $origin;?>&action=<?php echo ($in_favourites ? 'remove' : 'add');?>&id=<?php echo $model->model_id;?>"><?php echo ($in_favourites ? '-' : '+'); ?> FAVOURITES</a>
            <a class="ml-5" href="portfolio-print.php?origin=<?php echo $origin;?>&id=<?php echo $model->model_id;?>">PRINT</a>
        </div>
        <div class="col-sm-1 model-details">
            <?php
                $detail_names = array('height', 'bust', 'waist', 'hips', 'shoes', 'hair', 'eyes');
                $detail_titles = ARRAY('HEIGHT', 'BUST', 'WAIST', 'HIPS', 'SHOES', 'HAIR', 'EYES');
                for ($i = 0; $i < count($detail_names); ++$i) {
                    $detail_name = $detail_names[$i];
                    $detail_title = $detail_titles[$i];
                    ?>
                    <div class="detail">
                        <div class="detail-title"><?php echo $detail_title;?></div>
                        <div class="detail-value"><?php echo $model->$detail_name;?></div>
                    </div>
                    <?php
                }
            ?>
        </div>
        <div class="col-sm-4 h-100 p-0">
            <div id="diaporama" class="carousel slide h-100 diaporama" data-ride="carousel">
                <div class="carousel-inner h-100 images">
					<?php
					$photo_indices = array();
					foreach(array(1, 2, 3, 4) as $photo_rank) {
						$photo_id = 'photo_'.$photo_rank;
						if ($model->$photo_id) $photo_indices[] = $photo_id;
					}
					for($i = 0; $i < count($photo_indices); ++$i) {
					    $photo_id = $photo_indices[$i];
						?>
                        <div class="carousel-item <?php if (!$i) { ?>active<?php } ?> h-100">
                            <div class="d-block d-flex flex-column diaporama-item w-100 h-100">
                                <div class="image w-100 flex-grow-1"
                                     style="background-image: url('<?php echo $model->getPhotoByBasename($model->$photo_id)['url']; ?>'); width:100px; height:100px;">
                                </div>
                                <div class="caption">
                                    0<?php echo ($i + 1);?>/0<?php echo count($photo_indices);?><br/><br/>
                                </div>
                            </div>
                        </div>
						<?php
					}
					?>
                </div>
                <a class="carousel-control-prev" href="#diaporama" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next" href="#diaporama" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                </a>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="silk-js/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="silk-js/popper.min.js"></script>
<script type="text/javascript" src="silk-js/bootstrap.js"></script>
<?php if ($data->scripts != '') echo $data->scripts; ?>
<script type="text/javascript"><!--
    let indexPhoto = 0;
    const PHOTOS = ['<?php echo implode("', '", $photo_indices);?>'];
    function updateIndexView() {
        const indexView = document.getElementById('index');
        const buttonPrevious = document.getElementById('button-previous');
        const buttonNext = document.getElementById('button-next');
        if (indexView)
            indexView.textContent = `0${indexPhoto + 1}/0${PHOTOS.length}`;
        if (buttonPrevious)
            buttonPrevious.style.display = indexPhoto ? 'block' : 'none';
        if (buttonNext)
            buttonNext.style.display = indexPhoto < PHOTOS.length - 1 ? 'block' : 'none';
    }
    function previousImage() {
        if (indexPhoto) {
            const currentPhoto = document.getElementById(PHOTOS[indexPhoto]);
            if (currentPhoto) {
                currentPhoto.style.display = "none";
                --indexPhoto;
                const nextPhoto = document.getElementById(PHOTOS[indexPhoto]);
                if (nextPhoto)
                    nextPhoto.style.display = "block";
                updateIndexView();
            }
        }
    }
    function nextImage() {
        if (indexPhoto !== PHOTOS.length - 1) {
            const currentPhoto = document.getElementById(PHOTOS[indexPhoto]);
            if (currentPhoto) {
                currentPhoto.style.display = "none";
                ++indexPhoto;
                const nextPhoto = document.getElementById(PHOTOS[indexPhoto]);
                if (nextPhoto)
                    nextPhoto.style.display = "block";
                updateIndexView();
            }
        }
    }
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