<?php
$success = false;
$id = utils_s_get('id', false);
if($id !== false && ctype_digit($id)) {
	$db = new Database();
	if($db->model_exists($id)) {
		$success = true;
		$model = $db->model($id);
		$fullName = $model->first_name.' '.$model->last_name;
		$profilePhoto = $model->getPhotoByBasename($model->photo);
		$article = $model->article;
		$show_article = $model->show_article;
		$article_rank  = $model->article_rank;
		if (!empty($_POST)) {
			$article = utils_safe_post('article', $model->article);
			$show_article = utils_safe_post('show-article', false);
			$article_rank = utils_safe_post('article-rank', $model->article_rank);
			$show_article = $show_article ? 1 : 0;
			$article = trim($article);
			$db->model_update_article($model->model_id, $article, $show_article, $article_rank);
			utils_message_add_success('Article mis à jour');
		}
		$_POST = array(
		        'article' => $article,
		        'show-article' => $show_article,
		        'article-rank' => $article_rank,
        );
		if (!$show_article)
		    unset($_POST['show-article']);
		?>
        <div class="table breadcumbs">
            <div class="cell main">
                <h2>
                    <a href="index.php?panel=models">Modèles</a> /
                    <a href="index.php?panel=model&id=<?php echo $id;?>"><?php echo $fullName;?></a> /
                    Modifier l'article
                </h2>
            </div>
            <div class="cell photo"><?php if($profilePhoto) { ?><img src="<?php echo $profilePhoto['url'];?>"/><?php } ?></div>
        </div>
        <div class="configuration">
            <form method="post" action="index.php?panel=modelarticle&id=<?php echo $model->model_id;?>">
                <fieldset>
                    <legend>ARTICLE DU MODÈLE</legend>
                    <div class="table">
						<?php
						echo utils_textarea('Article (pour insérer une photo du modèle, taper {photo i}, i pouvant être 1, 2, 3 ou 4)','article');
						echo utils_checkbox('Afficher l\'article?', 'show-article');
						echo utils_input('Ordre d\'affichage', 'article-rank', 'number');
						?>
                    </div>
                    <div><input type="submit" value="Mettre à jour l'article"/></div>
                    <script src="nicEdit/nicEdit.js" type="text/javascript"></script>
                    <script type="text/javascript">//<!--
                        bkLib.onDomLoaded(function() {
                            const indices = ['article'];
                            for (let index of indices)
                                new nicEditor({iconsPath: 'nicEdit/nicEditorIcons.gif', buttonList: ['fontSize','bold','italic','underline','left','center','right','justify','link','unlink','removeformat','xhtml']}).panelInstance(index);
                        });
                        //--></script>
                </fieldset>
            </form>
        </div>
		<?php
	}
}
if(!$success)
	utils_message_add_error("Impossible d'afficher la page d'édition de l'article du modèle ayant pour ID $id. Modèle inexistant, ou erreur interne.");
?>