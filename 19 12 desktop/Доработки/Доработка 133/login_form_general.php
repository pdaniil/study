
<!--Данный скрипт добавить после поля «Не помню пароль»-->
<div  style="margin-bottom: 10px; text-align: center; width: 80%; ">
	<div style="width: 100%; text-align: center; margin-bottom: 10px;">Или</div>
	<div style=" text-align: center; width: 100%;">
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
			<a style="width: 30px; height: 30px; margin-left: 5px; margin-right: 5px; display: inline-block;" href="https://<?php echo $_SERVER["SERVER_NAME"].$options["uri_redirect"];?>"><img width="30" height="30" src="<?php echo "https://".$_SERVER["SERVER_NAME"].$social["social_img_url"];?>" /></a>
			<?php 
		}
		?>
	</div>
</div>