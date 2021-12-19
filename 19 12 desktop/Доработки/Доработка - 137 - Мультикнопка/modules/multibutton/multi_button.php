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
	exit("No DB connect");
}
$db_link->query("SET NAMES utf8;");


$SQL_select = "SELECT *  FROM `multibutton`";

$query = $db_link->prepare($SQL_select);
$query->execute();
$result_mb = $query->fetchAll();

$result = $result_mb[0];
	
if($result["public"] == '1')
{

	$placement = (isset($result["placement"]) && !empty($result["placement"])) ? $result["placement"] : 'bottomRight';

?>
<link href="/modules/shop/multibutton/multibutton.css?v=1.01" rel="stylesheet">
<script src="https://kit.fontawesome.com/79e6ad7d35.js" crossorigin="anonymous"></script>
<div class="multi-button <?= $placement ;?>">
	<div role="button" class="multi-button__toggle is-animating" style="background: <?= $result["color"]; ?>;"></div>
	<ul class="multi-button__submenu">
		<?php if(!empty($result["vk"])) : ?>
		<li>
			<span>Напишите нам в группу ВК</span>
			<a href="https://vk.me/<?= $result["vk"]; ?>" class="vk" target="_blank" rel="nofollow noopener"><i class="fa fa-vk" aria-hidden="true"></i></a>
		</li>
		<?php endif; ?>
		<?php if(!empty($result["tme"])) : ?>
		<li>
			<span>Напишите нам в Telegram</span>
			<a href="https://t.me/<?= $result["tme"]; ?>" class="tme" target="_blank" rel="nofollow noopener"><i class="fab fa-telegram-plane" aria-hidden="true"></i></a>
		</li>
		<?php endif; ?>
		<?php if(!empty($result["whatsapp"])) : ?>
		<li>
			<span>Напишите нам в чат Whatsapp</span>
			<a href="https://wa.me/<?= $result["whatsapp"]; ?>" class="whatsapp" rel="nofollow noopener"><i class="fa fa-whatsapp" aria-hidden="true"></i></a>
		</li>
		<?php endif; ?>
		<?php if(!empty($result["viber"])) : ?>
		<li>
			<span>Позвоните нам через Viber</span>
			<a href="viber://chat?number=%2B<?= $result["viber"];?>" class="viber" rel="nofollow noopener"><i class="fab fa-viber" aria-hidden="true"></i></a>
		</li>
		<?php endif; ?>
		<?php if(!empty($result["phone"])) : ?>
		<li>
			<span>Позвоните нам по мобильной связи</span>
			<a href="tel:+<?= $result["phone"]; ?>" class="phone" rel="nofollow noopener"><i class="fa fa-phone-alt" aria-hidden="true"></i></a>
		</li>
		<?php endif; ?>
		<?php if(!empty($result["insta"])) : ?>
		<li>
			<span>Свяжитесь с нами в Instagram</span>
			<a href="https://www.instagram.com/<?= $result["insta"]; ?>" class="instagram" rel="nofollow noopener"><i class="fab fa-instagram"></i></a>
		</li>
		<?php endif; ?>
	</ul>
</div>


<script>
	jQuery(document).ready(function() {
		jQuery(document).on('click', '.multi-button__toggle', function() {
			jQuery(this).toggleClass('active');
		});
		jQuery(document).mouseup(function (e) {
	    let container_toggle = $(".multi-button");
		    if (container_toggle.has(e.target).length === 0) {
					if(jQuery('.multi-button__toggle').hasClass('active')) {
						jQuery('.multi-button__toggle').removeClass('active');
					}
		    }
		  });
	});
</script>

<?
}
?>