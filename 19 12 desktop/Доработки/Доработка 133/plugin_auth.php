<?php 
if (!empty($_POST["token"]))
{
	$login = $_POST["token"];
}	
if(!empty($_POST["authentication"]))
{
	//Условие для соц сетей
	if( (!empty($_POST["auth_contact"]) && !empty($_POST["password"])) || (!empty($_POST["auth_contact_type"]) && $_POST["auth_contact_type"] == 'social') )
	{	
		$auth_contact_type = $_POST["auth_contact_type"];
		//$auth_contact_type подставляется в SQL-запрос, поэтому провряем его значение
		if( $auth_contact_type != 'email' && $auth_contact_type != 'phone' && $auth_contact_type !="social" )
		{
			exit;
		}
		
		/*
		Ищем по указанному типу контакта (email/phone) пользователя, у которого:
		- найден указанный контакт
		- указанный контакт подтвержден
		- пользователь не заблокирован админом
		- пароль от учетной записи указан верно
		*/
		///////////////12345678
		// Если тип входа - не social, тогда обычная аутентификации
		// Иначе - находим id пользователя из таблицы social_user_data по сочетанию токена, id социальной сети и id клиента 
		
		//Инициализируем auth record
		$auth_record = false;
		
		//Если не social - то обычная авторизация
		if ($auth_contact_type != 'social')
		{
			$auth_contact = $_POST["auth_contact"];
			$password = $_POST["password"];
			$auth_query = $db_link->prepare('SELECT * FROM `users` WHERE `'.$auth_contact_type.'`=? AND `'.$auth_contact_type.'_confirmed` = ? AND `unlocked` =? AND `password`=?;');
			$auth_query->execute( array($auth_contact, 1, 1,  md5($password.$DP_Config->secret_succession) ) );
			$auth_record = $auth_query->fetch();
		}
		//Если social
		//В POST из скрипта соц сети должны передаваться
		//$_POST["token"] - новый токен пользователя
		//$_POST["social_name"] - имя социаьлной сети, которое указали в таблице social
		//$_POST["id"] - id пользователя в соц сети, которое получаем из запроса в скрипте соц сети
		else
		{
			//Определяем id социальной сети из таблицы social
			$SQL = "SELECT `id` FROM `social` WHERE `social_name` = ?";
			$query = $db_link->prepare($SQL);
			$query->execute( array ($_POST["social_name"]));
			$result = $query->fetch();
			
			
			$social_user_id = $_POST["id"];
			$token = $_POST["token"];
			$social_id = $result["id"];
			
			//Поиск указаных значений в таблице `social_user_data` - если находим, то авторизуем
			$auth_query = $db_link->prepare('SELECT * FROM `social_user_data` WHERE `social_user_id`= ? AND `social_id` = ? AND `token` = ?');
			$auth_query->execute( array($social_user_id, $social_id, $token) );
			$auth_record = $auth_query->fetch();
		}
		if( $auth_record == false )
		{
			//Аутентификация не проходит
			//Добавляем к HTML-коду скрипт для сообщения
			if(!empty($_POST["wrong_authentication_tag"]))//Есть имя тега, куда выводить сообщение об ошибке аутентификации
			{
				$DP_Template->html = $DP_Template->html."\n<script>document.getElementById(\"".$_POST["wrong_authentication_tag"]."\").innerHTML = \"Ошибка аутентификации\";</script>";
			}
			else//Конкретный тег не передан - выводим сообщение в стандартный
			{
				$DP_Template->html = $DP_Template->html."\n<script>alert(\"Ошибка аутентификации\");</script>";
			}
		}
		else//Успешная аутентификация
		{
			//Определяем id пользователя:
			$user_id = $auth_record["user_id"];
			$time = time();
			
			
			//Сначала очищаем устаревшие сессии данного пользователя
			//Пользовательские настройки
			$db_link->prepare("DELETE FROM `users_options` WHERE `session_id` IN (SELECT `id` FROM `sessions` WHERE `user_id` = ? AND `last_activiti_time` < ?);")->execute( array($user_id, $last_activiti_time_to_del) );
			//Сами сессии
			$db_link->prepare("DELETE FROM `sessions` WHERE `user_id` = ? AND `last_activiti_time` < ?;")->execute( array($user_id, $last_activiti_time_to_del) );
			
			
			$session_succession = md5($login.$time.$DP_Config->secret_succession);//Код сессии - собираем его из логина, текущего дампа времени и секретной последовательности
			
			//Ключ защиты от CSRF-атак:
			$csrf_guard_key = sha1( $DP_Config->secret_succession . $session_succession . $_SERVER["REMOTE_ADDR"] . $_SERVER["HTTP_USER_AGENT"] );
			
			//Записываем сеcсию в БД
			$db_link->prepare('INSERT INTO `sessions` (`session`, `user_id`, `time`, `data`, `csrf_guard_key`) VALUES (?, ?, ?, ?, ?);')->execute( array($session_succession, $user_id, $time, '', $csrf_guard_key) );

			
			//Записываем сессию в куки:
			if(!empty($_POST["rememberme"]))
			{
				$cookietime = time()+9999999;//Запоминаем пользователя на долго
			}
			else
			{
				$cookietime = 0; // На время работы браузера
			}
			setcookie("session", $session_succession, $cookietime, "/", '',false,true);
			setcookie("u_id", $user_id, $cookietime, "/", '',false,true);
			
			//В куки есть сессия неавторизованного пользователя, переместим товары в корзину авторизованного пользователя
			if( isset( $_COOKIE["session"] ))
			{
				//Проверим есть ли товары в корзине этого пользователя
				$user_cart_query = $db_link->prepare("SELECT `id` FROM `shop_carts` WHERE `user_id` = 0 AND `session_id` = (SELECT `id` FROM `sessions` WHERE `session` = ?);");
				$user_cart_query->execute( array(str_replace(' ','',$_COOKIE["session"])) );
				
				while($shop_carts_id = $user_cart_query->fetch())
				{
					if( (int) $shop_carts_id['id'] > 0 )
					{
						$db_link->prepare('UPDATE `shop_carts` SET `user_id` = ?, `session_id` = 0 WHERE `id` = ?;')->execute( array($user_id, $shop_carts_id['id']) );
					}
				}
			}
			
			if( isset($_POST["target"]) )
			{
				header("Location: ".$DP_Config->domain_path.$_POST["target"]);//Переадресация на определенную страницу
			}
			else
			{
				header("Location: ".getPageUrl());//Переадресация на туже страницу
				
			}
		}
	}
}