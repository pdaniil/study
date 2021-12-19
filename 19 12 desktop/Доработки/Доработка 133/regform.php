<!-- Данный скрипт добавить в раздел основных блоков регистрации, в class="panel panel-primary", перед закрывающим тэгом.  -->

<div  style="margin-bottom: 60px; text-align: center;  width: 100%; height: 50px;">

	<div style="width: 100%; text-align: center; font-size:24px; margin-bottom: 20px;">Или</div>
	<?php
	$SQL_get_social = "SELECT * FROM `social`";
	$query = $db_link_pdo_pdo->prepare($SQL_get_social);
	$query->execute();

	while ($social = $query->fetch())
	{	
		$SQL_get_options = "SELECT * FROM `social_options` WHERE `id_social` = ?";
		$query_options = $db_link_pdo_pdo->prepare($SQL_get_options);
		$query_options->execute( array( $social["id"] ) );
		$options = $query_options->fetch();
		?>
		<a style="margin-left: 10px; margin-right: 10px; width: 50px; height: 50px;" href="https://<?php echo $_SERVER["SERVER_NAME"].$options["uri_redirect"];?>"><img width="50" height="50" src="<?php echo "https://".$_SERVER["SERVER_NAME"].$social["social_img_url"];?>" /></a>
		<?php
	} 
	?>
</div>	