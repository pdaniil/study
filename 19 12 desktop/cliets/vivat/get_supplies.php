 <?php

header('Content-Type: text/html; charset=utf-8');

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);//Предотвратить вывод сообщений об ошибках

require_once($_SERVER["DOCUMENT_ROOT"]."/content/shop/docpart/DocpartProduct.php");

//ЛОГ - ПОДКЛЮЧЕНИЕ КЛАССА
require_once($_SERVER["DOCUMENT_ROOT"]."/content/shop/docpart/DocpartSuppliersAPI_Debug.php");

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


class adavanta_enclosure
{
	public $result = 0; 
	public $Products = array();
	
	public function __construct($article, $manufacturers, $storage_options)
	{
		//ЛОГ - СОЗДАНИЕ ОБЪЕКТА
		$DocpartSuppliersAPI_Debug = DocpartSuppliersAPI_Debug::getInstance();
		
		$this->result = 0;//По умолчанию
		
		/*****Учетные данные*****/
		$login = $storage_options["login"];
		$password = $storage_options["password"];
		$token = $storage_options["token"];
		$plevelid = $storage_options["plevelid"];
		$ptypeid = $storage_options["ptypeid"];
		$routeid = $storage_options["routeid"];
		$tarifid = $storage_options["tarifid"];
		$depotid = $storage_options["depotid"];
		$currencyid = $storage_options["currencyid"];
		
		$url = 'http://srv.vivat-uae.net/getprices?token='.$token.'&partno='.$article.'&brand='.$manufacturers->manufacturer.'&plevelid='.$plevelid.'&ptypeid='.$ptypeid.'&routeid='.$routeid.'&tarifid='.$tarifid.'&depotid='.$depotid.'&currid='.$currencyid.'&withsubsts=1';
		/*****Учетные данные*****/
		
		
      
      	
		if( $token == "" || NULL || $token === 0)
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
			
			
			$ch = curl_init(); 
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_FAILONERROR,1);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		
			$xmlstr = curl_exec($ch); 
		
		
			$XMLobj = new SimpleXMLElement($xmlstr);	
			echo "<pre>";
			print_r($XMLobj);
			echo "</pre>";
			exit();
			
		}
      
      
      
		

			

			//Получаем список сбытовых организаций клиента
			
          
			
			// -------------------------------------------------------------------------------------------------

			//ЛОГ - [СООБЩЕНИЕ] (простое сообщение в лог)
			//ЛОГ [API-запрос] (вся информация о запросе)
			if($DocpartSuppliersAPI_Debug->suppliers_api_debug)
			{
				$DocpartSuppliersAPI_Debug->log_api_request("Получение остатков по артикулу ".$article, "https://adavanta.ru/api/v1/estimate/?number={$article}&brand=brand&cross=1", 'Token', '' );
			}
			
			if(curl_errno($ch))
			{
				//ЛОГ - [СООБЩЕНИЕ С ОШИБКОЙ]
				if($DocpartSuppliersAPI_Debug->suppliers_api_debug)
				{
					$DocpartSuppliersAPI_Debug->log_error("Есть ошибка", curl_error($ch) );
				}
			}

			curl_close($ch);
          
			$products = json_decode($curl_result);
          
	
            if (isset($products) && !empty($products)) {

                //--------------По данным ответа---------------//
                foreach ($products as $product) {

                    $price = (int)$product->price;

                    //Наценка
                    $markup = $storage_options["markups"][(int)$price];
                    if($markup == NULL)//Если цена выше, чем максимальная точка диапазона - наценка определяется последним элементов в массиве
                    {
                        $markup = $storage_options["markups"][count($storage_options["markups"])-1];
                    }

                    $min_order = $product->min_order;
                    if(empty($min_order)) {
                        $min_order = 1;
                    }

                    $delivery_time = $product->delivery_days;
                    $delivery_time_guaranteed = $product->delivery_days;


                    if(empty($delivery_time)) {
                        $delivery_time = 0;
                        $delivery_time_guaranteed = 0;
                    }



                    // //Создаем объек товара и добавляем его в список:
                    $DocpartProduct = new DocpartProduct((string)$product->brand,
                        (string)$product->partnumber,
                        (string)$product->description,
                        (int)$product->remain,
                        $price + $price*$markup,
                        $delivery_time + $storage_options["additional_time"],
                        $delivery_time_guaranteed + $storage_options["additional_time"],
                        NULL,
                        $min_order,
                        $storage_options["probability"],
                        $storage_options["office_id"],
                        $storage_options["storage_id"],
                        $storage_options["office_caption"],
                        $storage_options["color"],
                        $storage_options["storage_caption"],
                        $price,
                        $markup,
                        2,0,0,'',null,array("rate"=>$storage_options["rate"], "id_for_order" => $product->id_for_order)
                        );

                    if($DocpartProduct->valid == true)
                    {
                        array_push($this->Products, $DocpartProduct);
                    }

                }

			}

        


		// -------------------------------------------------------------------------------------------------


			//ЛОГ [РЕЗУЛЬТИРУЮЩИЙ ОБЪЕКТ - ОСТАТКИ]
		if($DocpartSuppliersAPI_Debug->suppliers_api_debug)
		{
			$DocpartSuppliersAPI_Debug->log_supplier_handler_result("Список остатков", print_r($this->Products, true) );
		}
		
		$this->result = 1;
			
	
	}
}//~class armtek_enclosure
/*
$f = fopen('log_sup.txt','w');
fwrite($f, $_POST["storage_options"]."\n");
fwrite($f, $_POST["article"]."\n");
fwrite($f, $_POST["manufacturers"]."\n");
fclose($f);
*/
$_POST["storage_options"] = '{"login":"avtomagickr","password":"1h6a2d2c","token":"bc3dcb9d-431a-4d7f-b175-d1f30996aee5","color":"#000000","probability":"95","ref":"MGK","plevelid":"3418","routeid":"28","tarifid":"71","ptypeid":"1","currencyid":"1","depotid":"-1","markups":[0],"office_id":"1","storage_id":"51","additional_time":"0","office_caption":"Avtomagic","storage_caption":"","rate":"1","group_id":"2"}';
$_POST["article"] = '144MY25';
$_POST["manufacturers"] = '[{"manufacturer":"CHEVROLET","manufacturer_id":0,"manufacturer_show":"CHEVROLET","name":"CHEVROLET","storage_id":"51","office_id":"1","synonyms_single_query":true,"params":[],"valid":true}]';
//Настройки подключения к складу
$storage_options = json_decode($_POST["storage_options"], true);
//ЛОГ - СОЗДАНИЕ ОБЪЕКТА
$DocpartSuppliersAPI_Debug = DocpartSuppliersAPI_Debug::getInstance();
//ЛОГ - ИНИЦИАЛИЗАЦИЯ ПАРАМЕТРОВ ОБЪЕКТА
$DocpartSuppliersAPI_Debug->init_object( array("storage_id"=>$storage_options["storage_id"], "api_script_name"=>__FILE__, "api_type"=>"CURL-HTTP-JSON") );


$ob = new adavanta_enclosure($_POST["article"], json_decode($_POST["manufacturers"], true), $storage_options);

exit(json_encode($ob));
?>