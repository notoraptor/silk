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
</head>
<body <?php if ($data->pagename != '') echo 'id="' . $data->pagename . '"'; ?>>
<div id="content" class="container-fluid d-flex flex-column">
    <div class="row align-items-center title-bar">
        <div class="col-sm model-name"><?php echo $model->first_name . ' ' . $model->last_name; ?></div>
        <div class="col-sm-1 close-button"><a href="model.php?origin=<?php echo $origin; ?>&id=<?php echo $model->model_id; ?>">&#215;</a></div>
    </div>
    <div class="row align-items-center flex-grow-1" style="background-color: orange;">
        <div class="w-100 h-100 images" style="background-color: blue;">
			<?php
			$photo_indices = array();
			foreach (array(1, 2, 3, 4) as $photo_rank) {
				$photo_id = 'photo_' . $photo_rank;
				if ($model->$photo_id) $photo_indices[] = $photo_id;
			}
			for ($i = 0; $i < count($photo_indices); ++$i) {
				$photo_id = $photo_indices[$i];
				?>
                <!--
            <img class="img-fluid w-50" src="<?php echo $model->getPhotoByBasename($model->$photo_id)['url']; ?>"/>
            -->
                <div class="w-50 h-100 image"
                     style="background-image: url('<?php echo $model->getPhotoByBasename($model->$photo_id)['url']; ?>'); background-color: green;">
                </div>
				<?php
			}
			?></div>
    </div>
</div>
<script type="text/javascript" src="js/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="js/popper.min.js"></script>
<script type="text/javascript" src="js/bootstrap.js"></script>
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