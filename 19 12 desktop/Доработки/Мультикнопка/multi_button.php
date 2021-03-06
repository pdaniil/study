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
	
	$SQL_select = "SELECT (SELECT `value`  FROM `multibutton` WHERE `type` = 'public') as `publicout`, (SELECT `value`  FROM `multibutton` WHERE `type` = 'vk') as `vkout`, (SELECT `value` FROM `multibutton` WHERE `type` = 'viber') as `viberout`, (SELECT `value` FROM `multibutton` WHERE `type` = 'tme') as `tmeout`, (SELECT `value` FROM `multibutton` WHERE `type` = 'whatsapp') as `whatsappout`, (SELECT `value` FROM `multibutton` WHERE `type` = 'phone') as `phoneout` , (SELECT `value` FROM `multibutton` WHERE `type` = 'place') as `placeout`";

	$query = $db_link->prepare($SQL_select);
	$query->execute();
	
	$result = $query->fetch();
	
if ($result["publicout"] == '1')
{

?>
<div class="multi_button">
	<div class="multi_btn">
		<a href="https://vk.me/<?echo $result["vkout"]; ?>" class="t825__messenger t825__vk t-name t-name_lg" target="_blank" rel="nofollow noopener"><svg width="62" height="62" xmlns="http://www.w3.org/2000/svg"><path d="M31 0C13.88 0 0 13.88 0 31c0 17.12 13.88 31 31 31 17.12 0 31-13.88 31-31C62 13.88 48.12 0 31 0zm13.274 34.964c.243.3.821.886 1.736 1.758h.02l.022.02.022.023.043.042c2.014 1.872 3.378 3.45 4.092 4.736.044.07.09.161.14.268.05.107.1.296.15.568.05.272.045.514-.011.728-.057.213-.235.411-.535.59-.3.178-.722.267-1.265.267l-5.485.087c-.343.071-.743.035-1.2-.107a5.247 5.247 0 0 1-1.114-.472l-.43-.258c-.428-.3-.928-.757-1.5-1.37a37.965 37.965 0 0 1-1.467-1.662 7.129 7.129 0 0 0-1.308-1.243c-.464-.336-.868-.446-1.21-.332-.043.015-.1.04-.172.075-.072.036-.193.139-.364.311a2.743 2.743 0 0 0-.461.632c-.136.25-.258.622-.365 1.115a7.028 7.028 0 0 0-.139 1.66c0 .214-.026.411-.075.59-.05.178-.104.31-.161.396l-.086.107c-.257.272-.635.429-1.135.471H29.55a9.714 9.714 0 0 1-3.128-.353c-1.07-.294-2.01-.671-2.817-1.136a25.333 25.333 0 0 1-2.207-1.413c-.665-.48-1.168-.89-1.51-1.232l-.536-.515c-.143-.142-.34-.357-.59-.643-.25-.286-.76-.935-1.531-1.95a53.332 53.332 0 0 1-2.272-3.236c-.743-1.143-1.617-2.65-2.625-4.521a70.642 70.642 0 0 1-2.796-5.828 1.703 1.703 0 0 1-.13-.578c0-.157.022-.272.065-.344l.086-.128c.214-.272.621-.408 1.221-.408l5.871-.042c.172.029.336.075.493.14.157.063.272.124.343.181l.107.065c.23.157.4.385.515.685.285.715.614 1.454.985 2.219.372.764.665 1.346.879 1.746l.343.621c.414.857.814 1.6 1.2 2.229.386.628.732 1.118 1.04 1.468.306.35.602.625.888.824.286.2.529.301.73.301.2 0 .392-.036.578-.107a.38.38 0 0 0 .107-.107c.043-.057.129-.214.258-.472.128-.257.224-.593.289-1.007.064-.414.132-.992.203-1.735.072-.744.072-1.636 0-2.679a10.036 10.036 0 0 0-.193-1.564c-.1-.471-.2-.8-.3-.986l-.128-.257c-.357-.485-.964-.792-1.822-.921-.186-.028-.15-.2.107-.514.243-.272.515-.485.815-.643.757-.37 2.464-.543 5.121-.515 1.171.015 2.136.107 2.893.279.285.072.525.168.718.29.192.12.339.292.44.513.099.221.174.45.224.685.05.236.075.562.075.975 0 .415-.007.808-.021 1.18-.015.37-.032.875-.053 1.51a53.173 53.173 0 0 0-.033 1.768c0 .157-.007.457-.021.9a16.86 16.86 0 0 0-.01 1.028c.006.243.031.533.074.868.043.336.125.615.246.835.121.222.282.397.483.525.114.03.235.058.364.086.128.029.314-.049.557-.236.242-.185.514-.432.814-.739.3-.306.671-.784 1.114-1.435a45.8 45.8 0 0 0 1.457-2.303 34.928 34.928 0 0 0 2.293-4.822c.057-.143.128-.268.214-.375a.947.947 0 0 1 .235-.226l.086-.064.107-.053c.043-.021.136-.043.279-.065.143-.02.285-.024.429-.01l6.171-.044c.557-.07 1.014-.053 1.37.054.358.107.58.225.665.353l.13.215c.327.915-.743 3.013-3.215 6.3a162.64 162.64 0 0 1-1.392 1.821c-1.115 1.43-1.757 2.365-1.927 2.809-.245.584-.145 1.163.298 1.733z" fill="#47668D" fill-rule="nonzero"></path></svg></a>
	</div>
	<div class="multi_btn">
		<a href="https://t.me/<? echo $result["tmeout"]; ?>" class="t825__messenger t825__telegram t-name t-name_lg" target="_blank" rel="nofollow noopener"><svg width="62" height="62" xmlns="http://www.w3.org/2000/svg"><g fill="#0087D0" fill-rule="nonzero"><path d="M31 0C13.88 0 0 13.88 0 31c0 17.12 13.88 31 31 31 17.12 0 31-13.88 31-31C62 13.88 48.12 0 31 0zm16.182 15.235l-6.737 31.207a.91.91 0 0 1-1.372.58l-10.36-6.777-5.449 5.002a.913.913 0 0 1-1.447-.385l-3.548-11.037L8.74 29.97c-.73-.329-.72-1.477.029-1.764l37.193-13.985c.67-.256 1.361.319 1.219 1.013z"></path><path d="M22.966 41.977l.606-5.754 16.807-16.43-20.29 13.325z"></path></g></svg></a>
	</div>
	<div class="multi_btn">
		<a href="https://wa.me/<? echo $result["whatsappout"]; ?>" class="t825__messenger t825__whatsapp t-name t-name_lg" rel="nofollow noopener"><svg width="62" height="62" xmlns="http://www.w3.org/2000/svg"><g fill="#27D061" fill-rule="nonzero"><path d="M32.367 14.888c-8.275 0-15.004 6.726-15.007 14.993a14.956 14.956 0 0 0 2.294 7.98l.356.567-1.515 5.533 5.677-1.488.548.325a14.979 14.979 0 0 0 7.634 2.09h.006c8.268 0 14.997-6.727 15-14.995a14.9 14.9 0 0 0-4.389-10.608 14.898 14.898 0 0 0-10.604-4.397zm8.417 21.34c-.369 1.052-2.138 2.013-2.989 2.142-.763.116-1.728.164-2.789-.179a25.28 25.28 0 0 1-2.524-.949c-4.444-1.95-7.345-6.502-7.566-6.802-.222-.301-1.809-2.443-1.809-4.661 0-2.218 1.144-3.307 1.55-3.759.406-.451.886-.564 1.181-.564.295 0 .591.003.849.016.272.014.637-.105.996.773.37.903 1.255 3.12 1.366 3.346.11.225.185.488.037.79-.148.3-.222.488-.443.75-.222.264-.465.588-.664.79-.222.224-.453.469-.194.92.258.45 1.147 1.926 2.463 3.12 1.692 1.535 3.119 2.011 3.562 2.237.443.226.701.188.96-.113.258-.3 1.106-1.316 1.401-1.766.295-.45.59-.376.997-.226.406.15 2.583 1.24 3.026 1.466.443.226.738.338.849.526.11.188.11 1.09-.259 2.143z"></path><path d="M31 0C13.88 0 0 13.88 0 31c0 17.12 13.88 31 31 31 17.12 0 31-13.88 31-31C62 13.88 48.12 0 31 0zm1.283 47.573h-.007c-3 0-5.948-.75-8.566-2.171l-9.502 2.48 2.543-9.243a17.735 17.735 0 0 1-2.392-8.918c.003-9.836 8.044-17.838 17.924-17.838 4.795.002 9.296 1.86 12.68 5.232 3.384 3.371 5.247 7.853 5.245 12.62-.004 9.836-8.046 17.838-17.925 17.838z"></path></g></svg></a>
	</div>
	<div class="multi_btn">
		<a href="viber://chat?number=%2B<? echo $result["viberout"];?>" class="t825__messenger t825__viber t-name t-name_lg" rel="nofollow noopener"><svg width="62" height="62" xmlns="http://www.w3.org/2000/svg"><path d="M31 0C13.88 0 0 13.88 0 31c0 17.12 13.88 31 31 31 17.12 0 31-13.88 31-31C62 13.88 48.12 0 31 0zm-1.162 16.092c.228-.005.466.03.671.03.083 0 .16-.007.227-.024 7.375.268 13.81 6.972 13.677 14.347 0 .67.268 1.743-.805 1.743s-.805-1.073-.805-1.877a23.832 23.832 0 0 0-.48-2.725 16.516 16.516 0 0 0-.747-2.355 11.716 11.716 0 0 0-1.657-2.886c-1.992-2.516-5.018-3.959-9.317-4.637-.67-.134-1.61 0-1.61-.805 0-.697.402-.812.846-.811zm10.15 14.621c-1.207.134-.94-.939-1.073-1.609-.805-4.693-2.414-6.436-7.24-7.509-.671-.134-1.743 0-1.61-1.072.135-1.073 1.207-.67 1.878-.536 4.827.536 8.715 4.558 8.715 9.117-.134.536.268 1.475-.67 1.61zm-2.95-2.413c0 .536 0 1.206-.805 1.34-.536 0-.805-.402-.939-.938-.134-2.011-1.207-3.084-3.217-3.353-.537-.134-1.207-.268-.94-1.073.135-.536.67-.536 1.208-.536 2.28-.133 4.693 2.28 4.693 4.56zm7.508 14.75c-.805 2.279-3.62 4.558-6.034 4.558-.268-.134-.939-.134-1.609-.402-10.458-4.559-18.235-11.934-22.526-22.66-1.475-3.487.134-6.57 3.755-7.777a2.615 2.615 0 0 1 2.01 0c1.61.536 5.498 6.034 5.632 7.643.134 1.34-.805 2.01-1.609 2.547-1.609 1.073-1.609 2.548-.939 4.023 1.61 3.486 4.29 5.765 7.643 7.375 1.207.536 2.414.536 3.218-.805 1.475-2.28 3.352-2.28 5.363-.805.939.67 2.011 1.341 2.95 2.146 1.342 1.072 2.95 2.01 2.146 4.156z" fill="#935BBE" fill-rule="nonzero"></path></svg></a>
	</div>
	<div class="multi_btn"> 
		<a href="tel:+<? echo $result["phoneout"]; ?>" class="t825__messenger t825__phone t-name t-name_lg" rel="nofollow noopener"><svg width="62" height="62" fill="#004d73" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 98.6 98.6"><path d="M49.2 0C22 0-.1 22.1-.1 49.3S22 98.6 49.2 98.6s49.3-22.1 49.3-49.3S76.5 0 49.2 0z"></path><path d="M77.1 67.9L64.4 55c-.9-.9-2.2-.9-3.1 0l-7 7c-1.4 1.4-3.5 1.4-4.9 0l-13-13c-1.4-1.4-1.4-3.5 0-4.9l7-7c.9-.9.9-2.2 0-3.1L30.6 21.4c-.9-.9-2.4-.8-3.2.1-4.1 5-11.6 18.9 9 39.9l.3.3.3.3c21.1 20.7 35 13.2 40 9.1 1-.8 1.1-2.3.1-3.2z" fill="#fff"></path></svg></a>
	</div>
	
	<a style="width: " href="#" id="popup__toggle" onclick="return false;">
		<div class="img-circle" style="transform-origin: center; width: 58px; height: 58px;">
			<div class="img-circleblock" style="transform-origin: center;"><img style="" class="element" src="<?php echo "http://".$_SERVER["SERVER_NAME"]."/modules/shop/multibutton/btn_phone.png"; ?>" />
			</div>
		</div>
	</a>

	<script>
		const btn_action_call = document.querySelector('#popup__toggle');
		const arr_multi_btn = document.querySelectorAll('.multi_btn');
		let translate_exp = 0;
		let flag_btn_multi = false;
		btn_action_call.addEventListener('click', ()=> {
			for (let i = 0; i<arr_multi_btn.length; i++)
			{
				translate_exp = i + 1;
				arr_multi_btn[i].classList.toggle('action_multi_btn_'+translate_exp);
				if (flag_btn_multi)
				{
					arr_multi_btn[i].classList.toggle('action_multi_btn_out_'+translate_exp);
				}
			}
			if (!flag_btn_multi)
			{
				flag_btn_multi = true;
			}
		});


	</script>
</div>

<?
}
?>


