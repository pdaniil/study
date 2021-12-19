<?php

//Добавляем ещё одно условие перед загрузкой страницы (Отвязка соц сети).

else if ($_POST["save_action"] == "social")
{
	$user_id = $_POST["user_id"];
	$id_social = $_POST["id_social"];

	$SQL_delete_social_user = "DELETE FROM `social_user_data` WHERE `user_id` = ? AND `social_id` = ?;";
	$query = $db_link_pdo_pdo->prepare($SQL_delete_social_user);
	$flag_result = $query->execute( array( $user_id, $id_social ) );

	$SQL_check_social = "SELECT COUNT(*) as `count` FROM `social_user_data` WHERE `user_id` = ?;";
	$query = $db_link_pdo_pdo->prepare($SQL_check_social);
	$query->execute( array( $user_id ) );
	$count_social = $query->fetch();

	if ($count_social["count"] < 1)
	{
		$SQL_delete_flag = "UPDATE `users` SET `social` = 0 WHERE `user_id` = ?;";
		if ($db_link_pdo_pdo->prepare($SQL_delete_flag)->execute( array( $user_id ) ))
		{
			?>
			<script>
				location="<?php echo $DP_Config->domain_path.$DP_Config->backend_dir; ?>/users/usermanager/user?user_id=<?php echo $_POST["user_id"]; ?>&info_message=Соц сеть была успешно удалена из профиля пользователя.";
			</script>
			<?php
			exit;
		}
		else
		{
			?>
			<script>
				location="<?php echo $DP_Config->domain_path.$DP_Config->backend_dir; ?>/users/usermanager/user?user_id=<?php echo $_POST["user_id"]; ?>&error_message=Ошибка в удалении данных о соц сетях пользователя. Не удалось удалить данные в таблице пользователей.";
			</script>
			<?php
			exit;
		}
	}

	if ($flag_result)
	{
		?>
		<script>
			location="<?php echo $DP_Config->domain_path.$DP_Config->backend_dir; ?>/users/usermanager/user?user_id=<?php echo $_POST["user_id"]; ?>&info_message=Соц сеть была успешно отвязана.";
		</script>
		<?php
		exit;
	}
	else
	{
		?>
		<script>
			location="<?php echo $DP_Config->domain_path.$DP_Config->backend_dir; ?>/users/usermanager/user?user_id=<?php echo $_POST["user_id"]; ?>&error_message=Ошибка в удалении данных о соц сетях пользователя.";
		</script>
		<?php
		exit;
	}



}



//Вывод данных и скрипт запуска формы (добавить после вывода всех остальных данных)
<div class="col-lg-6">
<div class="hpanel">
<div class="panel-heading hbuilt">
Соц сети пользователя
</div>
<div class="panel-body">
<div id="social_block" style="height:350px;">
<?php
					//Выбираем все соц сети из бд и выводим в блок
$SQL_get_social = "SELECT `social`.`social_img_url`,`social`.`social_caption`,`social`.`id`,`social_user_data`.`user_id` FROM `social` INNER JOIN `social_user_data` ON `social`.`id` = `social_user_data`.`social_id` WHERE `social_user_data`.`user_id` = ?";
$query = $db_link_pdo_pdo->prepare($SQL_get_social);
$query->execute( array( $_GET["user_id"] ) );
if (DP_User::isAdmin())
{
	while ($social = $query->fetch())
	{
		
		?>
		<div  class="col-lg-9"> 
			<img width="50" height="50" src="<?php echo "https://".$_SERVER["SERVER_NAME"].$social["social_img_url"];?>" />
			<span id="span_social_<?php echo $social["social_caption"] ;?>" class="span_social"><?php echo $social["social_caption"]; ?></span>
		</div>
		<div class="col-lg-2" style="margin-top: 15px;">
			<span style="cursor: pointer; background: red; color: white; padding: 5px; border-radius: 5px;" onClick="delete_social(document.querySelector('#span_social_<?php echo $social["social_caption"]; ?>').textContent, <?php echo $social["id"]; ?>)">Отвязать</span>
		</div>
		<form id = "social_delete_form_<?php echo $social["id"]; ?>" method="POST" action="https://dotactic.ru/cp/users/usermanager/user?user_id=<?php echo $_GET["user_id"]; ?>">
			<input type="hidden" name="save_action" value="social">
			<input type="hidden" name="user_id" value="<?php echo $_GET["user_id"];?>">
			<input type="hidden" name="id_social" value="<?php echo $social["id"]; ?>">
		</form>
		<div class="col-lg-12">
			&nbsp;
		</div>
		
		<?php
	}
}

?>
</div>

</div>
</div>
</div>
<script>
	function delete_social(social_caption, id_form)
	{
		var isSure = confirm("Вы дествительно хотите отвязать соц сеть "+ social_caption + "?");
		if (isSure)
		{
			document.querySelector('#social_delete_form_' + id_form).submit();
		}
	}
</script>