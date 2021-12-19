<?php
/**
 * Скрипт для обработки данных в личном кабинете Росско
*/

header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);//Предотвратить вывод сообщений об ошибках



$result = array(
    'status'  => true,
    'message' => '',
    'html'    => '',
    'data'    => array()
);

//Получаем данные
$request_object = json_decode($_POST['request_object'], true);

switch($request_object['action']) {
    case 'get_data':
        
        try
        {
            if(!(isset($request_object["token"]) && !empty($request_object["token"]))) {
                throw new Exception("Отсутствует token для подключения к Vivat.");
            }
            
            
            /*****Учетные данные*****/
            $token = $request_object["token"];
			$login = $request_object["login"];
			$password = $request_object["password"];
            /*****Учетные данные*****/ 
            
            //Получение необходимых параметров
            
			//Получения списка доступных РЕФ.
        	$ch = curl_init(); 
			curl_setopt($ch, CURLOPT_URL, "http://srv.vivat-uae.net/getreflist?token=".$token);
			curl_setopt($ch, CURLOPT_FAILONERROR,1);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	

			

			$xmlstr = curl_exec($ch); 
			curl_close($ch);
			$XMLobj = new SimpleXMLElement($xmlstr);	
			if ($XMLobj->status->id != 0)
			{
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, "http://srv.vivat-uae.net/gettoken?login=".$login."&pwd=".$password);
				curl_setopt($ch, CURLOPT_FAILONERROR,1);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
				curl_setopt($ch, CURLOPT_TIMEOUT, 30);
				$xmlstr = curl_exec($ch); 
				curl_close($ch);
				$XMLobj = new SimpleXMLElement($xmlstr);
				
				$token = $XMLobj->data->row->token;
				
				$ch = curl_init(); 
				curl_setopt($ch, CURLOPT_URL, "http://srv.vivat-uae.net/getreflist?token=".$token);
				curl_setopt($ch, CURLOPT_FAILONERROR,1);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
				curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	

			

				$xmlstr = curl_exec($ch); 
				curl_close($ch);
				$XMLobj = new SimpleXMLElement($xmlstr);	
			}	
			
			$result["data"] = $XMLobj;
			//////////////////////////////////
			
        }
        catch (Exception $e)
        {
            $result['status'] = false;
        	$result['message'] = "Ошибка: " . $e->getMessage();
        }
        
        //Формируем ответ для вывода
        if($result['status']) {
            
            $connection_options = isset($request_object["connection_options"]) ? $request_object["connection_options"] : array();
            
            $result['html'] = "<div class=\"hpanel\">
        		<div class=\"panel-heading hbuilt\">
        			Настройки Vivat Личный кабинет
        		</div>
        		<div class=\"panel-body\">";

            if(!empty($result['data'])) {
                
				$XMLobj = $result["data"];
               
				
				//	  [ref]: http://srv.vivat-uae.net/getreflist?token=[token] – список доступных РЕФ
			   
                if(!empty($XMLobj->data->row)) {
                    $result['html'] .= "<div class=\"form-group\">
                                <label class=\"col-lg-6 control-label\">Cписок доступных РЕФ</label>
                		        <div class=\"col-lg-6\">
                		            <select class=\"form-control\" id=\"ref\">";
									 $result['html'] .= "<option value=\"\"  title=\"REF\">Не выбран</option>";
                		            foreach($XMLobj->data->row as $obj) {
                    		              $is_selected = false;
                    		              if(!empty($connection_options) && isset($connection_options['ref']) && $connection_options['ref'] == $obj->title) $is_selected = true;
                    		              $selected = $is_selected ? 'selected' : '' ;
                    		              $result['html'] .= "<option value=\"" . $obj->title . "\" " . $selected . " title=\"REF\">".$obj->title."</option>";
                    		        }
                    		        
                   $result['html'] .= "</select>
            		        </div>
        		        </div>
        		        <div class=\"hr-line-dashed col-lg-12\"></div>"; 
                }
				
                
				//    [plevelid]: http://srv.vivat-uae.net/getplevellist?token=[token]&ref=[ref] - список доступных типов цен
				if (!isset($connection_options['ref']))
				{
					 $result['html'] .= "<div class=\"form-group\">
                                <label class=\"col-lg-6 control-label\">Cписок доступных типов цен</label>
                		        <div class=\"col-lg-6\" id=\"plevelid\">";
					$result['html'] .= "Не выбран РЕФ.
            		        </div>
        		        </div>
        		        <div class=\"hr-line-dashed col-lg-12\"></div>"; 
				}
				else
				{
					 $result['html'] .= "<div class=\"form-group\">
                                <label class=\"col-lg-6 control-label\">Cписок доступных типов цен</label>
                		        <div class=\"col-lg-6\">
                		            <select class=\"form-control\" id=\"plevelid\">";
									 $result['html'] .= "<option value=\"\"  title=\"EXP\">Не выбран</option>";
									 
									 
									$ch = curl_init(); 
									curl_setopt($ch, CURLOPT_URL, "http://srv.vivat-uae.net/getplevellist?token=".$token."&ref=".$connection_options['ref']);
									curl_setopt($ch, CURLOPT_FAILONERROR,1);
									curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
									curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
									curl_setopt($ch, CURLOPT_TIMEOUT, 30);
									 
									$xmlstr = curl_exec($ch); 
									curl_close($ch);
									$XMLobj = new SimpleXMLElement($xmlstr);	
									  
									 foreach ($XMLobj->data->row as $obj)
									 {
										$is_selected = false;
										if(!empty($connection_options) && isset($connection_options['plevelid']) && $connection_options['plevelid'] == $obj->plevelid) $is_selected = true;
										$selected = $is_selected ? 'selected' : '' ;
										$result['html'] .= "<option value=\"" . $obj->plevelid . "\" " . $selected . " title=\"E\">".$obj->title."</option>";
									 }
									 
									 
									 
                    		        
                   $result['html'] .= "</select>
            		        </div>
        		        </div>
        		        <div class=\"hr-line-dashed col-lg-12\"></div>"; 
				}
				
               
			   
			    //[routeid]: http://srv.vivat-uae.net/getroutelist?token=[token]&plevelid=[plevelid] – список маршрутов.
			   
                if (empty($connection_options["plevelid"]) || empty($connection_options['ref']))
				{
					$result['html'] .= "<div class=\"form-group\">
                                <label class=\"col-lg-6 control-label\">Cписок маршрутов</label>
                		        <div class=\"col-lg-6\" id=\"routeid\">";
					$result['html'] .= "Не выбран тип цены.
            		        </div>
        		        </div>
        		        <div class=\"hr-line-dashed col-lg-12\"></div>"; 
				}
				else
				{
					 $result['html'] .= "<div class=\"form-group\">
                                <label class=\"col-lg-6 control-label\">Cписок маршрутов</label>
                		        <div class=\"col-lg-6\">
                		            <select class=\"form-control\" id=\"routeid\">";
									 $result['html'] .= "<option value=\"\"  title=\"routeid\">Не выбран</option>";
					$ch = curl_init(); 
					curl_setopt($ch, CURLOPT_URL, "http://srv.vivat-uae.net/getroutelist?token=".$token."&plevelid=".$connection_options["plevelid"]);
					curl_setopt($ch, CURLOPT_FAILONERROR,1);
					curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
					curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		
					$xmlstr = curl_exec($ch); 
					curl_close($ch);
	
					$XMLobj = new SimpleXMLElement($xmlstr);	
					
				
				
					foreach ($XMLobj->data->row as $obj)
					{	
							$is_selected = false;
							if(!empty($connection_options) && isset($connection_options['routeid']) && $connection_options['routeid'] == $obj->routeid) $is_selected = true;
							$selected = $is_selected ? 'selected' : '' ;
							$result['html'] .= "<option value=\"" . $obj->routeid . "\" " . $selected . " title=\"E\">".$obj->title."</option>";
							$i += 2;
					}
					$result['html'] .= "</select>
            		        </div>
        		        </div>
        		        <div class=\"hr-line-dashed col-lg-12\"></div>"; 
				}
				
				
				
				//Список тарифов
				if (!isset($connection_options['routeid']) || !isset($connection_options["plevelid"]) || !isset($connection_options['ref']))
				{
					$result['html'] .= "<div class=\"form-group\">
                                <label class=\"col-lg-6 control-label\">Список тарифов</label>
                		        <div class=\"col-lg-6\" id=\"tarifid\">";
					$result['html'] .= "Не выбран маршрут.
            		        </div>
        		        </div>
        		        <div class=\"hr-line-dashed col-lg-12\"></div>"; 
				}
				else
				{	
					$result['html'] .= "<div class=\"form-group\">
                                <label class=\"col-lg-6 control-label\">Список тарифов</label>
                		        <div class=\"col-lg-6\">
                		            <select class=\"form-control\" id=\"tarifid\">";
					$result['html'] .= "<option value=\"\"  title=\"tarifid\">Не выбран</option>";
					
					$ch = curl_init(); 
					curl_setopt($ch, CURLOPT_URL, "http://srv.vivat-uae.net/gettariflist?token=".$token."&plevelid=".$connection_options["plevelid"]."&routeid=".$connection_options['routeid']);
					curl_setopt($ch, CURLOPT_FAILONERROR,1);
					curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
					curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		
					$xmlstr = curl_exec($ch); 
					curl_close($ch);
					
					try {
						$XMLobj = new SimpleXMLElement($xmlstr);
					}
					catch (Exception $e)
					{
						
					}
					
					foreach ($XMLobj->data->row as $elem)
					{
						$is_selected = false;
						if(!empty($connection_options) && isset($connection_options['tarifid']) && $connection_options['tarifid'] == $elem->tarifid) $is_selected = true;
						$selected = $is_selected ? 'selected' : '' ;
						$result['html'] .= "<option value=\"" . $elem->tarifid . "\" " . $selected . " title=\"E\">".$elem->title."</option>";
					}
				
					$result['html'] .= "</select>
            		        </div>
        		        </div>
        		        <div class=\"hr-line-dashed col-lg-12\"></div>"; 
					
				}
				
				//[ptypeid]: http://srv.vivat-uae.net/getpaytypelist?token=[token]&plevelid=[plevelid]&routeid=[routeid] – способы оплаты.
				if (empty($connection_options['tarifid']) || empty($connection_options["routeid"]) || empty($connection_options["plevelid"]) || empty($connection_options['ref']))
				{
					$result['html'] .= "<div class=\"form-group\">
                                <label class=\"col-lg-6 control-label\">Cпособы оплаты</label>
                		        <div class=\"col-lg-6\" id=\"ptypeid\">";
					$result['html'] .= "Не выбран тариф.
            		        </div>
        		        </div>
        		        <div class=\"hr-line-dashed col-lg-12\"></div>"; 
				}
				else
				{
					 $result['html'] .= "<div class=\"form-group\">
                                <label class=\"col-lg-6 control-label\">Cпособы оплаты</label>
                		        <div class=\"col-lg-6\">
                		            <select class=\"form-control\" id=\"ptypeid\">";
									 $result['html'] .= "<option value=\"\"  title=\"routeid\">Не выбран</option>";
					$ch = curl_init(); 
					curl_setopt($ch, CURLOPT_URL, "http://srv.vivat-uae.net/getpaytypelist?token=".$token."&plevelid=".$connection_options["plevelid"]."&routeid=".$connection_options["routeid"]);
					curl_setopt($ch, CURLOPT_FAILONERROR,1);
					curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
					curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		
					$xmlstr = curl_exec($ch); 
					curl_close($ch);
	
					$XMLobj = new SimpleXMLElement($xmlstr);	
					
				
				
					foreach ($XMLobj->data->row as $obj)
					{	
							$is_selected = false;
							if(!empty($connection_options) && isset($connection_options['ptypeid']) && $connection_options['ptypeid'] == $obj->ptypeid) $is_selected = true;
							$selected = $is_selected ? 'selected' : '' ;
							$result['html'] .= "<option value=\"" . $obj->ptypeid . "\" " . $selected . " title=\"E\">".$obj->title."</option>";
							
					}
					$result['html'] .= "</select>
            		        </div>
        		        </div>
        		        <div class=\"hr-line-dashed col-lg-12\"></div>"; 
				}
				/////////////
				
				//[currencyid]: http://srv.vivat-uae.net/getcurrencylist?token=[token]&plevelid=[plevelid]&routeid=[routeid]&ptypeid=[ptypeid] список валют
				if (empty($connection_options['ptypeid']) || empty($connection_options["routeid"]) || empty($connection_options["plevelid"]) || empty($connection_options['ref']))
				{
					$result['html'] .= "<div class=\"form-group\">
                                <label class=\"col-lg-6 control-label\">Список валют</label>
                		        <div class=\"col-lg-6\" id=\"currencyid\">";
					$result['html'] .= "Не выбран способы оплаты.
            		        </div>
        		        </div>
        		        <div class=\"hr-line-dashed col-lg-12\"></div>"; 
				}
				else
				{
					 $result['html'] .= "<div class=\"form-group\">
                                <label class=\"col-lg-6 control-label\">Список валют</label>
                		        <div class=\"col-lg-6\">
                		            <select class=\"form-control\" id=\"currencyid\">";
									 $result['html'] .= "<option value=\"\"  title=\"routeid\">Не выбран</option>";
					$ch = curl_init(); 
					curl_setopt($ch, CURLOPT_URL, "http://srv.vivat-uae.net/getcurrencylist?token=".$token."&plevelid=".$connection_options["plevelid"]."&routeid=".$connection_options["routeid"]."&ptypeid=".$connection_options["ptypeid"]);
					curl_setopt($ch, CURLOPT_FAILONERROR,1);
					curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
					curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		
					$xmlstr = curl_exec($ch); 
					curl_close($ch);
	
					$XMLobj = new SimpleXMLElement($xmlstr);	
					
				
				
					foreach ($XMLobj->data->row as $obj)
					{	
							$is_selected = false;
							if(!empty($connection_options) && isset($connection_options['currencyid']) && $connection_options['currencyid'] == $obj->currencyid) $is_selected = true;
							$selected = $is_selected ? 'selected' : '' ;
							$result['html'] .= "<option value=\"" . $obj->currencyid . "\" " . $selected . " title=\"E\">".$obj->title."</option>";
							
					}
					$result['html'] .= "</select>
            		        </div>
        		        </div>
        		        <div class=\"hr-line-dashed col-lg-12\"></div>"; 
				}
				
				
				
				
				/////////////////////
				
				if (empty($connection_options['currencyid']) || empty($connection_options["routeid"]) || empty($connection_options["plevelid"]) || empty($connection_options['ref']))
				{
					$result['html'] .= "<div class=\"form-group\">
                                <label class=\"col-lg-6 control-label\">Список складов</label>
                		        <div class=\"col-lg-6\" id=\"depotid\">";
					$result['html'] .= "Не выбрана валюта.
            		        </div>
        		        </div>
        		        <div class=\"hr-line-dashed col-lg-12\"></div>"; 
				}
				else
				{
					 $result['html'] .= "<div class=\"form-group\">
                                <label class=\"col-lg-6 control-label\">Список складов</label>
                		        <div class=\"col-lg-6\">
                		            <select class=\"form-control\" id=\"depotid\">";
									 $result['html'] .= "<option value=\"\"  title=\"depotid\">Не выбран</option>";
					$ch = curl_init(); 
					curl_setopt($ch, CURLOPT_URL, "http://srv.vivat-uae.net/getdepotlist?token=".$token."&plevelid=".$connection_options["plevelid"]."&routeid=".$connection_options["routeid"]);
					curl_setopt($ch, CURLOPT_FAILONERROR,1);
					curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
					curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		
					$xmlstr = curl_exec($ch); 
					curl_close($ch);
	
					$XMLobj = new SimpleXMLElement($xmlstr);	
					
				
				
					foreach ($XMLobj->data->row as $obj)
					{	
							$is_selected = false;
							if(!empty($connection_options) && isset($connection_options['depotid']) && $connection_options['depotid'] == $obj->depotid) $is_selected = true;
							$selected = $is_selected ? 'selected' : '' ;
							$result['html'] .= "<option value=\"" . $obj->depotid . "\" " . $selected . " title=\"E\">".$obj->title."</option>";
							
					}
					$result['html'] .= "</select>
            		        </div>
        		        </div>
        		        <div class=\"hr-line-dashed col-lg-12\"></div>"; 
				}
                

            }
        		        
        		        
        	$result['html'] .= "</div></div>";
            
        } else {
            
            $result['html'] = "<div class=\"hpanel\">
        		<div class=\"panel-heading hbuilt\">
        			Настройки Vivat Личный кабинет
        		</div>
        		<div class=\"panel-body\"><div class=\"alert alert-danger\">" . $result['message'] . "</div></div>
        	</div>";
        	
        }
    
    break;
    
    default:
        $result['status'] = false;
        $result['message'] = 'Неизвестное действие';
}




exit(json_encode($result));