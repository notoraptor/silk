<?php
/**
 * Created by PhpStorm.
 * User: notoraptor
 * Date: 29/08/2018
 * Time: 06:33
 */
function print_model_card($models, $index) {
	$html = '';
	capture_start();
	if ($index < count($models)) {
		$model = $models[$index];
		?>
		<div class="model">
			<div class="row">
				<div class="col align-self-center">
                    <img class="img-fluid" src="<?php echo $model->getPhotoByBasename($model->photo)['url'];?>"/>
				</div>
				<div class="col">
					<div class="details">
						<div class="name"><?php echo $model->first_name.' '.$model->last_name;?></div>
						<div class="detail"><span class="key">Height:</span> <span class="value"><?php echo $model->height;?></span></div>
						<div class="detail"><span class="key">Waist:</span> <span class="value"><?php echo $model->waist;?></span></div>
						<div class="detail"><span class="key">Bust:</span> <span class="value"><?php echo $model->bust;?></span></div>
						<div class="detail"><span class="key">Hips:</span> <span class="value"><?php echo $model->hips;?></span></div>
						<div class="detail"><span class="key">Hair:</span> <span class="value"><?php echo $model->hair;?></span></div>
						<div class="detail"><span class="key">Shoes:</span> <span class="value"><?php echo $model->shoes;?></span></div>
						<div class="detail"><span class="key">Eyes:</span> <span class="value"><?php echo $model->eyes;?></span></div>
						<div class="icons">
							<?php if ($model->instagram_link) {
								?>
								<div class="row">
									<div class="col">
										<div class="instagram">
											<a target="_blank" href="https://www.instagram.com/<?php echo $model->instagram_link;?>"><img class="img-fluid" src="data/main/instagram-black.svg"></a>
										</div>
										<div class="followers"><?php echo get_nb_followers($model->instagram_link);?></div>
									</div>
									<div class="col">
										<div class="heart">&hearts;</div>
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
	} else {
		echo '&nbsp;';
	}
	capture_end($html);
	return array(
		'html' => $html,
		'next' => $index + 1
	);
}
function print_models($models, $select_fn) {
	$html_models = '';
	$selected_models = array();
	foreach($models as $model) if ($model->photo && $select_fn($model)) $selected_models[] = $model;
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
				<div class="row mt-3">
					<div class="col">
						<?php
						$res = print_model_card($selected_models, $index_model);
						$index_model = $res['next'];
						echo $res['html'];
						?>
					</div>
					<div class="col">
						<?php
						$res = print_model_card($selected_models, $index_model);
						$index_model = $res['next'];
						echo $res['html'];
						?>
					</div>
					<div class="col">
						<?php
						$res = print_model_card($selected_models, $index_model);
						$index_model = $res['next'];
						echo $res['html'];
						?>
					</div>
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