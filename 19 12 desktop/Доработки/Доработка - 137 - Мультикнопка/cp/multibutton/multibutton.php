<?php 

require_once($_SERVER["DOCUMENT_ROOT"]."/config.php");
$DP_Config = new DP_Config;

//Подключение к БД
try
{
	$db_link = new PDO('mysql:host='.$DP_Config->host.';dbname='.$DP_Config->db, $DP_Config->user, $DP_Config->password);
}
catch (PDOException $e) 
{
    echo "Ошибка подключения к БД: ".mysqli_connect_error();
    exit;
}
$db_link->query("SET NAMES utf8;");


?>

<?php
/**
 * Страничный скрипт для страницы пользователя:
 * - создание;
 * - редактирование.
*/
defined('_ASTEXE_') or die('No access');
?>


<?php
if($_POST["save_action"])
{
    if($_POST["save_action"] == "save")//Создаем пользователя
    {
        
			$SQL_update = "UPDATE `multibutton` SET `whatsapp` = ?, `viber` = ?, `tme` = ?, `phone` = ?, `vk` = ?, `insta` = ?, `placement` = ?, `public` = ?, `color` = ? WHERE id = ?;";		
			$query = $db_link->prepare($SQL_update);

			$message = 'Настройки успешно сохранены!';
			if($query->execute( array( $_POST["whatsappout"],$_POST["viberout"],$_POST["tmeout"],$_POST["phoneout"],$_POST["vkout"],$_POST["instaout"],$_POST["placeout"],$_POST["publicout"],$_POST["colorout"], $_POST["multibutton_idout"]) ) != true	) {
				$message = 'Ошибка сохранения';

				?>
        <script>
            location="<?php echo $DP_Config->domain_path.$DP_Config->backend_dir; ?>/shop/multiknopka?error_message=<?php echo $message; ?>";
        </script>
        <?php
			}

			?>
			<script>
					location="<?php echo $DP_Config->domain_path.$DP_Config->backend_dir; ?>/shop/multiknopka?success_message=<?php echo $message; ?>";
			</script>
			<?php
		}
}//if() - действия по сохранению

    require_once("content/control/actions_alert.php");//Вывод сообщений о результатах действий
    
		$SQL_select = "SELECT * FROM `multibutton` LIMIT 1";
		$query = $db_link->prepare($SQL_select);
		$query->execute();
		$result_fetch = $query->fetchAll();
		
		$result = $result_fetch[0];

		//Получаем настройки
		$public = isset($result['public']) ? $result['public'] : 0;
		$whatsapp = (isset($result['whatsapp']) && !empty($result['whatsapp'])) ? $result['whatsapp'] : '';
		$viber = (isset($result['viber']) && !empty($result['viber'])) ? $result['viber'] : '';
		$tme = (isset($result['tme']) && !empty($result['tme'])) ? $result['tme'] : '';
		$vk = (isset($result['vk']) && !empty($result['vk'])) ? $result['vk'] : '';
		$insta = (isset($result['insta']) && !empty($result['insta'])) ? $result['insta'] : '';
		$phone = (isset($result['phone']) && !empty($result['phone'])) ? $result['phone'] : '';
		$placement = isset($result['placement']) ? $result['placement'] : 'bottomRight';
		$multibutton_id = isset($result['id']) ? $result['id'] : 1;
		$color = (isset($result['color']) && !empty($result['color'])) ? $result['color'] : '';

	?>
	
	
	<div class="col-lg-12">
		<div class="hpanel">
			<div class="panel-heading hbuilt">
				Действия
			</div>
			<div class="panel-body">
				<a class="panel_a" onClick="save_action();" href="javascript:void(0);">
					<div class="panel_a_img" style="background: url('/<?php echo $DP_Config->backend_dir; ?>/templates/<?php echo $DP_Template->name; ?>/images/save.png') 0 0 no-repeat;"></div>
					<div class="panel_a_caption">Сохранить</div>
				</a>
			 
				<a class="panel_a" href="/<?php echo $DP_Config->backend_dir; ?>">
					<div class="panel_a_img" style="background: url('/<?php echo $DP_Config->backend_dir; ?>/templates/<?php echo $DP_Template->name; ?>/images/power_off.png') 0 0 no-repeat;"></div>
					<div class="panel_a_caption">Выход</div>
				</a>
			</div>
		</div>
	</div>
    
    
  <div class="col-lg-12">
		<div class="hpanel">
			<div class="panel-heading hbuilt">
				Настройки
			</div>
			<div class="panel-body">

				<input type="hidden" id="multibutton_id" name="multibutton_id" value="<?= $multibutton_id ;?>">
				<div class="form-group">
						<label for="" class="col-lg-6 control-label">
							Публикация
						</label>
						<div class="col-lg-5">
							<input type="checkbox" id="public" <?= ($public == '1') ? 'checked' : '' ?>  class="form-control">
						</div>
						
					</div>
				<div class="hr-line-dashed col-lg-12"></div>
				<div class="form-group">
						<label for="" class="col-lg-6 control-label">
							What's App
						</label>
						<div class="col-lg-5">
							<input type="text" id="whatsapp" value="<?php echo $whatsapp;?>" class="form-control">
						</div>
						
					</div>
				<div class="hr-line-dashed col-lg-12"></div>
				<div class="form-group">
						<label for="" class="col-lg-6 control-label">
							Viber
						</label>
						<div class="col-lg-5">
							<input type="text" id="viber" value="<?php echo $viber;?>" class="form-control">
						</div>
						
					</div>
				<div class="hr-line-dashed col-lg-12"></div>
				<div class="form-group">
						<label for="" class="col-lg-6 control-label">
							Телефон
						</label>
						<div class="col-lg-5">
							<input type="text" id="phone" value="<?php echo $phone;?>" class="form-control">
						</div>
						
					</div>
				<div class="hr-line-dashed col-lg-12"></div>
				<div class="form-group">
						<label for="" class="col-lg-6 control-label">
							Ссылка ВК
						</label>
						<div class="col-lg-5">
							<input type="text" id="vk" value="<?php echo $vk;?>" class="form-control">
						</div>
						
					</div>
				<div class="hr-line-dashed col-lg-12"></div>
				<div class="form-group">
						<label for="" class="col-lg-6 control-label">
							Телеграмм
						</label>
						<div class="col-lg-5">
							<input type="text" id="tme" value="<?php echo $tme;?>" class="form-control">
						</div>
						
					</div>
				<div class="hr-line-dashed col-lg-12"></div>
				<div class="form-group">
						<label for="" class="col-lg-6 control-label">
							Группа Инстаграм
						</label>
						<div class="col-lg-5">
							<input type="text" id="insta" value="<?php echo $insta;?>" class="form-control">
						</div>
						
					</div>
				<div class="hr-line-dashed col-lg-12"></div>
				<div class="form-group">
						<label for="" class="col-lg-6 control-label">
							Положение
						</label>
						<div class="col-lg-5">
							<select id="place" class="form-control" aria-label="Default select example">
								<?php

									$placement_array = array(
										'middleLeft' => 'Слево посередине',
										'bottomLeft' => 'Слево внизу',
										'middleRight' => 'Справа посередине',
										'bottomRight' => 'Справа внизу',
									);

								?>

								<?php foreach ($placement_array as $key => $value) : ?>

									<?php
										$selected = '';
										if($key == $placement) $selected = 'selected';
									?>

									<option <?= $selected ;?> value="<?= $key ;?>"><?= $value ;?></option>

								<?php endforeach; ?>
							  
							</select>
						</div>
						
					</div>
				<div class="hr-line-dashed col-lg-12"></div>
				<div class="form-group">
						<label for="" class="col-lg-6 control-label">
							Цвет
						</label>
						<div class="col-lg-5">
							<input type="color" id="color" value="<?php echo $color;?>" class="form-control">
						</div>
						
					</div>
				<div class="hr-line-dashed col-lg-12"></div>
			</div>
		</div>
	</div>
	
	<form id="multiform" method="post">
		<input type="hidden" name="vkout" id="vkout" value="">
		<input type="hidden" name="tmeout" id="tmeout" value="">
		<input type="hidden" name="viberout" id="viberout" value="">
		<input type="hidden" name="whatsappout" id="whatsappout" value="">
		<input type="hidden" name="instaout" id="instaout" value="">
        <input type="hidden" name="colorout" id="colorout" value="">
		<input type="hidden" name="phoneout" id="phoneout" value="">
		<input type="hidden" name="placeout" id="placeout" value="">
		<input type="hidden" name="publicout" id="publicout" value="">
		<input type="hidden" name="multibutton_idout" id="multibutton_idout" value="">
		<input type="hidden" name="save_action" value="save">
	</form>
	
	<script>
		function save_action()
		{
			const form = document.querySelector('#multiform');
			
			document.querySelector('#vkout').value = document.querySelector('#vk').value;
			document.querySelector('#tmeout').value = document.querySelector('#tme').value;
			document.querySelector('#colorout').value = document.querySelector('#color').value;
			document.querySelector('#viberout').value = document.querySelector('#viber').value;
			document.querySelector('#whatsappout').value = document.querySelector('#whatsapp').value;
			document.querySelector('#instaout').value = document.querySelector('#insta').value;
			document.querySelector('#placeout').value = document.querySelector('#place').value;
			document.querySelector('#phoneout').value = document.querySelector('#phone').value;
			document.querySelector('#multibutton_idout').value = document.querySelector('#multibutton_id').value;
			
			if (document.querySelector('#public').checked)
			{
				document.querySelector('#publicout').value = '1';
			}
			else
			{
				document.querySelector('#publicout').value = '0';
			}
			
			
			form.submit();
		}
	</script>
	
    <?php
  
   
//else //Действий нет - выводим страницу

?>