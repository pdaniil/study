<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/config.php");
$DP_Config = new DP_Config;//Конфигурация CMS


$test_file = fopen($_SERVER["DOCUMENT_ROOT"]."/license/test.txt", "w");

fwrite($test_file, "content");

$content = file_get_contents($DP_Config->domain_path."license/test.txt");

echo $content;
?>