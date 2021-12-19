<!-- Вставляем вместо блока условий if/else после $delete_result = $delete_result->execute($binding_values); в блоке удаления записи групп пользователя. 

	КОНТЕНТ ИДЁТ КАК НА СТРАНИЦЕ - ОТ ВЕРХА К НИЗУ.-->

	if ($delete_result === true)
	{    
		//Удаляем информацию о пользователе из таблицы social_user_data
		//Так как этих данных может там не быть, то вначале проверяем через SELECT
		//Если count > 0, тогда уже делаем DELETE, если ошибка - сообщаем
		//Если count = 0, то DELETE не делаем, просто выводим сообщение об успешном удалении

		$SQL_find = "SELECT COUNT(*) AS `count` FROM `social_user_data` WHERE";
		$binding_values = array();
		for($i=0; $i<count($users_list_to_del); $i++)
		{
			$SQL_find .= " `user_id`=".$users_list_to_del[$i];
			array_push($binding_values,  $users_list_to_del[$i]);
			if(!(($i+1) >= count($users_list_to_del))) $SQL_find .= " OR";//Если итерация не последняя
		}
		$find_result = $db_link_pdo_pdo->prepare($SQL_find);
		$find_result->execute($binding_values);
		$count = $find_result->fetch();

		if ($count["count"] > 0)
		{	//12345678
			$SQL_delete = "DELETE FROM `social_user_data` WHERE";
			$binding_values = array();
			for($i=0; $i<count($users_list_to_del); $i++)
			{
				$SQL_delete .= " `user_id`=".$users_list_to_del[$i];
				array_push($binding_values,  $users_list_to_del[$i]);
				if(!(($i+1) >= count($users_list_to_del))) $SQL_delete .= " OR";//Если итерация не последняя
			}
			$delete_result = $db_link_pdo_pdo->prepare($SQL_delete);
			$delete_result = $delete_result->execute($binding_values);

			if ($delete_result = true)
			{
				//12345678
				//Переадресация с сообщением о результатах выполнения
				$success_message = "Удаление выполнено успешно";
				?>
				<script>
					location="<?php echo $DP_Config->domain_path.$DP_Config->backend_dir; ?>/users/usermanager?success_message=<?php echo $success_message.$s_page; ?>";
				</script>
				<?php
				exit;
			}

			else
					{	//12345678
						//Переадресация с сообщением о результатах выполнения
						$warning_message = "Выполнено с ошибкой: остались данные о социальных сетях пользователя.";
						?>
						<script>
							location="<?php echo $DP_Config->domain_path.$DP_Config->backend_dir; ?>/users/usermanager?warning_message=<?php echo $warning_message.$s_page; ?>";
						</script>
						<?php
						exit;
					}
				}
				
				else
				{	//12345678
					//Переадресация с сообщением о результатах выполнения
					$success_message = "Удаление выполнено успешно. Данных о социальных сетях пользователей обнаружено не было.";
					?>
					<script>
						location="<?php echo $DP_Config->domain_path.$DP_Config->backend_dir; ?>/users/usermanager?success_message=<?php echo $success_message.$s_page; ?>";
					</script>
					<?php
					exit;
				}
				
				
			}
			else
			{
				//Переадресация с сообщением о результатах выполнения
				$warning_message = "Выполнено с ошибкой: остались привязки к группам - их нужно зачистить в ручную и разобраться, в чем ошибка";
				?>
				<script>
					location="<?php echo $DP_Config->domain_path.$DP_Config->backend_dir; ?>/users/usermanager?warning_message=<?php echo $warning_message.$s_page; ?>";
				</script>
				<?php
				exit;
			}




			................................................................
			ФИЛЬТРЫ

			ДОБАВЛЯЕМ ПЕРЕМЕННУЮ В ТЕХ МЕСТАХ, ГДЕ ЕСТЬ $unlocked;

			1.

			$user_id = "";
			$group_id = "";
			$email = "";
			$phone = "";
			$unlocked = "";
			$social_filter = "";


			......................

			2.
			$users_filter = json_decode($users_filter, true);
			$user_id = $users_filter["user_id"];
			$group_id = $users_filter["group_id"];
			$email = $users_filter["email"];
			$phone = $users_filter["phone"];
			$unlocked = $users_filter["unlocked"];
			$social_filter = $users_filter["social_filter"];


			..................................................................

			ДОБАВЛЯЕМ ПОСЛЕ БЛОКА ФИЛЬТРА РАЗБЛОКИРОВАН

			<div class="col-lg-4">
				<div class="form-group">
					<label for="" class="col-lg-6 control-label">
						Соц сети
					</label>
					<div class="col-lg-6">
						<select id="social_filter" class="form-control">
							<option value="-1">Все</option>
							<option value="1">Есть</option>
							<option value="0">Нет</option>
						</select>
						<script>
							document.getElementById("social_filter").value = <?php echo $social_filter; ?>;
						</script>
					</div>
				</div>
			</div>
			<div class="col-lg-4"></div>
<!------------------------------------------------------------------------------------>


УСТАНОВКА COOKIE (function filterUsers())

users_filter.social_filter = document.getElementById("social_filter").value;


СНЯТИЕ COOKIE (unsetFilterUsers())
users_filter.social_filter = -1;


<!------------------------------------------------------------------------------------------->
В таблице вывода пользователей добавляем новую колонку после колонки телефона
<th>
	<a href="javascript:void(0);" onclick="" id="social_sorter">Соц сети</a>
</th>



В условии, которое идёт в скрипте после, добавляем `soclai_flag`
if( array_search($sort_field, array('user_id', 'reg_variant', 'email', 'phone', 'time_registered', 'time_last_visit', 'admin_created', 'unlocked', 'social_flag') ) === false && 	array_search($sort_field,$profile_colomns_names_checked) === false )
{
	$sort_field = "user_id";
}			

В подстроку с условиям фильтрования пользовалетей добавляем 

//6. Соц сети 
if($users_filter["social_filter"] != -1)
{
	if($WHERE_CONDITIONS != "")
	{
		$WHERE_CONDITIONS .= " AND ";
	}
	$WHERE_CONDITIONS .= " `users`.`social` = ?";

	array_push($binding_values, $users_filter["social_filter"]);
}


Вставляем после вывода колонки телефона

<?php
								    //12345678
									//подгружаем иконки соц сетей
$SQL_get_social_user = "SELECT `social_user_data`.`user_id`,`social_user_data`.`social_id`,`social`.`social_img_url` FROM `social_user_data` INNER JOIN `social` ON `social_user_data`.`social_id` = `social`.`id` WHERE `social_user_data`.`user_id` = ?";
$query = $db_link_pdo_pdo->prepare($SQL_get_social_user);
$query->execute( array( $users_list_array["user_id"] ) );
?>
<td>
	<?
	while ($result_social = $query->fetch())	
	{
		?>
		<img width="20px;"src=<?php echo "https://".$_SERVER["SERVER_NAME"].$result_social["social_img_url"];?>>
		<?php
	}
	?>
</td>
<?
?>


