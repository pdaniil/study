<?php
	$login = 'avtomagickr';
	$password = '1h6a2d2c';
	
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, "http://srv.vivat-uae.net/gettoken?login=".$login."&pwd=".$password);
				curl_setopt($ch, CURLOPT_FAILONERROR,1);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
				curl_setopt($ch, CURLOPT_TIMEOUT, 30);
				$xmlstr = curl_exec($ch); 
				curl_close($ch);
				$XMLobj = new SimpleXMLElement($xmlstr);
			echo "<pre>";
			print_r($XMLobj);
			echo "</pre>";
			echo $_SERVER['SERVER_ADDR'];	
?>