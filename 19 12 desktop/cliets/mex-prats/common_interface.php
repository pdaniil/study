<?
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);//Предотвратить вывод сообщений об ошибках
//Класс продукта
require_once($_SERVER["DOCUMENT_ROOT"]."/content/shop/docpart/DocpartProduct.php");

class shate_m_enclosure
{
	public $result;
	
	public $Products = array();//Список товаров
	
	public function __construct($article, $storage_options)
	{
		$this->result = 0;//По умолчанию
		
		/*****Учетные данные*****/
        $login = $storage_options["login"];
		$password = $storage_options["password"];
        $api_key = $storage_options["api_key"];
		/*****Учетные данные*****/
        
		// -------------------------------------------------------------------------------------------------
		//Авторизуемся
		
		$api_path = "";
		switch($storage_options["country"])
		{
			case "ru":
				$api_path = "https://api.shate-m.ru/";
				break;
			case "by":
				$api_path = "https://api.shate-m.com/";
				break;
			case "kz":
				$api_path = "http://svkzastsa0003:8989/";
				break;
			default: $api_path = "https://api.shate-m.ru/";
		}
		
		// Сперва на сервере используется HTTP Basic авторизация, выполняем ее: 
		$url = $api_path . 'login/'; 

		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // отключение сертификата
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); // отключение сертификата

		curl_setopt($ch, CURLOPT_URL, $url); 
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.9.0.5) Gecko/2008120122 Firefox/3.0.5');
		curl_setopt($ch, CURLOPT_USERPWD, $login.":".$password); 

		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_NOBODY, 1);

		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "ApiKey=".$api_key);

		$result = curl_exec($ch); 
		//curl_close($ch); 

		// Читаем token
		$token = '';
		$result = explode("\n",$result);
		foreach($result as $v){
			if( strpos($v, 'oken:') > 0 ){
				$token = $v;
				$token = str_replace('Token:', '', $token);
				$token = trim($token);
				break;
			}
		}
		
		// Если токена нет то выходим
		if(empty($token)){
			return;
		}
		
		// Запрашиваем товары по артикулу.
		$url = $api_path . 'api/search/GetTradeMarksByArticleCode/'.$article;
		
		//$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // отключение сертификата
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); // отключение сертификата
		curl_setopt($ch, CURLOPT_HEADER, 0);

		curl_setopt($ch, CURLOPT_URL, $url); 
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.9.0.5) Gecko/2008120122 Firefox/3.0.5');

		curl_setopt($ch, CURLOPT_POST, 0);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array( 
			'header' => "Token:". $token
		)); 

		$result = curl_exec($ch); 
		
		$result = json_decode($result);
		
		if($result->StatusCode != 200)
		{
			return;
		}
		
		$result = $result->TradeMarkByArticleCodeModels;// список производителей у которых есть этот артикулу
		
		$result_arr = array();
		// Делаем цикл по производителям и запрашиваем товары
		foreach($result as $mark){
			
			// Запрашиваем товары по артикулу.
			$url = $api_path . 'api/search/GetPricesByArticle/?articleCode='. $article .'&tradeMarkName='. str_replace(' ', '+', $mark->TradeMarkName) .'&tradeMarkId='. $mark->TradeMarkId .'&includeAnalogs=true';
			//$url = $api_path . '/api/search/GetPricesByArticle/?articleCode='. $article;
			
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // отключение сертификата
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); // отключение сертификата
			curl_setopt($ch, CURLOPT_HEADER, 0);

			curl_setopt($ch, CURLOPT_URL, $url); 
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.9.0.5) Gecko/2008120122 Firefox/3.0.5');

			curl_setopt($ch, CURLOPT_POST, 0);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array( 
				'header' => "Token:". $token
			)); 

			$result_arr = (array)json_decode(curl_exec($ch), true); 
			
			if(!empty($result_arr['PriceModels'])){
				//var_dump($result_arr['PriceModels']);
				foreach($result_arr['PriceModels'] as $item){
					
					$ArticleCode 	= $item['ArticleCode'];// Артикул
					$MarkName 		= $item['TradeMarkName'];// производитель
					$name 			= $item['Description'];// Описание детали
					
					if(!empty($item['ArticlePriceInfo'])){
						foreach($item['ArticlePriceInfo'] as $item_price){
							
							
							$price = (float)$item_price["Price"];
							
							//Наценка
							$markup = $storage_options["markups"][(int)$price];
							if($markup == NULL)//Если цена выше, чем максимальная точка диапазона - наценка определяется последним элементов в массиве
							{
								$markup = $storage_options["markups"][count($storage_options["markups"])-1];
							}
							
							//Набор параметров для SAO
							$json_params = array("OfferKey"=>$item_price["OfferKey"], "ArticleId"=>$item['ArticleId'], "Multiplicity"=>$item_price["Multiplicity"]);
							
							
							//Создаем объек товара и добавляем его в список:
							$DocpartProduct = new DocpartProduct($MarkName,
								$ArticleCode,
								$name,
								$item_price["Qty"],
								$price + $price*$markup,
								$item_price["DeliveryTerm"] + $storage_options["additional_time"],
								$item_price["DeliveryTerm"] + $storage_options["additional_time"],
								NULL,
								$item_price["Multiplicity"],
								$storage_options["probability"],
								$storage_options["office_id"],
								$storage_options["storage_id"],
								$storage_options["office_caption"],
								$storage_options["color"],
								$storage_options["storage_caption"],
								$price,
								$markup,
								2,0,0,'',json_encode($json_params),array("rate"=>$storage_options["rate"])
								);
							
							if($DocpartProduct->valid == true)
							{
								array_push($this->Products, $DocpartProduct);
							}
							
							
						}
					}
					
				}
			}
			
			//break;
		}
		
		curl_close($ch); 
		
		//echo '<pre>';
		//var_dump($result);
		//exit;
		
		
        $this->result = 1;
	}//~function __construct($article)
};//~class shate_m_enclosure



$ob = new shate_m_enclosure($_POST["article"], json_decode($_POST["storage_options"], true));

//$ob = new shate_m_enclosure("OC247", array("login"=>"DERGACHEV77", "password"=>"123456789", "api_key"=>"A49F72AF-E75A-4C2A-BF77-8EBF7006F2D2", "country"=>"ru") );

exit(json_encode($ob));
?>