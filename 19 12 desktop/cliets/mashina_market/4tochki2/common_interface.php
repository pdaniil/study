<?php
// DOC : https://b2b.pwrs.ru/Help/Page?url=index.html
require_once( $_SERVER["DOCUMENT_ROOT"]."/content/shop/docpart/DocpartProduct.php" );
include( 'TochkiApi.php' );




class tochki_enclosure
{
	public $result = 0; 
	public $Products = array();
	
	public function __construct($article, $manufacturers, $storage_options)
	{
		
		
		
		try 
		{
			
			$api = new TochkiApi();
			
			$api_options = array( 'wsdl' => 'http://api-b2b.pwrs.ru/WCF/ClientService.svc?wsdl',
									'login' => trim( $storage_options['login'] ),
									'password' => trim( $storage_options['password'] ) );
			
			$retail_price = $storage_options['retail_price']; //Розничная цена в проценке
			
			
			
			
			$api->setOptions( $api_options );
			
			//Получаем товары, наличие на складах и цены
			$suppliers_items = $api->getSupplierItems( $article );
			
			
			
			
			
			
			if ( empty( $suppliers_items ) ) {
				
				$this->result = 1;
				return;
			}
			
			
			
			
			
			//Получаем инфу о скаладах( сроки логистики и прочая инфа )
			$warehouse_info = $api->getWarehouseInfo();
			
			
			
			
			
			
			
			
			
			
			// /* 
			foreach ( $suppliers_items as $item ) 
			{
				
				$article 				= $item['article'];
				$manufacturer 			= $item['brand'];
				$name 					= $item['name'];
				
				//Наличие на складах
				foreach ( $item['storages'] as $storage ) 
				{
					
					if ( $retail_price ) {
						
						$price_purchase		= $storage['price_rozn']; //Рекомендуемая цена из API
						
					} else {
						
						$price_purchase		= $storage['price']; //Цена поставщика( видимо )
												
					}
					
					// $price_purchase		= $storage['price_rozn']; //Возможно нужно будет использовать этот параметр
					$exist 				= $storage['rest'];
					
					$time_to_exe				= $warehouse_info[$storage['wrh']]['logisticDays'] + $storage_options['additional_time'];
					$time_to_exe_guaranteed	= $warehouse_info[$storage['wrh']]['logisticDays'] + $storage_options['additional_time'];
					
					$storage 					= 0;
					$min_order					= 1;
					$probability				= $storage_options['probability'];
					$product_type				= 2;
					$product_id				= 0;
					$storage_record_id		= 0;
					$url						= '';
					$json_params				= '';
					$rest_params				= array( "rate"=>$storage_options["rate"] );
					
					//Наценка
					$markup = $storage_options["markups"][(int)$price_purchase];
					//Если цена выше, чем максимальная точка диапазона - наценка определяется последним элементов в массиве
					if( $markup == NULL ) {
						
						$markup = $storage_options["markups"][count($storage_options["markups"])-1];
						
					}
					
					$price_for_customer = $price_purchase + $price_purchase * $markup;
					
					$DocpartProduct = new DocpartProduct( $manufacturer, 
															$article,
															$name,
															$exist,
															$price_for_customer,
															$time_to_exe,
															$time_to_exe_guaranteed,
															$storage,
															$min_order,
															$probability,
															$storage_options["office_id"],
															$storage_options["storage_id"],
															$storage_options["office_caption"],
															$storage_options["color"],
															$storage_options["storage_caption"],
															$price_purchase,
															$markup,
															$product_type,
															$product_id,
															$storage_record_id,
															$url,
															$json_params,
															$rest_params );
					
					if($DocpartProduct->valid) {
						
						array_push($this->Products, $DocpartProduct);
						
					}
					
					
				}
				
				
			} // ~foreach ( $suppliers_items as $item )
			
			$this->result = 1;
			
			
			
		}
		catch ( SoapFault $e )
		{
			
		}
		catch ( Exception $e )
		{
			
		}
		
	} //~__construct($article, $manufacturers, $storage_options)
	
}

$f = fopen('log_sup.txt','w');
fwrite($f, $_POST["storage_options"]."\n");
fwrite($f, $_POST["article"]."\n");
fwrite($f, $_POST["manufacturers"]."\n");
fclose($f);

//$_POST["storage_options"] = '';
//$_POST["article"] = '';
//$_POST["manufacturers"] = '';

//Настройки подключения к складу
$storage_options = json_decode($_POST["storage_options"], true);




$ob =  new tochki_enclosure( $_POST["article"], json_decode($_POST["manufacturers"], true), $storage_options );
$json = json_encode($ob);



exit($json);
?>