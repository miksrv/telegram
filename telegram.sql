-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Фев 09 2018 г., 10:14
-- Версия сервера: 5.7.21-0ubuntu0.16.04.1
-- Версия PHP: 7.0.22-0ubuntu0.16.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `telegram`
--

-- --------------------------------------------------------

--
-- Структура таблицы `test_messages`
--

CREATE TABLE `test_messages` (
  `item_update_id` int(10) NOT NULL,
  `item_message_id` int(10) NOT NULL,
  `item_from_id` int(10) NOT NULL COMMENT 'Alias users table',
  `item_date` int(10) NOT NULL COMMENT 'Unix timestamp',
  `item_text` text CHARACTER SET utf8,
  `item_viewed` tinyint(1) NOT NULL DEFAULT '0',
  `item_answer` tinyint(1) NOT NULL DEFAULT '0',
  `item_hide` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Messages table';

-- --------------------------------------------------------

--
-- Структура таблицы `test_users`
--

CREATE TABLE `test_users` (
  `user_id` int(10) UNSIGNED NOT NULL COMMENT 'User ID',
  `user_first_name` varchar(250) DEFAULT NULL,
  `user_username` varchar(250) DEFAULT NULL,
  `user_language_code` varchar(50) DEFAULT NULL,
  `user_avatar` varchar(200) DEFAULT NULL COMMENT 'User avatar file name',
  `user_favorite` tinyint(1) NOT NULL DEFAULT '0',
  `user_hide` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Telegram user tables';

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `test_messages`
--
ALTER TABLE `test_messages`
  ADD PRIMARY KEY (`item_update_id`);

--
-- Индексы таблицы `test_users`
--
ALTER TABLE `test_users`
  ADD UNIQUE KEY `item_id` (`user_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
