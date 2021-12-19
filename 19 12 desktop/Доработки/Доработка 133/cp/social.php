<?php
/**
 * Страничный скрипт - просмотр соц сетей.
*/
defined('_ASTEXE_') or die('No access');

if (true)
{
    require_once("content/control/actions_alert.php");//Вывод сообщений о результатах действий
?>
    
    
	
	<div class="col-lg-12">
		<div class="hpanel">
			<div class="panel-heading hbuilt">
				Действия
			</div>
			<div class="panel-body">
			   <a class="panel_a" onclick="unselect_block();" href="javascript:void(0);">
					<div class="panel_a_img" style="background: url('/cp/templates/bootstrap_admin/images/selection_off.png') 0 0 no-repeat;"></div>
					<div class="panel_a_caption">Снять выделение</div>
				</a>
				<a class="panel_a" href="/<?php echo $DP_Config->backend_dir; ?>">
					<div class="panel_a_img" style="background: url('/<?php echo $DP_Config->backend_dir; ?>/templates/<?php echo $DP_Template->name; ?>/images/power_off.png') 0 0 no-repeat;"></div>
					<div class="panel_a_caption">Выход</div>
				</a>
			</div>
		</div>
	</div>
	
	<!--Стили для блока соц сетей -->
	<style>
		.social_block 
		{
			cursor: pointer;
			padding: 10px;
			border-radius: 10px;
			
		}
		.social_block:hover
		{
			box-shadow: inset 2px 2px 5px rgba(154, 147, 140, 0.5), 1px 1px 5px rgba(255, 255, 255, 1);
		}
		
		.on_select
		{
			
			padding: 10px;
			border-radius: 10px;
			box-shadow: inset 2px 2px 10px rgba(154, 147, 140, 0.8), 2px 2px 2px rgba(255, 255, 255, 1);
		}
		
		.span_social
		{
			font-size: 20px;
		}
		
	</style>
	
	<div class="col-lg-6">
		<div class="hpanel">
			<div class="panel-heading hbuilt">
				Список социальных сетей
			</div>
			<div id="place_for_social" class="panel-body text-center">
				
				<?php
					//Выбираем все соц сети из бд и выводим в блок
					$SQL_get_social = "SELECT * FROM `social`";
					$query = $db_link->prepare($SQL_get_social);
					$query->execute();
				
					while ($social = $query->fetch())
					{
						
						?>
							<div  class="col-lg-12 social_block">
								<img width="50" height="50" src="<?php echo "https://".$_SERVER["SERVER_NAME"].$social["social_img_url"];?>" />
								<span class="span_social"><?php echo $social["social_caption"]; ?></span>
								<input type="hidden" value="<?php echo $social["id"]; ?>"><!-- нужно для корректного отображения блока настроек -->
							</div>
							<div class="col-lg-12">
								&nbsp;
							</div>
							
						<?php
					}
				
				?>
				
			</div>
		</div>
	</div>
	
	<script>
	
		let show_options_block = null;
		//Функция снять выделение
		function unselect_block ()
		{
			let selected_block = document.querySelector(".on_select");
				if (selected_block != null)
				{
					selected_block.classList.remove('on_select');
					selected_block.classList.add('social_block');
				}
			if (show_options_block != null)	
				show_options_block.classList.add('options_block');		
					
		}
		
		//Функция добавляет стиль выбранного блока соц сетей и отображения соответствующего блока настроек
		function bind_event_click(block)
		{
			unselect_block();
			block.classList.add('on_select');
			block.classList.remove('social_block');
				
			let j = 0;
				
			while (block.childNodes[j].nodeName != 'INPUT')
			{
				j++;
			}
				
			showOptions(block.childNodes[j].value);
		}
	
		let arr_social_block = document.querySelectorAll('.social_block');
		//Устанавливаем событие click на каждый блок соц сети
		for (let i = 0; i < arr_social_block.length; i++)
		{
			arr_social_block[i].addEventListener('click', ()=> bind_event_click(arr_social_block[i]));
		}
		
		
	</script>
	
	
	<style>
		.options_block
		{
			display: none;
		}
	</style>
	
	<div class="col-lg-6">
		<div class="hpanel">
			<div class="panel-heading hbuilt">
				Настройки
			</div>
			<div class="panel-body" style="min-height: 550px;">
				<?php 
					//Выбираем все настройки всех соц сетей
					$SQL_get_options = "SELECT * FROM `social_options`";
					$query = $db_link->prepare($SQL_get_options);
					$query->execute();
					$options = null;
					while ($options = $query->fetch()) 
					{
						//Для каждой строки настроек ищем соответств соц сеть
						if (!is_null($options))
						{
							$SQL_get_social = "SELECT * FROM `social` WHERE `id` = ?";
							$query_social = $db_link->prepare($SQL_get_social);
							$query_social->execute( array( $options["id_social"] ) );
							$social = $query_social->fetch();
						}
						
						?>
						
						<div class="options_block options_block_<?php echo (!is_null($options)) ? $options["id_social"] : $id_new_social;?>"><!--Добавляем класс options_block_{id соответсв соц сети} -->
							<div class="form-group">
								<label for="" class="col-lg-6 control-label">ID</label>
								<div class="col-lg-6"><?php echo (!is_null($options)) ? $options["id_social"] : $id_new_social;?></div>
							</div>
							<div class="hr-line-dashed col-lg-12"></div>
							<div class="form-group">
								<label for="" class="col-lg-6 control-label">Id приложения</label>
								<div class="col-lg-6">
									<input readonly onkeyup="" type="text"  value="<?php echo $options["client_id"];?>" class="form-control">
								</div>
							</div>
							<div class="hr-line-dashed col-lg-12"></div>
							<div class="form-group">
								<label for="" class="col-lg-6 control-label">Секретный код приложения</label>
								<div class="col-lg-6">
									<input readonly onkeyup="" type="text"  value="<?php echo $options["secret_code"];?>" class="form-control">
								</div>
							</div>
							<div class="hr-line-dashed col-lg-12"></div>
							<div class="form-group">
								<label for="" class="col-lg-6 control-label">Адрес обработчика</label>
								<div class="col-lg-6">
									<input readonly onkeyup="" type="text"  value="<?php echo $options["uri_redirect"];?>" class="form-control">
								</div>
							</div>
							<div class="hr-line-dashed col-lg-12"></div>
							<div class="form-group">
								<label for="" class="col-lg-6 control-label">Название</label>
								<div class="col-lg-6">
									<input readonly onkeyup="" type="text"  value="<?php echo $social["social_caption"];?>" class="form-control">
								</div>
							</div>
							<div class="hr-line-dashed col-lg-12"></div>
							<div class="form-group">
								<label for="" class="col-lg-6 control-label">URL</label>
								<div class="col-lg-6">
									<input readonly onkeyup="" type="text"  value="<?php echo $social["social_name"];?>" class="form-control">
								</div>
							</div>
							<div class="hr-line-dashed col-lg-12"></div>
							<div class="form-group">
								<label for="" class="col-lg-6 control-label">Иконка</label>
								<div class="col-lg-6">
									<input readonly onkeyup="" type="text"  value="<?php echo $social["social_img_url"];?>" class="form-control">
								</div>
							</div>
							<div class="hr-line-dashed col-lg-12"></div>
						</div>
						<?php
					} 
				?>
			</div>
		</div>
	</div>
	<?php 
		//Функция убирает показаный блок настроек и показывает тот, у которого class = options_block_{переданный id}
	?>
	<script>
		
		function showOptions(id)
		{
			let options_block = document.querySelector('.options_block_' + id);
			options_block.classList.remove('options_block');
			show_options_block = options_block;
		}
		
	</script>
<?php
}//else - Аргументов нет - просто выводим список соц сетей
?>