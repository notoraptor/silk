<?php
/**
 * Created by PhpStorm.
 * User: notoraptor
 * Date: 29/08/2018
 * Time: 06:33
 */
function print_model_card($models, $index, $pagename) {
	$html = '';
	if ($index < count($models)) {
		$model = $models[$index];
		capture_start();
		?>
            <div class="model">
                <div class="row m-0 h-100">
                    <div class="col-lg align-self-center">
                        <a href="model.php?origin=<?php echo $pagename; ?>&id=<?php echo $model->model_id; ?>">
                            <img class="img-fluid" src="<?php echo $model->getPhotoByBasename($model->photo)['url']; ?>"/>
                        </a>
                    </div>
                    <div class="col-lg align-self-end pb-3">
                        <div class="details">
                            <p class="name">
                                <a href="model.php?origin=<?php echo $pagename; ?>&id=<?php echo $model->model_id; ?>">
                                    <?php echo $model->first_name . ' ' . $model->last_name; ?>
                                </a>
                            </p>
                            <div class="detail"><span class="key">Height:</span> <span
                                        class="value"><?php echo $model->height; ?></span></div>
                            <div class="detail"><span class="key">Waist:</span> <span
                                        class="value"><?php echo $model->waist; ?></span></div>
                            <div class="detail"><span class="key">Bust:</span> <span
                                        class="value"><?php echo $model->bust; ?></span></div>
                            <div class="detail"><span class="key">Hips:</span> <span
                                        class="value"><?php echo $model->hips; ?></span></div>
                            <div class="detail"><span class="key">Hair:</span> <span
                                        class="value"><?php echo $model->hair; ?></span></div>
                            <div class="detail"><span class="key">Shoes:</span> <span
                                        class="value"><?php echo $model->shoes; ?></span></div>
                            <div class="detail"><span class="key">Eyes:</span> <span
                                        class="value"><?php echo $model->eyes; ?></span></div>
                            <div class="icons">
                                <?php if ($model->instagram_link) {
                                    ?>
                                    <div class="row">
                                        <div class="col-sm text-right">
                                            <div style="display: inline-block">
                                                <div class="text-center">
                                                    <a target="_blank" href="https://www.instagram.com/<?php echo $model->instagram_link; ?>">
                                                        <img class="img-fluid instagram" src="data/main/instagram-black.svg">
                                                    </a>
                                                </div>
                                                <div class="text-center">
                                                    <?php echo get_nb_followers($model->instagram_link); ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm text-left">
                                            <a class="heart" href="favourites.php?action=add&from=<?php echo $pagename; ?>&id=<?php echo $model->model_id; ?>">
                                                &hearts;
                                            </a>
                                        </div>
                                    </div>
                                    <?php
                                } else {
                                    ?>
                                    <div class="heart">&hearts;</div>
                                    <?php
                                } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
		<?php
		capture_end($html);
	}
	return array(
		'html' => $html,
		'next' => $index + 1
	);
}

function print_models($models, $select_fn, $pagename) {
	$html_models = '';
	$selected_models = array();
	foreach ($models as $model) if ($model->photo && $select_fn($model)) $selected_models[] = $model;
	$count_selected = count($selected_models);
	if ($count_selected) {
		capture_start();
		?>
        <div class="models">
			<?php
			$n_rows = (int)($count_selected / 3);
			if ($count_selected - 3 * $n_rows) ++$n_rows;
			$index_model = 0;
			for ($i_row = 0; $i_row < $n_rows; ++$i_row) { ?>
                <div class="row">
					<?php for ($i_col = 0; $i_col < 3; ++$i_col) { ?>
                        <div class="col-md p-2">
                            <div class="model-wrapper">
							<?php
							$res = print_model_card($selected_models, $index_model, $pagename);
							$index_model = $res['next'];
							echo $res['html'];
							?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
				<?php
			}
			?>
        </div>
		<?php
		capture_end($html_models);
	}
	return $html_models;
}

?>