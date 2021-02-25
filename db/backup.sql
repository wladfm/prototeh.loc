-- --------------------------------------------------------
-- Хост:                         localhost
-- Версия сервера:               5.7.29-log - MySQL Community Server (GPL)
-- Операционная система:         Win64
-- HeidiSQL Версия:              11.0.0.5958
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Дамп структуры базы данных prototeh
CREATE DATABASE IF NOT EXISTS `prototeh` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;
USE `prototeh`;

-- Дамп структуры для таблица prototeh.chosens
CREATE TABLE IF NOT EXISTS `chosens` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL COMMENT 'ИД пользователя',
  `id_contact` int(11) NOT NULL COMMENT 'ИД контакта',
  PRIMARY KEY (`ID`),
  KEY `id_user` (`id_user`),
  KEY `id_contact` (`id_contact`),
  CONSTRAINT `FK_chosens_contacts` FOREIGN KEY (`id_contact`) REFERENCES `contacts` (`ID`),
  CONSTRAINT `FK_chosens_users` FOREIGN KEY (`id_user`) REFERENCES `users` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Избранные контакты (M-M)';

-- Дамп данных таблицы prototeh.chosens: ~0 rows (приблизительно)
/*!40000 ALTER TABLE `chosens` DISABLE KEYS */;
/*!40000 ALTER TABLE `chosens` ENABLE KEYS */;

-- Дамп структуры для таблица prototeh.contacts
CREATE TABLE IF NOT EXISTS `contacts` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ФИО',
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Телефон',
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'E-Mail',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `phone` (`phone`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Таблица контактов';

-- Дамп данных таблицы prototeh.contacts: ~20 rows (приблизительно)
/*!40000 ALTER TABLE `contacts` DISABLE KEYS */;
INSERT INTO `contacts` (`ID`, `name`, `phone`, `email`) VALUES
	(1, 'Астафьева Алиса Романовна', '375(23)754-95-72', 'o@outlook.com'),
	(2, 'Волкова София Марковна', '375(837)195-08-93', 'hr6zdl@yandex.ru'),
	(3, 'Гончаров Даниил Павлович', '375(4715)500-84-96', 'kaft93x@outlook.com'),
	(4, 'Давыдов Александр Ильич', '375(3650)192-92-48', 'dcu@yandex.ru'),
	(5, 'Журавлев Максим Александрович', '375(4778)255-84-18', '19dn@outlook.com'),
	(6, 'Кожевников Марк Максимович', '375(024)273-88-39', 'pa5h@mail.ru'),
	(7, 'Корнева Мария Александровна', '375(6810)944-72-99', '281av0@gmail.com'),
	(8, 'Кочетков Платон Александрович', '375(4736)779-14-29', '8edmfh@outlook.com'),
	(9, 'Крюкова Вероника Сергеевна', '375(0819)787-86-73', 'sfn13i@mail.ru'),
	(10, 'Лебедев Кирилл Константинович', '375(45)444-27-18', 'g0orc3x1@outlook.com'),
	(11, 'Назаров Дмитрий Кириллович', '375(887)071-06-20', 'rv7bp@gmail.com'),
	(12, 'Николаев Алексей Фёдорович', '375(09)858-16-37', '93@outlook.com'),
	(13, 'Новикова Александра Александровна', '375(820)019-50-73', 'er@gmail.com'),
	(14, 'Сидоров Даниил Арсеньевич', '375(71)876-90-78', 'o0my@gmail.com'),
	(15, 'Соколов Михаил Артёмович', '375(54)702-46-06', '715qy08@gmail.com'),
	(16, 'Степанова Александра Макаровна', '375(1690)986-26-76', 'vubx0t@mail.ru'),
	(17, 'Сухарева Полина Михайловна', '375(534)036-67-63', 'wnhborq@outlook.com'),
	(18, 'Терентьева Полина Романовна', '375(9850)211-22-41', 'gq@yandex.ru'),
	(19, 'Трофимова Ксения Филиповна', '375(04)811-35-58', 'ic0pu@outlook.com'),
	(20, 'Яшина Дарья Данииловна', '375(852)165-04-00', 'o7khr@yandex.ru');
/*!40000 ALTER TABLE `contacts` ENABLE KEYS */;

-- Дамп структуры для таблица prototeh.menu
CREATE TABLE IF NOT EXISTS `menu` (
  `ID` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Наименование',
  `m` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Имя модуля',
  `type_page` smallint(6) NOT NULL DEFAULT '0' COMMENT 'Тип страницы (описание в классе entity_menu)',
  PRIMARY KEY (`ID`) USING BTREE,
  UNIQUE KEY `m` (`m`),
  KEY `type_page` (`type_page`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Меню';

-- Дамп данных таблицы prototeh.menu: ~5 rows (приблизительно)
/*!40000 ALTER TABLE `menu` DISABLE KEYS */;
INSERT INTO `menu` (`ID`, `name`, `m`, `type_page`) VALUES
	(1, 'Главная', 'main', 0),
	(2, 'Список контактов', 'list', 2),
	(3, 'Избранные контакты', 'chosen', 2),
	(4, 'Авторизация', 'login', 1),
	(5, 'Регистрация', 'registration', 1);
/*!40000 ALTER TABLE `menu` ENABLE KEYS */;

-- Дамп структуры для таблица prototeh.sessions
CREATE TABLE IF NOT EXISTS `sessions` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL COMMENT 'ИД пользователя',
  `ip` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'IP-адрес клиента',
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Клиент',
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Токен',
  `date_start` datetime NOT NULL COMMENT 'Дата регистрации токена',
  `date_end` datetime NOT NULL COMMENT 'Дата действия токена',
  PRIMARY KEY (`ID`) USING BTREE,
  KEY `id_user` (`id_user`) USING BTREE,
  KEY `token` (`token`) USING BTREE,
  KEY `ip` (`ip`) USING BTREE,
  KEY `user_agent` (`user_agent`) USING BTREE,
  KEY `date_start` (`date_start`) USING BTREE,
  KEY `date_end` (`date_end`) USING BTREE,
  CONSTRAINT `FK_sessions_users` FOREIGN KEY (`id_user`) REFERENCES `users` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Таблица сессий';

-- Дамп данных таблицы prototeh.sessions: ~0 rows (приблизительно)
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;

-- Дамп структуры для таблица prototeh.users
CREATE TABLE IF NOT EXISTS `users` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Логин',
  `pass` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Пароль (хэш)',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Имя пользователя',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `login` (`login`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Пользователи';

-- Дамп данных таблицы prototeh.users: ~2 rows (приблизительно)
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
