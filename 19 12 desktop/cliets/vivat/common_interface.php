<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);//Предотвратить вывод сообщений об ошибках
//Класс продукта
require_once($_SERVER["DOCUMENT_ROOT"]."/content/shop/docpart/DocpartProduct.php");

//ЛОГ - ПОДКЛЮЧЕНИЕ КЛАССА
require_once($_SERVER["DOCUMENT_ROOT"]."/content/shop/docpart/DocpartSuppliersAPI_Debug.php");

$DP_Config = new DP_Config();
try
{
	$db_link = new PDO('mysql:host='.$DP_Config->host.';dbname='.$DP_Config->db, $DP_Config->user, $DP_Config->password);
}
catch (PDOException $e) 
{
	exit("No DB connect");
}
$db_link->query("SET NAMES utf8;");




function resetToken($login, $password, $storage_id)
{
	global $db_link;
	$OPTIONS = "SELECT `connection_options` FROM `shop_storages` WHERE `id` = ?";
	$result_options_query = $db_link->prepare($OPTIONS);
	$result_options_query->execute( array(  $storage_id) );
	$mysql_fetch = $result_options_query->fetch();

	if( ! $mysql_fetch )
		return;

	$connection_options = json_decode($mysql_fetch["connection_options"], true);
	
	
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "http://srv.vivat-uae.net/gettoken?login=".$login."&pwd=".$password);
	curl_setopt($ch, CURLOPT_FAILONERROR,1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);$xmlstr = curl_exec($ch); 
	curl_close($ch);
	$XMLobj = new SimpleXMLElement($xmlstr);
	$token = strval($XMLobj->status->id);
	$connection_options["token"] = $token;		
	$connection_options_json = json_encode($connection_options);
	$OPTION_UPDATE = "
	UPDATE `shop_storages` 
	SET `connection_options` = ? 
	WHERE `id` = ?
	";
	$db_link->prepare($OPTION_UPDATE)->execute( array($connection_options_json, $storage_id) );
	
	
	return $token;	
}

class vivat_enclosure
{
	public $result;
	
	public $Products = array();
	
	public function __construct($article, $storage_options)
	{
		
		//ЛОГ - СОЗДАНИЕ ОБЪЕКТА
		$DocpartSuppliersAPI_Debug = DocpartSuppliersAPI_Debug::getInstance();
		
		$this->status = 0;//По умолчанию
		
		/*****Учетные данные*****/
		$login = $storage_options["login"];
		$password = $storage_options["password"];
		$token = $storage_options["token"];
		/*****Учетные данные*****/

		// -------------------------------------------------------------------------------------------------
		//Если нет токена, то получаем его
		//Получаем список сбытовых организаций клиента
		
		if( $token == "" ||  $token == 0)
		{
			//ЛОГ - [СООБЩЕНИЕ] (простое сообщение в лог)
			if($DocpartSuppliersAPI_Debug->suppliers_api_debug)
			{
				$DocpartSuppliersAPI_Debug->log_simple_message("Токена нет. Получаем токен через API.");
			}
			
			//-----------------------------Сохраняем токен------------------------------------//
			
			$token = resetToken($login, $password, $storage_options["storage_id"]);
			
			if(empty($token)) 
			{
				if($DocpartSuppliersAPI_Debug->suppliers_api_debug)
				{
					$DocpartSuppliersAPI_Debug->log_api_request("API-запрос на получение токена", "http://srv.vivat-uae.net/gettoken, логин: $login, пароль: $password", 'Token','' );
				}
			} 
			//----------------------------------------------------------------------------------//
			
		}
		else
		{
			//ЛОГ - [СООБЩЕНИЕ] (простое сообщение в лог)
			if($DocpartSuppliersAPI_Debug->suppliers_api_debug)
			{
				$DocpartSuppliersAPI_Debug->log_simple_message("Токен есть.");
			}
			$plevelid = $storage_options["plevelid"];
			$routeid = $storage_options["routeid"];

			$url = "http://srv.vivat-uae.net/getprices?token=".$token."&partno=".$article."&brand=&plevelid=".$plevelid."&ptypeid=".$storage_options["ptypeid"]."&routeid=".$routeid."&tarifid=".$storage_options["tarifid"]."&depotid=".$storage_options["depotid"]."&currid=".$storage_options["currencyid"]."&withsubsts=1";
			$ch = curl_init(); 
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_FAILONERROR,1);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 30);

			$xmlstr = curl_exec($ch); 

			
			$XMLobj = new SimpleXMLElement($xmlstr);	

			
			

