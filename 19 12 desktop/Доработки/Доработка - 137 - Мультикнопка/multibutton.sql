-- phpMyAdmin SQL Dump
-- version 3.4.8
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Окт 30 2021 г., 17:01
-- Версия сервера: 5.1.52
-- Версия PHP: 5.3.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `abcms_shop`
--

-- --------------------------------------------------------

--
-- Структура таблицы `multibutton`
--

CREATE TABLE IF NOT EXISTS `multibutton` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `whatsapp` varchar(255) DEFAULT NULL,
  `viber` varchar(255) DEFAULT NULL,
  `tme` varchar(255) DEFAULT NULL,
  `vk` varchar(255) DEFAULT NULL,
  `insta` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `placement` varchar(255) NOT NULL DEFAULT 'right-bottom',
  `public` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Дамп данных таблицы `multibutton`
--

INSERT INTO `multibutton` (`id`, `whatsapp`, `viber`, `tme`, `vk`, `insta`, `phone`, `placement`, `public`) VALUES
(1, '+79999999999', '8999999999', 'TEST_Service_bot', 'https://vk.com/group', 'sdfdsf', '7343434343', 'bottomRight', 1);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
ALTER TABLE `multibutton` ADD `color` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `public`; 