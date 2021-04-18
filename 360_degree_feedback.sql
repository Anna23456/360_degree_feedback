-- phpMyAdmin SQL Dump
-- version 5.0.3
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Апр 19 2021 г., 01:52
-- Версия сервера: 10.4.14-MariaDB
-- Версия PHP: 7.4.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `360_degree_feedback`
--

-- --------------------------------------------------------

--
-- Структура таблицы `results`
--

CREATE TABLE `results` (
  `idAboutTest` int(11) NOT NULL,
  `idWhoTookTest` int(11) NOT NULL,
  `typeWhoTookTest` varchar(64) NOT NULL,
  `date` date NOT NULL,
  `idSkill` int(11) NOT NULL,
  `valueAnswer` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `results`
--

INSERT INTO `results` (`idAboutTest`, `idWhoTookTest`, `typeWhoTookTest`, `date`, `idSkill`, `valueAnswer`) VALUES
(1, 1, 'self', '2021-04-19', 1, -2),
(1, 1, 'self', '2021-04-19', 2, -1),
(1, 1, 'self', '2021-04-19', 3, 0),
(1, 1, 'self', '2021-04-19', 4, 1),
(1, 1, 'self', '2021-04-19', 5, 0),
(1, 1, 'self', '2021-04-19', 6, -1),
(1, 1, 'self', '2021-04-19', 7, 0),
(7, 1, 'boss', '2021-04-19', 1, 100),
(7, 1, 'boss', '2021-04-19', 2, -2),
(7, 1, 'boss', '2021-04-19', 3, -1),
(7, 1, 'boss', '2021-04-19', 4, 0),
(7, 1, 'boss', '2021-04-19', 5, 1),
(7, 1, 'boss', '2021-04-19', 6, 2),
(7, 1, 'boss', '2021-04-19', 7, 100);

-- --------------------------------------------------------

--
-- Структура таблицы `skills`
--

CREATE TABLE `skills` (
  `idSkill` int(11) NOT NULL,
  `competency` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `skills`
--

INSERT INTO `skills` (`idSkill`, `competency`) VALUES
(1, 'Целеустремленность'),
(2, 'Инициативность'),
(3, 'Исполнительность'),
(4, 'Командная работа'),
(5, 'Интерес к работе'),
(6, 'Стрессоустойчивость'),
(7, 'Желание развиваться');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `results`
--
ALTER TABLE `results`
  ADD PRIMARY KEY (`idAboutTest`,`idWhoTookTest`,`idSkill`) USING BTREE,
  ADD KEY `idSkill` (`idSkill`);

--
-- Индексы таблицы `skills`
--
ALTER TABLE `skills`
  ADD PRIMARY KEY (`idSkill`);

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `results`
--
ALTER TABLE `results`
  ADD CONSTRAINT `results_ibfk_1` FOREIGN KEY (`idSkill`) REFERENCES `skills` (`idSkill`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