			foreach ($XMLobj->row as $product) {
				$price = $product->price;
				$markup = $storage_options["markups"][(int)$price];
				if($markup == NULL)//Если цена выше, чем максимальная точка диапазона - наценка определяется последним элементов в массиве
				{
					$markup = $storage_options["markups"][count($storage_options["markups"])-1];
				}	

				echo "<pre>";
				print_r($product);
				echo "</pre>";
				$manufacturer = $product->brand;
				$article_res = $product->partno;
				$name = $product->descrrus;
				$exist = $product->qty;
				$price = $product->price;
				$timeToExe = $product->depotid[stripos($product->depotid,'-')-1];
				$timeToExe_min = $product->depotid[stripos($product->depotid,'-')+1];
				$storage = "";

				$sao_code = "";
				$b_id = "";


				$markup = $storage_options["markups"][(int)$price];
				if($markup == NULL)//Если цена выше, чем максимальная точка диапазона - наценка определяется последним элементов в массиве
				{
					$markup = $storage_options["markups"][count($storage_options["markups"])-1];
				}


							//Создаем объек товара и добавляем его в список:
				$DocpartProduct = new DocpartProduct($manufacturer,
					$article_res,
					$name,
					$exist,
					$price + $price*$markup,
					$timeToExe_min + $storage_options["additional_time"],
					$timeToExe + $storage_options["additional_time"],
					$storage,
					1,
					$storage_options["probability"],
					$storage_options["office_id"],
					$storage_options["storage_id"],
					$storage_options["office_caption"],
					$storage_options["color"],
					$storage_options["storage_caption"],
					$price,
					$markup,
					2,0,0,'',json_encode(array("code"=>$sao_code, "b_id"=>$b_id)),array("rate"=>$storage_options["rate"])
				);

							//var_dump($DocpartProduct);
				if($DocpartProduct->valid == true)
				{
					array_push($this->Products, $DocpartProduct);
				}




			}



		}




		// -------------------------------------------------------------------------------------------------

		//ЛОГ - [СООБЩЕНИЕ] (простое сообщение в лог)
		//ЛОГ [API-запрос] (вся информация о запросе)
		if($DocpartSuppliersAPI_Debug->suppliers_api_debug)
		{
			$DocpartSuppliersAPI_Debug->log_api_request("Получение списка товаров ".$article, $url, $XMLobj, '' );
		}

		if(curl_errno($ch))
		{
			//ЛОГ - [СООБЩЕНИЕ С ОШИБКОЙ]
			if($DocpartSuppliersAPI_Debug->suppliers_api_debug)
			{
				$DocpartSuppliersAPI_Debug->log_error("Есть ошибка", curl_error($ch) );
			}
		}





			//ЛОГ [РЕЗУЛЬТИРУЮЩИЙ ОБЪЕКТ - БРЭНДЫ]
		if($DocpartSuppliersAPI_Debug->suppliers_api_debug)
		{
			$DocpartSuppliersAPI_Debug->log_supplier_handler_result("Список товаров", print_r($this->Products, true) );
		}

		$this->status = 1;

	}
};//~class vivat_enclosure
/*
$f = fopen('log.txt','w');
fwrite($f, $_POST["storage_options"]."\n");
fwrite($f, $_POST["article"]."\n");
fclose($f);
*/
$_POST["storage_options"] = '{"login":"avtomagickr","password":"1h6a2d2c","token":"9fd930d2-3c6b-4f52-946a-b8b949c7693d","color":"#000000","probability":"95","ref":"MGK","plevelid":"3418","routeid":"28","tarifid":"80","ptypeid":"1","currencyid":"1","depotid":"-1","markups":[0],"office_id":"1","storage_id":"51","additional_time":"0","office_caption":"Avtomagic","storage_caption":"","rate":"1","group_id":"2"}';
$_POST["article"] = '3591214F11';
//Настройки подключения к складу
$storage_options = json_decode($_POST["storage_options"], true);
//ЛОГ - СОЗДАНИЕ ОБЪЕКТА
$DocpartSuppliersAPI_Debug = DocpartSuppliersAPI_Debug::getInstance();
//ЛОГ - ИНИЦИАЛИЗАЦИЯ ПАРАМЕТРОВ ОБЪЕКТА
$DocpartSuppliersAPI_Debug->init_object( array("storage_id"=>$storage_options["storage_id"], "api_script_name"=>__FILE__, "api_type"=>"CURL-HTTP-JSON") );
//ЛОГ - СОЗДАНИЕ ФАЙЛА ЛОГА
$DocpartSuppliersAPI_Debug->start_log();



$ob = new vivat_enclosure($_POST["article"], $storage_options);
//exit(json_encode($ob));
?>