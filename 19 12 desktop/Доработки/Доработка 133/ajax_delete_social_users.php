<?php
/*
Серверный скрипт для удаления соц сети из профиля пользователя.
*/
header('Content-Type: application/json;charset=utf-8;');
//Соединение с БД
require_once($_SERVER["DOCUMENT_ROOT"]."/config.php");
$DP_Config = new DP_Config;//Конфигурация CMS
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


//Для работы с пользователями
require_once($_SERVER["DOCUMENT_ROOT"]."/content/users/dp_user.php");


//Скрипт могут запускать только авторизованые пользоватеил

if (DP_User::getUserId() == 0 )
{
	$result = array();
	$result["status"] = false;
	$result["message"] = "Forbidden";
	exit(json_encode($result));
}


//Функция удаления соц сети из профиля пользователя
function delete_user_social( $user_id, $social_id, $exist_other_social)
{
	global $db_link;
	//Если других соц сетей больше нет - то в начале меняем флаг social на 0 в таблице users
	
	if (!$exist_other_social)
	{
		$SQL_social_users = "UPDATE `users` SET `social` = 0 WHERE `user_id` = ?";
		if (!$query = $db_link->prepare($SQL_social_users)->execute( array( $user_id )))
		{
			$result = array();
			$result["status"] = false;
			$result["message"] = "Ошибка удаления данных (err 1).";
			exit(json_encode($result));
		}
	}
	
	//Далее - удаляем строку уз таблицы social_user_data
	
	$SQL_delete_social = "DELETE FROM `social_user_data` WHERE `user_id` = ? AND `social_id` = ?;";
	$query = $db_link->prepare($SQL_delete_social)->execute( array( $user_id, $social_id ) );
	if (!$query)
	{
		$result = array();
		$result["status"] = false;
		$result["message"] = "Ошибка удаления данных (err 2).";
		exit(json_encode($result));
	}
	
}




//Проверка, есть ли у пользователя ещё какие-либо способы входа на сайт, иначе после удаления соц сети от не сможет войти на сайт.

//Получаем количество записей о привязанных соц сетях пользователя, где id соц сети не совпадает с текущим id.
$SQL_select_login_data = "SELECT COUNT(*) AS `count` FROM `social_user_data` WHERE `user_id` = ? AND `social_id` != ?;";
$query = $db_link->prepare($SQL_select_login_data);
$query->execute( array( $_POST["user_id"], $_POST["social_id"] ) );

$current_count = $query->fetch();

$count = $current_count["count"];

//Если такие записи есть, то ок - можем отвязывать соц сеть
if ($count > 0)
{
	//Отвязываем
	delete_user_social($_POST["user_id"], $_POST["social_id"], true);
}
else
{
	//Если таких данных нет, то проверяем, есть ли у пользователя подтверждённые email/phone, а так же password.
	$SQL_select_login_data = "SELECT COUNT(*) AS `count` FROM `users` WHERE `user_id` = ? AND ((`phone` != '' AND `phone_confirmed` = 1) OR (`email` != '' AND `email_confirmed` = 1)) AND `password` != '';";
	$query = $db_link->prepare($SQL_select_login_data);
	$query->execute( array( $_POST["user_id"] ) );
	
	$user_data = $query->fetch();
	
	//Такие записи есть - можем удалять
	if ($user_data["count"] > 0 )
	{
		delete_user_social($_POST["user_id"],$_POST["social_id"],false);
	}
	else
	{
		//Таких записей нет - выкидываем
		$result = array();
		$result["status"] = false;
		$result["message"] = "Необходимо установить и подтвердить логин/пароль, так как после удаления данной соц сети не останется других способов входа на сайт.";
		exit(json_encode($result));
	}
}


$result = array();
$result["status"] = true;
$result["message"] = "Ok";
exit(json_encode($result));//Вообще не является администратором бэкенда
?>