<style>

		#popup__toggle
		{

		}

		.multi_button
		{
			position: fixed;
			z-index: 999;
			
			<?php if ($result["placeout"] == 'ЛВ') { ?>
			margin-top: 150px;
			margin-left: 50px;
			<? }
			else if ($result["placeout"] == 'ЛН') { ?>
			margin-top: 40%;
			margin-left: 50px;
			<?
			
			}
			else if ($result["placeout"] == 'ПН') { ?>
			margin-top: 40%;
			margin-left: 90%;
			<?
			
			}
			else
			{
				?>
				
					margin-top: 150px;
					margin-left: 90%;
				
				<?
			}
			?>
		}
		.multi_btn{
			position: absolute;
			opacity: 0;
		}


		.element {
			transform: rotate(-140deg);
			width: 34px;
			margin-left: 12px;
			margin-top: 12px;
			margin-bottom: 0px;
		}
		.img-circle{
			background-color:#29AEE3;
			box-sizing:content-box;-webkit-box-sizing:content-box;
		}
		.img-circle{
			box-sizing:content-box;
			-webkit-box-sizing:content-box;
			width:72px;
			height:72px;
			bottom: 14px;
			right: 49px;
			
			-webkit-border-radius: 100%;
			-moz-border-radius: 100%;
			border-radius: 100%;
			border: 2px solid transparent;opacity: .7;
			
			animation-duration: 1.5s;
			-webkit-animation-duration: 1.5s;
			animation-iteration-count: infinite;
			-webkit-animation-iteration-count: infinite;
			opacity: 0.8;
		}
		.img-circle:hover
		{
			animation-name: pulse;
			-webkit-animation-name: pulse;
		}
		.img-circleblock{
			box-sizing:content-box;
			-webkit-box-sizing:content-box;
			width:72px;height:72px;
			background-image:url(images/mini.png);
			background-position: center center;
			background-repeat:no-repeat;
			animation-name: tossing_low;
			-webkit-animation-name: tossing_low;
			animation-duration: 1.5s;
			-webkit-animation-duration: 1.5s;
			animation-iteration-count: infinite;
			-webkit-animation-iteration-count: infinite;
		}

		.img-circleblock:hover
		{
			animation-name: tossing;
			-webkit-animation-name: tossing;

		}

		.img-circle:hover{opacity: 1;}


		@keyframes trans_btn {
			100%
			{
				transform: translate(0px;20px);
			}
		}


		@keyframes pulse {
			0% {
				transform: scale(0.9);opacity: 1;
			}

			50% {
				transform: scale(1); opacity: 1; 
			}   
			100% {
				transform: scale(0.9);opacity: 1;}
			}
			@-webkit-keyframes pulse {
				0% {
					-webkit-transform: scale(0.95);opacity: 1;
				}
				50% {
					-webkit-transform: scale(1);opacity: 1;
				}   
				100% {
					-webkit-transform: scale(0.95);opacity: 1;
				}
			}
			@keyframes tossing_low {
				0% {transform: rotate(-2deg);}
				5% {transform: rotate(2deg);}
				10% {transform: rotate(-2deg);}
				15% {transform: rotate(2deg);}
				20% {transform: rotate(-2deg);}
				30% {transform: rotate(2deg);}
				40% {transform: rotate(-2deg);}
				50% {transform: rotate(2deg);}
				60% {transform: rotate(-2deg);}
				70% {transform: rotate(2deg);}
				80% {transform: rotate(-2deg);}
				90% {transform: rotate(2deg);}
				100% {transform: rotate(-2deg);}

			}
			@keyframes tossing {
				0% {transform: translate(1px, 0px);}
				5% {transform: translate(-1px, 0px);}
				10% {transform: translate(1px, 0px);}
				15% {transform: translate(-1px, 0px);}
				20% {transform: translate(1px, 0px);}
				25% {transform: translate(-1px, 0px);}
				30% {transform: translate(1px, 0px);}
				35% {transform: translate(-1px, 0px);}
				40% {transform: translate(1px, 0px);}
				45% {transform: translate(-1px, 0px);}
				50% {transform: translate(1px, 0px);}
				55% {transform: translate(-2 px, 0px);}
				60% {transform: translate(2px, 0px);}
				65% {transform: translate(-2px, 0px);}
				70% {transform: translate(2px, 0px);}
				80% {transform: translate(-2px, 0px);}
				90% {transform: translate(2px, 0px);}
				100% {transform: translate(-2px, 0px);}
			}

			
			<?php if ($result["placeout"] == 'ЛВ' || $result["placeout"] == 'ПВ') {  ?>
			
			.action_multi_btn_1 {
				transition-duration: .2s;
				transform: translate(0, 100px);
				opacity: 1;

			}

			.action_multi_btn_1:hover
			{
				transform: scale(1.2) translate(0, 80px);
			}

			.action_multi_btn_2 {
				transition-duration: .3s;
				transform: translate(0, 200px);
				opacity: 1;

			}
			.action_multi_btn_2:hover
			{
				transition-duration: .2s;
				transform: scale(1.2) translate(0, 170px);
			}
			.action_multi_btn_3 {

				transition-duration: .4s;
				transform: translate(0, 300px);
				opacity: 1;
			}
			.action_multi_btn_3:hover
			{
				transition-duration: .2s;
				transform: scale(1.2) translate(0, 250px);
			}
			.action_multi_btn_4 {
				transition-duration: .5s;
				transform: translate(0, 400px);
				opacity: 1;
			}
			.action_multi_btn_4:hover
			{
				transition-duration: .2s;
				transform: scale(1.2) translate(0, 330px);
			}
			.action_multi_btn_5 {
				transition-duration: .6s;
				transform: translate(0, 500px);
				opacity: 1;
			}
			.action_multi_btn_5:hover
			{
				transition-duration: .2s;
				transform: scale(1.2) translate(0, 420px);
			}
			.action_multi_btn_out_1 {
				transition-duration: .2s;
				transform: translate(0, 0);
				opacity: 0;

			}
			.action_multi_btn_out_2 {
				transition-duration: .2s;
				transform: translate(0, 0);
				opacity: 0;

			}
			.action_multi_btn_out_3 {

				transition-duration: .2s;
				transform: translate(0, 0);
				opacity: 0;
			}
			.action_multi_btn_out_4 {
				transition-duration: .2s;
				transform: translate(0, 0);
				opacity: 0;
			}
			.action_multi_btn_out_5 {
				transition-duration: .2s;
				transform: translate(0, 0);
				opacity: 0;
			}
			<?php }
			
			else if ($result["placeout"] == 'ПН' || $result["placeout"] == 'ЛН')
			{
				?>
				
				.action_multi_btn_1 {
				transition-duration: .2s;
				transform: translate(0, -100px);
				opacity: 1;

			}

			.action_multi_btn_1:hover
			{
				transform: scale(1.2) translate(0, -80px);
			}

			.action_multi_btn_2 {
				transition-duration: .3s;
				transform: translate(0, -200px);
				opacity: 1;

			}
			.action_multi_btn_2:hover
			{
				transition-duration: .2s;
				transform: scale(1.2) translate(0, -170px);
			}
			.action_multi_btn_3 {

				transition-duration: .4s;
				transform: translate(0, -300px);
				opacity: 1;
			}
			.action_multi_btn_3:hover
			{
				transition-duration: .2s;
				transform: scale(1.2) translate(0, -250px);
			}
			.action_multi_btn_4 {
				transition-duration: .5s;
				transform: translate(0, -400px);
				opacity: 1;
			}
			.action_multi_btn_4:hover
			{
				transition-duration: .2s;
				transform: scale(1.2) translate(0, -330px);
			}
			.action_multi_btn_5 {
				transition-duration: .6s;
				transform: translate(0, -500px);
				opacity: 1;
			}
			.action_multi_btn_5:hover
			{
				transition-duration: .2s;
				transform: scale(1.2) translate(0, -420px);
			}
			.action_multi_btn_out_1 {
				transition-duration: .2s;
				transform: translate(0, 0);
				opacity: 0;

			}
			.action_multi_btn_out_2 {
				transition-duration: .2s;
				transform: translate(0, 0);
				opacity: 0;

			}
			.action_multi_btn_out_3 {

				transition-duration: .2s;
				transform: translate(0, 0);
				opacity: 0;
			}
			.action_multi_btn_out_4 {
				transition-duration: .2s;
				transform: translate(0, 0);
				opacity: 0;
			}
			.action_multi_btn_out_5 {
				transition-duration: .2s;
				transform: translate(0, 0);
				opacity: 0;
			}
				
				
				<?
			}
			
		?>

		</style>





