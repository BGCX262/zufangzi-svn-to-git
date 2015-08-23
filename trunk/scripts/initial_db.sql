-- MySQL Administrator dump 1.4
--
-- ------------------------------------------------------
-- Server version	5.0.77


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


--
-- Create schema zugefangzi
--

CREATE DATABASE IF NOT EXISTS zugefangzi;
USE zugefangzi;

--
-- Definition of table `zugefangzi`.`advertisement`
--
CREATE TABLE  `zugefangzi`.`advertisement` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `type` enum('want','lease') NOT NULL default 'lease',
  `title` varchar(100) NOT NULL,
  `description` text,
  `area` int(11) NOT NULL default '0',
  `address` text NOT NULL,
  `district` varchar(20) default NULL,
  `city` varchar(20) NOT NULL default 'Stockholm',
  `country` int(11) NOT NULL default '46',
  `rent` double NOT NULL default '0',
  `rent_measurement` enum('month','day') NOT NULL default 'month',
  `num_of_room` int(11) NOT NULL default '0',
  `start_date` date NOT NULL,
  `stop_date` date default NULL,
  `password` varchar(100) NOT NULL,
  `status` enum('draft','active','deleted','closed') NOT NULL default 'active',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `advertisement_password_unique` USING BTREE (`password`),
  UNIQUE KEY `advertisement_content_unique` USING BTREE (`user_id`,`title`),
  CONSTRAINT `advertisement_user_fk_constraint` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Definition of table `zugefangzi`.`mail_queue`
--
CREATE TABLE  `zugefangzi`.`mail_queue` (
  `id` int(11) NOT NULL auto_increment,
  `subject` varchar(100) NOT NULL,
  `sender` varchar(50) NOT NULL,
  `recipient` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `created` datetime NOT NULL,
  `sent` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='email queue';

--
-- Dumping data for table `zugefangzi`.`mail_queue`
--

--
-- Definition of table `zugefangzi`.`user`
--
CREATE TABLE  `zugefangzi`.`user` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(20) default NULL,
  `email` varchar(50) character set latin1 NOT NULL,
  `mobile` varchar(20) character set latin1 default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `user_unique` USING BTREE (`email`,`mobile`,`name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `zugefangzi`.`user`
--
INSERT INTO `zugefangzi`.`user` VALUES   (1,'james','lhj1982@gmail.com','+46768502727');
INSERT INTO `zugefangzi`.`user` VALUES   (2,'','li_huajun2007@yahoo.com','');



/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;



-- 2010-08-30
ALTER TABLE `zugefangzi`.`mail_queue` ADD COLUMN `type` ENUM('user', 'contact-us')  NOT NULL DEFAULT 'user' AFTER `id`;
ALTER TABLE `zugefangzi`.`mail_queue` ADD COLUMN `name` VARCHAR(50)  AFTER `type`;

-- 2010-08-31
CREATE TABLE `zugefangzi`.`city` (
  `id` INTEGER  NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(20)  NOT NULL,
  PRIMARY KEY (`id`)
)
ENGINE = InnoDB
CHARACTER SET utf8 COLLATE utf8_general_ci;

alter table city add unique (`name`);

ALTER TABLE `zugefangzi`.`city` DROP INDEX `name`,
 ADD UNIQUE INDEX `city_unique` USING BTREE(`name`);

INSERT INTO city (name) VALUE ('stockholm');
INSERT INTO city (name) VALUE ('uppsala');
 
ALTER TABLE `zugefangzi`.`advertisement` DROP COLUMN `city`;
ALTER TABLE `zugefangzi`.`advertisement` ADD COLUMN `city_id` INTEGER  NOT NULL DEFAULT 1 AFTER `district`,
 ADD CONSTRAINT `city_fk_constraint` FOREIGN KEY `city_fk_constraint` (`city_id`)
    REFERENCES `city` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT;

-- new table
CREATE TABLE `zugefangzi`.`localhelp` (
  `id` INTEGER  NOT NULL AUTO_INCREMENT,
  `city_id` INTEGER  NOT NULL,
  `body` TEXT  NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `localhelp_unique`(`city_id`),
  CONSTRAINT `localhelp_city_fk_constraint` FOREIGN KEY `localhelp_city_fk_constraint` (`city_id`)
    REFERENCES `city` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT
)
ENGINE = InnoDB
CHARACTER SET utf8 COLLATE utf8_general_ci
COMMENT = 'local tips to find/buy apartment/house (wiki-like)';

-- 2010-09-08
ALTER TABLE `zugefangzi`.`city` ADD COLUMN `name_cn` VARCHAR(20)  AFTER `name`;
update city set name_cn='斯德哥尔摩' where id=1;
update city set name_cn='乌普萨拉' where id=2;

-- 2010-09-11
CREATE TABLE `zugefangzi`.`message` (
  `id` INTEGER  NOT NULL,
  `user_id` INTEGER  NOT NULL,
  `type` ENUM('guestbook')  NOT NULL DEFAULT 'guestbook',
  `body` TEXT  NOT NULL,
  `created` DATETIME  NOT NULL,
  CONSTRAINT `message_user_fk_constraint` FOREIGN KEY `message_user_fk_constraint` (`user_id`)
    REFERENCES `user` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT
)
ENGINE = InnoDB
CHARACTER SET utf8 COLLATE utf8_general_ci
COMMENT = 'user messages (guest book and etc)';
ALTER TABLE `zugefangzi`.`message` ADD COLUMN `status` ENUM('active','deleted')  NOT NULL DEFAULT 'active' AFTER `body`;

-- 2010-09-12
ALTER TABLE `zugefangzi`.`city` ADD COLUMN `name_cn_long` VARCHAR(50)  AFTER `name_cn`;
ALTER TABLE `zugefangzi`.`city` ADD COLUMN `name_long` VARCHAR(50)  AFTER `name`;
update city set name_long='east (uppsala,västerås)', name_cn='瑞典东部', name_cn_long='瑞典东部（乌普萨拉，韦斯特罗斯）', name='east' where id=2;
update city set name_long='stockholm', name_cn_long='斯德哥尔摩' where id=1;

-- 2010-10-19
-- add primary key in message
ALTER TABLE `zugefangzi`.`message` MODIFY COLUMN `id` INTEGER  NOT NULL AUTO_INCREMENT,
 ADD PRIMARY KEY (`id`);
 
 
-- 2010-11-05
 CREATE TABLE `zugefangzi`.`advertisement_response` (
  `id` INTEGER  NOT NULL,
  `advertisement_id` INTEGER  NOT NULL,
  `user_id` INTEGER  NOT NULL,
  `created` INTEGER  NOT NULL,
  CONSTRAINT `adv_response_advertisment_fk_constraint` FOREIGN KEY `adv_response_advertisment_fk_constraint` (`advertisement_id`)
    REFERENCES `advertisement` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `adv_response_user_fk_constraint` FOREIGN KEY `adv_response_user_fk_constraint` (`user_id`)
    REFERENCES `user` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT
)
ENGINE = InnoDB
CHARACTER SET utf8 COLLATE utf8_general_ci;

ALTER TABLE `zugefangzi`.`advertisement_response` ADD COLUMN `mail_id` INTEGER  NOT NULL AFTER `user_id`,
 ADD CONSTRAINT `adv_response_mail_fk_constraint` FOREIGN KEY `adv_response_mail_fk_constraint` (`mail_id`)
    REFERENCES `mail_queue` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT;
    
ALTER TABLE `zugefangzi`.`advertisement_response` DROP COLUMN `created`;

ALTER TABLE `zugefangzi`.`advertisement_response` MODIFY COLUMN `id` INTEGER  NOT NULL AUTO_INCREMENT,
 ADD PRIMARY KEY (`id`);
 
 
-- 2010-11-16
insert into city (name, name_long, name_cn, name_cn_long) values ('south', 'south(malmö,lund,Karlskrona)', '瑞典南部', '瑞典南部（马尔默，隆德，卡尔斯克鲁纳）');

-- 2010-11-24
CREATE TABLE `zugefangzi`.`advertisement_extension` (
  `id` INTEGER  NOT NULL AUTO_INCREMENT,
  `advertisement_id` INTEGER  NOT NULL,
  `latitude` DOUBLE  NOT NULL,
  `longitude` DOUBLE  NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `advertisement_extension_fk_constraint` FOREIGN KEY `advertisement_extension_fk_constraint` (`advertisement_id`)
    REFERENCES `advertisement` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT
)
ENGINE = InnoDB
CHARACTER SET utf8 COLLATE utf8_general_ci
COMMENT = 'advertisement extension information, such as google location';

-- 2010-12-06
CREATE TABLE `advertisement_notification` (
    `id` INT(11) NULL,
    `advertisement_id` INT(11) NULL,
    `type` ENUM('close_advertisement') NULL DEFAULT 'close_advertisement',
    `created` DATETIME NULL,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `advertisement_notification_unique` (`advertisement_id`, `type`)
)
COMMENT='advertisement notification, such as close advertisement'
COLLATE='utf8_general_ci'
ENGINE=InnoDB
ROW_FORMAT=DEFAULT
AUTO_INCREMENT=10;

ALTER TABLE `advertisement_notification`
    ADD COLUMN `status` ENUM('valid','invalid') NULL DEFAULT 'valid' AFTER `created`;

ALTER TABLE `advertisement_notification`
    DROP INDEX `advertisement_notification_unique`,
    ADD UNIQUE INDEX `advertisement_notification_unique` (`advertisement_id`, `type`, `status`);
    
ALTER TABLE `mail_queue`
    CHANGE COLUMN `type` `type` ENUM('user','contact-us','close_notification', 'sysinfo') NOT NULL DEFAULT 'user' AFTER `id`;
    
ALTER TABLE `advertisement_notification` CHANGE COLUMN `id` `id` INT(11) NOT NULL AUTO_INCREMENT;

-- 2010-12-30
ALTER TABLE `mail_queue`
    ADD COLUMN `send_time` DATETIME NULL DEFAULT NULL AFTER `created`,
    ADD COLUMN `error_message` TEXT NULL DEFAULT NULL AFTER `sent`;

-- 2011-07-09
CREATE  TABLE `zugefangzi`.`category` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `key` VARCHAR(10) NOT NULL ,
  `name` VARCHAR(45) NOT NULL ,
  `description` TEXT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `key_UNIQUE` (`key` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'category, such as normal apartment, or shops' ;

INSERT INTO `zugefangzi`.`category` (`id`, `key`, `name`) VALUES (1, 'APARTMENT', '房子');
INSERT INTO `zugefangzi`.`category` (`id`, `key`, `name`) VALUES (2, 'SHOP', '商铺');

ALTER TABLE `zugefangzi`.`advertisement` ADD COLUMN `category_id` INT NULL  AFTER `type` , 
  ADD CONSTRAINT `category_fk_constraint`
  FOREIGN KEY (`category_id` )
  REFERENCES `zugefangzi`.`category` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION
, DROP INDEX `advertisement_content_unique` 
, ADD UNIQUE INDEX `advertisement_content_unique` USING BTREE (`user_id` ASC, `title` ASC, `type` ASC, `category_id` ASC) 
, ADD INDEX `category_fk_constraint` (`category_id` ASC) ;


ALTER TABLE `zugefangzi`.`advertisement` DROP FOREIGN KEY `category_fk_constraint` ;
ALTER TABLE `zugefangzi`.`advertisement` CHANGE COLUMN `category_id` `category_id` INT(11) NULL DEFAULT 1  , 
  ADD CONSTRAINT `category_fk_constraint`
  FOREIGN KEY (`category_id` )
  REFERENCES `zugefangzi`.`category` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;
  

-- 2011-07-19
CREATE  TABLE `zugefangzi`.`currency` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `code` VARCHAR(5) NOT NULL ,
  `name` VARCHAR(45) NOT NULL ,
  `html_code` VARCHAR(10) NULL ,
  `display_en` VARCHAR(10) NOT NULL ,
  `display_cn` VARCHAR(10) NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `currency_unique` (`code` ASC, `name` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'currency code' ;  

ALTER TABLE `zugefangzi`.`currency` ADD COLUMN `enabled` TINYINT(1)  NOT NULL DEFAULT false  AFTER `display_cn` ;

INSERT INTO `currency` VALUES (1,'AED','United Arab Emirates, Dirhams',NULL,'','',0),(2,'AFN','Afghanistan, Afghanis',NULL,'','',0),(3,'ALL','Albania, Leke',NULL,'','',0),(4,'AMD','Armenia, Drams',NULL,'','',0),(5,'ANG','Netherlands Antilles, Guilders (also called F',NULL,'','',0),(6,'AOA','Angola, Kwanza',NULL,'','',0),(7,'ARS','Argentina, Pesos',NULL,'','',0),(8,'AUD','Australia, Dollars',NULL,'','',0),(9,'AWG','Aruba, Guilders (also called Florins)',NULL,'','',0),(10,'AZN','Azerbaijan, New Manats',NULL,'','',0),(11,'BAM','Bosnia and Herzegovina, Convertible Marka',NULL,'','',0),(12,'BBD','Barbados, Dollars',NULL,'','',0),(13,'BDT','Bangladesh, Taka',NULL,'','',0),(14,'BGN','Bulgaria, Leva',NULL,'','',0),(15,'BHD','Bahrain, Dinars',NULL,'','',0),(16,'BIF','Burundi, Francs',NULL,'','',0),(17,'BMD','Bermuda, Dollars',NULL,'','',0),(18,'BND','Brunei Darussalam, Dollars',NULL,'','',0),(19,'BOB','Bolivia, Bolivianos',NULL,'','',0),(20,'BRL','Brazil, Brazil Real',NULL,'','',0),(21,'BSD','Bahamas, Dollars',NULL,'','',0),(22,'BTN','Bhutan, Ngultrum',NULL,'','',0),(23,'BWP','Botswana, Pulas',NULL,'','',0),(24,'BYR','Belarus, Rubles',NULL,'','',0),(25,'BZD','Belize, Dollars',NULL,'','',0),(26,'CAD','Canada, Dollars',NULL,'','',0),(27,'CDF','Congo/Kinshasa, Congolese Francs',NULL,'','',0),(28,'CHF','Switzerland, Francs',NULL,'','',0),(29,'CLP','Chile, Pesos',NULL,'','',0),(30,'CNY','China, Yuan Renminbi',NULL,'','',0),(31,'COP','Colombia, Pesos',NULL,'','',0),(32,'CRC','Costa Rica, Colones',NULL,'','',0),(33,'CUP','Cuba, Pesos',NULL,'','',0),(34,'CVE','Cape Verde, Escudos',NULL,'','',0),(35,'CYP','Cyprus, Pounds (expires 2008-Jan-31)',NULL,'','',0),(36,'CZK','Czech Republic, Koruny',NULL,'','',0),(37,'DJF','Djibouti, Francs',NULL,'','',0),(38,'DKK','Denmark, Kroner',NULL,'Kr','丹麦克朗',1),(39,'DOP','Dominican Republic, Pesos',NULL,'','',0),(40,'DZD','Algeria, Algeria Dinars',NULL,'','',0),(41,'EGP','Egypt, Pounds',NULL,'','',0),(42,'ERN','Eritrea, Nakfa',NULL,'','',0),(43,'ETB','Ethiopia, Birr',NULL,'','',0),(44,'EUR','Euro Member Countries, Euro',NULL,'Euro','欧元',1),(45,'FJD','Fiji, Dollars',NULL,'','',0),(46,'FKP','Falkland Islands (Malvinas), Pounds',NULL,'','',0),(47,'GBP','United Kingdom, Pounds',NULL,'','',0),(48,'GEL','Georgia, Lari',NULL,'','',0),(49,'GGP','Guernsey, Pounds',NULL,'','',0),(50,'GHS','Ghana, Cedis',NULL,'','',0),(51,'GIP','Gibraltar, Pounds',NULL,'','',0),(52,'GMD','Gambia, Dalasi',NULL,'','',0),(53,'GNF','Guinea, Francs',NULL,'','',0),(54,'GTQ','Guatemala, Quetzales',NULL,'','',0),(55,'GYD','Guyana, Dollars',NULL,'','',0),(56,'HKD','Hong Kong, Dollars',NULL,'','',0),(57,'HNL','Honduras, Lempiras',NULL,'','',0),(58,'HRK','Croatia, Kuna',NULL,'','',0),(59,'HTG','Haiti, Gourdes',NULL,'','',0),(60,'HUF','Hungary, Forint',NULL,'','',0),(61,'IDR','Indonesia, Rupiahs',NULL,'','',0),(62,'ILS','Israel, New Shekels',NULL,'','',0),(63,'IMP','Isle of Man, Pounds',NULL,'','',0),(64,'INR','India, Rupees',NULL,'','',0),(65,'IQD','Iraq, Dinars',NULL,'','',0),(66,'IRR','Iran, Rials',NULL,'','',0),(67,'ISK','Iceland, Kronur',NULL,'','',0),(68,'JEP','Jersey, Pounds',NULL,'','',0),(69,'JMD','Jamaica, Dollars',NULL,'','',0),(70,'JOD','Jordan, Dinars',NULL,'','',0),(71,'JPY','Japan, Yen',NULL,'','',0),(72,'KES','Kenya, Shillings',NULL,'','',0),(73,'KGS','Kyrgyzstan, Soms',NULL,'','',0),(74,'KHR','Cambodia, Riels',NULL,'','',0),(75,'KMF','Comoros, Francs',NULL,'','',0),(76,'KPW','Korea (North), Won',NULL,'','',0),(77,'KRW','Korea (South), Won',NULL,'','',0),(78,'KWD','Kuwait, Dinars',NULL,'','',0),(79,'KYD','Cayman Islands, Dollars',NULL,'','',0),(80,'KZT','Kazakhstan, Tenge',NULL,'','',0),(81,'LAK','Laos, Kips',NULL,'','',0),(82,'LBP','Lebanon, Pounds',NULL,'','',0),(83,'LKR','Sri Lanka, Rupees',NULL,'','',0),(84,'LRD','Liberia, Dollars',NULL,'','',0),(85,'LSL','Lesotho, Maloti',NULL,'','',0),(86,'LTL','Lithuania, Litai',NULL,'','',0),(87,'LVL','Latvia, Lati',NULL,'','',0),(88,'LYD','Libya, Dinars',NULL,'','',0),(89,'MAD','Morocco, Dirhams',NULL,'','',0),(90,'MDL','Moldova, Lei',NULL,'','',0),(91,'MGA','Madagascar, Ariary',NULL,'','',0),(92,'MKD','Macedonia, Denars',NULL,'','',0),(93,'MMK','Myanmar (Burma), Kyats',NULL,'','',0),(94,'MNT','Mongolia, Tugriks',NULL,'','',0),(95,'MOP','Macau, Patacas',NULL,'','',0),(96,'MRO','Mauritania, Ouguiyas',NULL,'','',0),(97,'MTL','Malta, Liri (expires 2008-Jan-31)',NULL,'','',0),(98,'MUR','Mauritius, Rupees',NULL,'','',0),(99,'MVR','Maldives (Maldive Islands), Rufiyaa',NULL,'','',0),(100,'MWK','Malawi, Kwachas',NULL,'','',0),(101,'MXN','Mexico, Pesos',NULL,'','',0),(102,'MYR','Malaysia, Ringgits',NULL,'','',0),(103,'MZN','Mozambique, Meticais',NULL,'','',0),(104,'NAD','Namibia, Dollars',NULL,'','',0),(105,'NGN','Nigeria, Nairas',NULL,'','',0),(106,'NIO','Nicaragua, Cordobas',NULL,'','',0),(107,'NOK','Norway, Krone',NULL,'Kr','挪威克朗',1),(108,'NPR','Nepal, Nepal Rupees',NULL,'','',0),(109,'NZD','New Zealand, Dollars',NULL,'','',0),(110,'OMR','Oman, Rials',NULL,'','',0),(111,'PAB','Panama, Balboa',NULL,'','',0),(112,'PEN','Peru, Nuevos Soles',NULL,'','',0),(113,'PGK','Papua New Guinea, Kina',NULL,'','',0),(114,'PHP','Philippines, Pesos',NULL,'','',0),(115,'PKR','Pakistan, Rupees',NULL,'','',0),(116,'PLN','Poland, Zlotych',NULL,'','',0),(117,'PYG','Paraguay, Guarani',NULL,'','',0),(118,'QAR','Qatar, Rials',NULL,'','',0),(119,'RON','Romania, New Lei',NULL,'','',0),(120,'RSD','Serbia, Dinars',NULL,'','',0),(121,'RUB','Russia, Rubles',NULL,'','',0),(122,'RWF','Rwanda, Rwanda Francs',NULL,'','',0),(123,'SAR','Saudi Arabia, Riyals',NULL,'','',0),(124,'SBD','Solomon Islands, Dollars',NULL,'','',0),(125,'SCR','Seychelles, Rupees',NULL,'','',0),(126,'SDG','Sudan, Pounds',NULL,'','',0),(127,'SEK','Sweden, Kronor',NULL,'Kr','瑞典克郎',1),(128,'SGD','Singapore, Dollars',NULL,'','',0),(129,'SHP','Saint Helena, Pounds',NULL,'','',0),(130,'SLL','Sierra Leone, Leones',NULL,'','',0),(131,'SOS','Somalia, Shillings',NULL,'','',0),(132,'SPL','Seborga, Luigini',NULL,'','',0),(133,'SRD','Suriname, Dollars',NULL,'','',0),(134,'STD','S?o Tome and Principe, Dobras',NULL,'','',0),(135,'SVC','El Salvador, Colones',NULL,'','',0),(136,'SYP','Syria, Pounds',NULL,'','',0),(137,'SZL','Swaziland, Emalangeni',NULL,'','',0),(138,'THB','Thailand, Baht',NULL,'','',0),(139,'TJS','Tajikistan, Somoni',NULL,'','',0),(140,'TMM','Turkmenistan, Manats',NULL,'','',0),(141,'TND','Tunisia, Dinars',NULL,'','',0),(142,'TOP','Tonga, Pa\'anga',NULL,'','',0),(143,'TRY','Turkey, New Lira',NULL,'','',0),(144,'TTD','Trinidad and Tobago, Dollars',NULL,'','',0),(145,'TVD','Tuvalu, Tuvalu Dollars',NULL,'','',0),(146,'TWD','Taiwan, New Dollars',NULL,'','',0),(147,'TZS','Tanzania, Shillings',NULL,'','',0),(148,'UAH','Ukraine, Hryvnia',NULL,'','',0),(149,'UGX','Uganda, Shillings',NULL,'','',0),(150,'USD','United States of America, Dollars',NULL,'','',0),(151,'UYU','Uruguay, Pesos',NULL,'','',0),(152,'UZS','Uzbekistan, Sums',NULL,'','',0),(153,'VEB','Venezuela, Bolivares (expires 2008-Jun-30)',NULL,'','',0),(154,'VEF','Venezuela, Bolivares Fuertes',NULL,'','',0),(155,'VND','Viet Nam, Dong',NULL,'','',0),(156,'VUV','Vanuatu, Vatu',NULL,'','',0),(157,'WST','Samoa, Tala',NULL,'','',0),(158,'XAF','Communauté Financière Africaine BEAC, Francs',NULL,'','',0),(159,'XAG','Silver, Ounces',NULL,'','',0),(160,'XAU','Gold, Ounces',NULL,'','',0),(161,'XCD','East Caribbean Dollars',NULL,'','',0),(162,'XDR','International Monetary Fund (IMF) Special Dra',NULL,'','',0),(163,'XOF','Communauté Financière Africaine BCEAO, Francs',NULL,'','',0),(164,'XPD','Palladium Ounces',NULL,'','',0),(165,'XPF','Comptoirs Fran?ais du Pacifique Francs',NULL,'','',0),(166,'XPT','Platinum, Ounces',NULL,'','',0),(167,'YER','Yemen, Rials',NULL,'','',0),(168,'ZAR','South Africa, Rand',NULL,'','',0),(169,'ZMK','Zambia, Kwacha',NULL,'','',0),(170,'ZWD','Zimbabwe, Zimbabwe Dollars',NULL,'','',0);

ALTER TABLE `zugefangzi`.`advertisement` ADD COLUMN `currency` INT(11) NOT NULL DEFAULT 127  AFTER `rent_measurement` , 
  ADD CONSTRAINT `advertisement_currency_fk_constraint`
  FOREIGN KEY (`currency` )
  REFERENCES `zugefangzi`.`currency` (`id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION
, ADD INDEX `advertisement_currency_fk_constraint` (`currency` ASC) ;

-- 2011-08-29
CREATE  TABLE `advertising_agency` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `city_id` INT NOT NULL ,
  `user_id` INT NOT NULL ,
  `description` TEXT NOT NULL ,
  `comment` TEXT NOT NULL ,
  `created` DATETIME NOT NULL ,
  `modified` DATETIME NULL ,
  `status` ENUM('active','closed','expired') NOT NULL DEFAULT 'active' ,
  PRIMARY KEY (`id`) ,
  INDEX `advertising_agency_fk_constraint1` (`city_id` ASC) ,
  INDEX `advertising_agency_fk_constraint2` (`user_id` ASC) ,
  CONSTRAINT `advertising_agency_fk_constraint1`
    FOREIGN KEY (`city_id` )
    REFERENCES `city` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `advertising_agency_fk_constraint2`
    FOREIGN KEY (`user_id` )
    REFERENCES `user` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = 'advertising agency request' ;

-- 2011-09-13
ALTER TABLE `mail_queue` ADD COLUMN `ipaddress` VARCHAR(15) NOT NULL  AFTER `error_message` ;

-- 2011-09-24
ALTER TABLE `message` ADD COLUMN `ipaddress` VARCHAR(15) NOT NULL  AFTER `created` ;

-- insert data, put into very end.
INSERT INTO `localhelp` (`id`, `city_id`, `body`) VALUES
(1, 1, '<h2>斯德哥尔摩(Stockholm) 找房&租房贴士</h2>\r\n<p>各位即将来到瑞典斯德哥尔摩留学，工作的华人朋友，以下是笔者收集的一些关于本地区寻找房子的信息，希望对各位有所帮助。欢迎纠错，补充。</p>\r\n\r\n\r\n\r\n<p>租房大致上分为 系统排房 和 民间交易</p>\r\n\r\n<p>\r\n\r\n\r\n<h3>1-A 系统排房</h3>\r\n<p>一旦注册成为会员排队即为开始，排队时间越长，积分越多，积分越多越容易租到房子。比较常用的是\r\n\r\n\r\n<p><a href="http://www.sssb.se/">Stiftelsen Stockholms Studentbostäder\r\n简称 SSSB</a><br />\r\n特点：必须是斯德哥尔摩的学生，房源离学校都较近，房源多、价格合理，但排的人多，好房子尤其是家庭房比较难排。建议新到留学生先注册着攒积分。</p>\r\n<p><a href="http://www.svenskabostader.se/">Svenska Bostäder 简称 SB</a><br />\r\n特点：瑞典最大的排房网站，容易排到，但价格高</p>\r\n\r\n<p><a href="http://www.bostad.stockholm.se/">Stockholm stads\r\nbostadsförmedling</a><br />\r\n特点：需要排的时间周期比较长</p>\r\n\r\n<p><a href="http://www.signalisten.se/">Signalisten</a><br />\r\n特点：提供长租房，仅限于solna区</p>\r\n</p>\r\n</p>\r\n\r\n\r\n\r\n\r\n<p>\r\n<h3>1-B 民间交易</h3>\r\n<p>一般为二手房或者家庭房，优点是:成交快，有机会接触当地家庭；缺点是：要注意安全，谨防欺诈。比较常用的是\r\n\r\n\r\n<p><a href="http://www.blocket.se/">Blocket</a><br />\r\n特点：瑞典最大的二手交易网站，选择Stockholm->Bostad</p>\r\n<p><a href="http://kina.cc">kina.cc</a><br />\r\n特点：需注册才能看帖，是华人信息交流的综合论坛</p>\r\n\r\n<p><a href="http://www.bostaddirekt.com">Bostad Direkt</a><br />\r\n特点：房源多，找房快，但需要注册并交费才能看到联系方式</p>\r\n</p>\r\n</p>\r\n\r\n<p>\r\n<h3>2 斯德哥尔摩几大学区房分布</h3>\r\n<p>\r\n<p>\r\n<h4>Strix,Pax</h4>\r\n地铁蓝线Västera Skogen站。靠近KTH kista校区，KI solna校区，斯德哥尔摩经济学院\r\n\r\n</p>\r\n<p>\r\n<h4>Lappis</h4>\r\n地铁红线Universitet站。靠近KTH主校区，斯大主校区\r\n</p>\r\n\r\n<p>\r\n<h4>Kungshamra</h4>\r\n地铁红线Bergshamra站。靠近KTH主校区，斯大主校区\r\n</p>\r\n\r\n<p>\r\n<h4>Kista民工房</h4>\r\n地铁蓝线Kista站。靠近KTH kista校区\r\n</p>\r\n\r\n<p>\r\n<h4>Flemingsberg</h4>\r\n小火车flemingsberg站。靠近KI flemingsberg校区\r\n</p>\r\n\r\n</p></p>'),
(2, 2, '<h2>【Uppsala】瑞典 乌普萨拉 留学生找房&租房全功略！- by 胡劲之（芝麻烧饼，Merlin250）</h2>\r\n<p>各位即将来到瑞典乌普萨拉（Uppsala）就读的同学（Uppsala大学以及SLU大学），\r\n以下这是笔者收集的一些关于Uppsala地区寻找房子的信息，希望对各位有所帮助。</p>\r\n\r\n<p>疏漏之处，在所难免，如有发现，请各位及时和我取得联系，这样能尽快修改，别误导了其他同学。\r\n此外，如果有同学居住在Uppsala其他地区，没有列在我写出的这些区域，都随时欢迎你们写攻略补充进来。</p>\r\n\r\n<p>我的Email Address：merlin250@gmail.com\r\n-- 胡劲之（芝麻烧饼，Merlin250）</p>\r\n \r\n <p>\r\n<h3>1，几个说明和建议，</h3>\r\n \r\n<p>首先，我先大略说说，在瑞典租房，还不是简单的“签约-租房-付款-入住”，这样的流程。\r\n这边大多都是“排房”制度，每个人在各个不同的房屋公司注册一个账号，然后进入排队流程。\r\n一般是每等一天就算一分，叠加上去。分数越高在选房的时候就越有优势。</p>\r\n \r\n<p>以上说了这么多，只是单纯为了让各位同学了解一下，在瑞典租房多多少少还是有些难度的。</p>\r\n \r\n<p>Uppsala是个大学城，留学生数量甚众，所以对于留学生来说，仅仅注册了一天两天，排到房子的概率不高。\r\n所以在一开始准备来瑞典留学的同时，拿AD，装包的同时，还得操心一下自己以后的住房问题。\r\n在大家还感到茫然的时候，我推荐这篇文章给各位看看，英语版本的“官方”排房信息。</p>\r\n<p><a href="http://www.uppsalastudentkar.nu/en/housing/generalguide">http://www.uppsalastudentkar.nu/en/housing/generalguide</a></p>\r\n \r\n<p>这个页面是隶属于Uppsala学生会（Student Union）的，就好像工人有工会，学生在瑞典也有学生会。\r\n学生会除了帮学生解决实际生活中的一些困难以外，也会在学生受到不公正待遇的时候为学生出头。\r\n比如去和某位PHD学生的老板为了学生的薪水和工作超量问题扯皮，等等，在这边是相当有实力的组织。</p>\r\n\r\n<p>文章中介绍了不少可供留学生选取的房屋的公司的链接，同学们要主动在这些公司的页面注册并取得联系。\r\n此外，在这里公布两个人的联系方式，他们是2007年负责 Student Union 供给留学生房屋的工作人员。\r\n据我所知，2008年夏天，这二位依旧在那里任职。如果大家在排房子的时候实在有困难，可以向他们询问。</p>\r\n<p>Martin Breed，martin.breed@gmail.com<br />\r\nMaria Marklund，marklund_maria@hotmail.com</p>\r\n \r\n<p>另附，两个以前的房屋相关帖子链接,<br />\r\n<a href="http://merlin250.spaces.live.com/blog/cns!48962ECAF0B29852!752.entry">（1-1）送给UU生物系新同学的房屋建议</a><br />\r\n<a href="http://merlin250.spaces.live.com/blog/cns!48962ECAF0B29852!745.entry">（1-2）给即将来Uppsala地区读书的同学一些接机住房相关信息</a>\r\n\r\n</p>\r\n </p>\r\n \r\n <p>\r\n<h3>2，学生常住公寓情况介绍，</h3>\r\n \r\n <p>\r\n<h4>（2-1）Corridor Room</h4>\r\n一般是数人住在一个单元里，\r\n人数多的，12人一个单元，\r\n人数少的，3人或者5人一个单元。<br />\r\n公用厨房和活动室，一个过道，两侧都是住户。\r\n3人活着5人合用的Corridor有些像Apartment的一套房，<br />\r\n不过由于公司出租的时候是按个人为单位出租，所以仍旧归纳为Corridor Room之列。</p>\r\n <p>\r\n<h4>（2-2）Apartment</h4>\r\n一套一套出租的，两房一厅，三房一厅的房子。<br />\r\nApartment 是相对合算的房子，一套三房一厅的房子一个月含水电5500上下，\r\n基本上，三个人合租一个 Apartment 是在Uppsala最省的住房条件了。<br />\r\n不过就如上面所述，好房子人人想要，自然要排不少时候才有机会租到 Apartment。\r\n按照在这边的时间估计，一般一年左右的房号可以再某些供给学生的 Apartment 区域排到房子。</p>\r\n </p>\r\n <p>\r\n<h3>3，Uppsala 华人留学生常住的区域介绍，</h3>\r\n \r\n<p><h4>（3-1）Flogsta，包括一个Corridor区域和一个Apartment区域。</h4>\r\n<p>Flogsta Corridor Room 属于 Heimstaden 公司，是个“高”楼区（7层楼市瑞典人所谓的高楼）\r\n可以在Wiki上看到关于这个区的词条，http://en.wikipedia.org/wiki/Flogsta</p>\r\n\r\n</p>价格是含水电 3000sek 一个月，在Uppsala算是比较贵的学生房，独立卫浴，10人或者12人共用厨房。\r\n优点是这边国际学生很多，主要是留学生区，所以英语氛围不错。周末Party很多。\r\n公寓面积是19平米，室内网速很快，隔音好。</p>\r\n \r\n<p>每年交10个月的房租，6，7月房间免费。</p>\r\n \r\n<p>Flogsta Apartment 属于 studentstaden 公司，\r\n价格，两房一厅 4850sek，三房一厅 5300sek，这都是不含水电费的价格。\r\n水电费一般是3个月一交，每次 300sek 左右。\r\n这个区域比较安静，房间价格均摊之后也挺值的，是不少华人学生推崇的居住区。\r\n每年交12月的房租。</p>\r\n <p>\r\nFlogsta 距离生物系的主楼 BMC 骑车要20分钟左右，距离生物系另一个楼 EBC 骑车10分钟。<br />\r\nFlogsta 距离计算机系和数学系的主楼 MIC（IT college）骑车25分钟左右。<br />\r\nFlogsta 距离化学和物理系的主楼 Angström 骑车25分钟。<br />\r\nFlogsta 距离经济系主楼 Economic 骑车10分钟。<br />\r\nFlogsta 距离英语学院主楼（在 Carolina 图书馆旁）骑车10分钟。<br />\r\nFlogsta 距离地理学院主楼 GEO 骑车15分钟。<br />\r\nFlogsta 距离瑞典农大（SLU）主楼骑车35分钟左右。</p>\r\n <p>\r\n此外，FLogsta 距离一个超市很近，叫做 ICA väst，这个超市的价格不是很贵。\r\n距离比较便宜的 LIDL 超市骑车 10分钟，也挺方便。</p>\r\n</p> \r\n<p>\r\n<h4>（3-2）Triangle，这个区域因为从地图上看是个三角形，所以 UU 的学生普遍这么称呼她，</h4>\r\n \r\n<p>Triangle 的学生房是属于 V Dala Nation 的，Nation 是 Uppsala 特有的学生组织在这里不做赘述。\r\n一般同学们只有在学校注册之后，加入了 Nation 才有机会在 Triangle 地区排到房子。\r\n这边的房子优点在于位置好，去任何一个学院都挺近的，价格也不贵，\r\n是小户型的 Corridor Room，3人合住的 2600sek每间房，公用洗手间和卫浴。\r\n如果是5人合住的是 2900sek每间房，独立卫生间，公用厨房。</p>\r\n \r\n<p>每年交10个月的房租，7，8月房间免费。</p>\r\n <p>\r\nTriangle 距离生物系的主楼 BMC 骑车要10分钟左右，距离生物系另一个楼 EBC 骑车4分钟。<br />\r\nTriangle 距离计算机系和数学系的主楼 MIC（IT college）骑车15分钟左右。<br />\r\nTriangle 距离化学和物理系的主楼 Angström 骑车15分钟。<br />\r\nTriangle 距离经济系主楼 Economic 骑车4分钟。<br />\r\nTriangle 距离英语学院主楼（在 Carolina 图书馆旁）骑车7分钟。<br />\r\nTriangle 距离地理学院主楼 GEO 骑车4分钟。<br />\r\nTriangle 距离瑞典农大（SLU）主楼骑车25分钟左右。</p>\r\n </p>\r\n <p>\r\n<h4>（3-3）Rackarberget，这个区域距离 Triangle 挺近的，</h4>\r\n \r\n<p>Rackarberget 的学生房是属于 studentstaden 公司，5人或者7人左右的 Corridor Room，\r\n价格是含水电 2500sek每月，公用卫浴。\r\n独立卫生间，公用厨房和浴室，2700sek每月。\r\n这里也是一个主要的学生公寓区，户型比较多。\r\n要是在经济学院和英语学院就读，这里是最佳选择。</p>\r\n <p>\r\nRackarberget 距离生物系的主楼 BMC 骑车要15分钟左右，距离生物系另一个楼 EBC 骑车7分钟。<br />\r\nRackarberget 距离计算机系和数学系的主楼 MIC（IT college）骑车18分钟左右。<br />\r\nRackarberget 距离化学和物理系的主楼 Angström 骑车18分钟。<br />\r\nRackarberget 距离经济系主楼 Economic 骑车4分钟。<br />\r\nRackarberget 距离英语学院主楼（在 Carolina 图书馆旁）骑车7分钟。<br />\r\nRackarberget 距离地理学院主楼 GEO 骑车7分钟。<br />\r\nRackarberget 距离瑞典农大（SLU）主楼骑车25分钟左右。</p>\r\n <p>\r\n每年交10个月的房租，6，7月房间免费。</p>\r\n <p>\r\nTriangle，Rackarberget 附近有一个 ICA 小超市，不过价格较贵，货物品种也少些。\r\n一般住在这两个区域的中国学生都是去 Willys 购物，骑车要15分钟左右到达。\r\n还有一处在 V-Dala Nation 附近的 ICA 超市，俗称排骨店，也还不错，骑车过去5分钟。</p>\r\n </p>\r\n <p>\r\n<h4>（3-4）Dobelnsgatan，护士楼，</h4>\r\n<p>\r\n一般都称这个区域做护士楼。这是 Uppsala 最便宜的学生区。\r\n优势在于价格和地理位置，劣势也很明显。\r\n护士楼距离 BMC，MIC，Angström 都特别近，一般走路溜达就去了。\r\n价格是 2100sek 左右每月，公用厨房和卫浴。\r\n这里住了很多的中国同学，印度学生，以及黑人学生，\r\n所以在居住环境方面要比上述几处有些差距。</p>\r\n <p>\r\n每年交10个月的房租，6，7月房间免费。\r\n这里刚刚换了房屋公司，</p>\r\n \r\n<p>（以下感谢蔡延玲同学提供信息！）</p>\r\n <p>\r\n那个uppsala最大的房东uppsalahem, 网址：<a href="www.uppsalahem.se">www.uppsalahem.se</a> . \r\nappartment挺多，分布很广，但是也是按注册时间长短看的，公司的人说4，5年才能排到。学生房相对容易点。\r\n最近刚刚买下护士楼，护士楼也有很多人搬走，可以关注一下网站。</p>\r\n <p>\r\nDobelnsgatan（护士楼） 距离生物系的主楼 BMC 骑车要1分钟左右，距离生物系另一个楼 EBC 骑车5分钟。 <br />\r\nDobelnsgatan（护士楼） 距离计算机系和数学系的主楼 MIC（IT college）骑车5分钟左右。 <br />\r\nDobelnsgatan（护士楼） 距离化学和物理系的主楼 Angström 骑车5分钟。 <br />\r\nDobelnsgatan（护士楼） 距离经济系主楼 Economic 骑车10分钟。 <br />\r\nDobelnsgatan（护士楼） 距离英语学院主楼（在 Carolina 图书馆旁）骑车6分钟。 <br />\r\nDobelnsgatan（护士楼） 距离地理学院主楼 GEO 骑车5分钟。 <br />\r\nDobelnsgatan（护士楼） 距离瑞典农大（SLU）主楼骑车15分钟左右。</p>\r\n </p></p>\r\n <p>\r\n<h3>4，Uppsala 排房公司注册方法。</h3>\r\n <p>\r\n<h4>（4-1）Studentstaden 公司，以 Flogsta Apartment 为例，感谢孙伟伦提供详细排房攻略。</h4>\r\n <p> \r\n4-1-1. 上學校提供的租屋網站 建立帳號密碼 (可以選擇英文版本 不需要 swedish ID number) \r\nhttp://studentstaden.devainvision.se/default.asp<br />\r\n4-1-2. 回覆郵件確認後帳號生效 就可以選擇房子<br />\r\n4-1-3. 排房的優先順序是依照你的帳號存在的時間 時間越長 順位越前面<br />\r\n4-1-4. 如果有家庭 告知租屋公司後 可以直接多六個月的時間 幫助你早日排到房子<br />\r\n4-1-5. 帳號必須每六個月登入一次 否則會被停止<br />\r\n4-1-6. 排到房以後 帶著 Swedish ID, Registration letter 去 Vasakronan 簽約<br />\r\n4-1-7. 租屋公司會提醒你 必須要打電話告知 vaterfall 你搬進去了 開始算用電量 開始交電費<br />\r\n4-1-8. 房租是提前一個月交 電費是每兩個月收一次 (不包含暖氣)</p> \r\n</p> <p> \r\n<h4>（4-2）V-Dala Nation，以 Triangle 学生公寓为例，感谢李娜同学提供详细排房攻略。</h4>\r\n <p>\r\n应该各个 NATION 都有房子，但是房子最多，而且最容易申请到得就是 V-DALA NATION。\r\nV-DALA 一般一个月都有有一次分房大会，至于具体每个月的几号要询问 V-DALA 的分房办公室。</p>\r\n <p>\r\n这个办公室的联系方式如下：<br />\r\nThomas Hansson / Hyresvrd Stiftelsen<br />\r\nVstmanland-Dala Nations Studentbostder<br />\r\nSvartmangatan 16，753 12 UPPSALA<br />\r\nkontor:018-12 80 70.<br />\r\nfax:018-10 15 28.<br />\r\nOffice opening hours: Monday 10-12, wednesday 16-18, thursday 10-12<br />\r\nTelephonehours mon 09:00-10:00, wed 15:00-16:00, thur 09:00-10:00<br />\r\nEmail landlord: bostad@v-dala.nation.uu.se</p>\r\n <p>\r\n一般写邮件他们都不会回，最好是在办公时间亲自自己去一趟询问。</p>\r\n <p>\r\n在分房大会上要携带护照，录取通知书，当然还有会员卡进行登记，\r\n（只有是这个 NATION 的会员才可以入住这个 NATION 的房子）。\r\n然后他们会根据入会的时间长短对进行登记的会员抽签，一个 SEMESTER 算一分，\r\n从分多的会员开始抽，先抽到得会员就可以在所有 AVAILABLE 的空房里挑，\r\n依次下去，直到所有空房都有 APPLY 掉。\r\n每个月 AVAILABLE 的空房数量悬殊很大，一般来说新的学期换季会有很多人搬家，\r\n如果平常的话，一个月估计也就有两三套空房（除护士楼外）。</p>\r\n <p>\r\nV-DALA的房子最抢手的应该是 Triangeln 这边的 CORRIDOR，\r\n据我知道的有三人和5人两种，三人要SHARE BATHROOM，房租2300左右。\r\n5人是有单独的卫生间，房租大约是2900左右。\r\n这些 CORRIDOR 是7，8月免房租。\r\nV-DALA 也有 APARTMENT，但是不多，而且比较贵，性价比比较低。</p>\r\n </p> <p> \r\n<h4>（4-3）Heimstaden 公司，以及 vasakronan 公司，因为这俩家公司都可以订到 Flogsta Corridor 的房子，</h4>\r\n <p>\r\n4-3-1，Heimstaden\r\nhttp://www.heimstaden.com/uppsala-1.aspx\r\n这是 heimstaden 公司的主页，有英语界面，注册了之后也要排天数等着。</p>\r\n \r\n<p>4-3-2，Vasakronan，谢谢梁砺文同学提供详细攻略。\r\nhttp://bostad.vasakronan.se/HSS/Default.aspx\r\n注册，然后登陆，在里面选房子，选好了排着\r\n老版的是可以选一次选5个排，房子的具体信息点击房子名字就可以看到\r\n以前好象是全部有英文的，现在的新版网站都是瑞典语了。\r\n这个公司就是论排队天数等房子。</p>\r\n<p>\r\n4-3-2（新补充，非常感谢龚天虹同学提供详细攻略）\r\n\r\nVasakronan 排房攻略\r\n\r\n<a href="http://bostad.vasakronan.se/HSS/Default.aspx">http://bostad.vasakronan.se/HSS/Default.aspx </a>\r\n\r\n<p><strong>4-3-2-1. 注册帐号</strong><br />\r\n\r\n1) 点击Registera dig<br />\r\n\r\n2) 如已有瑞典人口号，选择Svenskt personnr: Ja，在Person/Orgnr: 填入12位人口号，如19801101-1234，如无人口号，选择Svenskt personnr: Nej，在Person/Orgnr：填入8位生日加4位英文字母，如19801101-ABCD；在Jag söker: 勾出需要搜索房屋的类型: 家庭住房与学生住房。\r\n\r\n点击下一步(Nästa steg)<br />\r\n\r\n3) 粗体字为必填内容\r\n\r\nFörnamn: 名，Efternamn: 姓， c/o adress: 邮箱地址 (非必填)，Bostadsadress：住址， Postnummer: 邮编，Postadress: 邮寄地址(填住址即可)，Telefon dagtid: 日常电话号码，Telefon kvällstid: 夜间电话号码 (非必填)，Telefon mobil: 手机号码(非必填)，E-post：电子邮件地址\r\n\r\n点击下一步(Nästa steg)<br />\r\n\r\n4) Sökandeuppgifter 公寓申请信息填写(若之前未勾Bostad则此步跳过)\r\n\r\nAntal vuxna:  成人数量， Antal barn: 小孩数量\r\n\r\nHushållets årsinkomst: 家庭年收入，Typ av inkomst: 收入类型\r\n\r\nNuvarande boendeform: 目前住房\r\n\r\n(Bor i föräldrahem/Lives in parental Rent apartment; Hyr lägenhet i andra hand/二手房; Villa/Villa; Radhus/Townhouse; Hyresrätt; Eget kontrakt hos AP Fastigheter/AP签约房; Bostadsrätt;Inneboende) \r\n\r\nSkäl för flytt: 搬家原因\r\n\r\n(Arbete/工作; Studier/学习; Ändrade familjeförhållanden/其他家属关系; Annan orsak/其他原因)\r\n\r\nÖnskemål: 希望居住区域\r\n\r\nPrenumerera på lägenheter som passar dina önskemål: \r\n\r\n填Ja则表示同意每周电子邮件接收符合你条件的房屋信息<br />\r\n\r\n5) Uppgifter för studentlägenhet 学生房申请信息填写   *必填\r\n\r\nStockholm与Uppsala区域填写<br />\r\n\r\n6) Sammanfattning信息复查\r\n\r\n点Spara表示Save<br />\r\n\r\n7) 此时页面已登入，上方的Information标明你现在获得了四位数字密码pin-kode, 以后就可以用你的12位人口号+4位数字密码登入网站了。</p>\r\n<p>\r\n\r\n<strong>4-3-2-2. 排房子</strong><br />\r\n\r\n1) 登入后进入初始页面(Startsida) 你会看到Ledigt just nu enligt din profil 表示符合你需求的现有空房。以Uppsala学生房为例，点击 # lediga studentlägenheter i Uppsala 后的Visa进行查看， Adress/Område/Rum/Storlek/Hyra/Tillträde/Ant.anm分别表示地址/区域/房间数量/房屋平米数/月租/入住时间/\r\n<br />\r\n2) 点击感兴趣的房子后可看到其详细信息。若想要排这套房子，则点击右边的Anmäl intresse。(Byggår: 房屋修建年份，Sista anmälningsdatum: 最晚排房时间)\r\n\r\n之后在Intresseanmälningar里就可以查看自己目前在排的房子了。<br />\r\n\r\n3) 在Erbjudanden里可查看offer的房子。</p></p>\r\n </p> \r\n</p>\r\n<p> \r\n<h3>5，新生没有房子找谁？</h3>\r\n \r\n <p>\r\n<p>第一，直接找学院的秘书帮着解决，有的学院是秘书直接帮着安排，有的是需要问问，他们有时会帮忙有时不会。\r\n学院没有义务帮学生解决房屋问题，所以这点大家不用抱怨，自己赶紧想辙是要紧的。</p>\r\n \r\n<p>第二，生物系的学生普遍都会被学院安排在 Flogsta 的 Corridor Room 里，\r\n我建议没有房子的同学过来之前先和生物系的同学联系，实在不行大家合住一下解一下燃眉之急。\r\n不过要重点提一下，Flogsta Corridor Room 是不许两人合住的，大家如何短期住一下尚可，长此以往不太好。</p>\r\n \r\n<p>第三，如果学院没有帮着找，就问问我此文中提到的 Student Union，\r\n他们有时会帮忙，不过 master 留学生一般不是他们负责的范畴。</p>\r\n \r\n<p>第四，在 KINA 坛子里询问一下有没有老生能留下房子给新生，或者有没有 Apartment 的空房可以租用的。</p>\r\n \r\n<p>第五，在暑期 Uppsala 学联会安排接机前同学们会通过电子邮件联系学联具体落地，\r\n如果届时还有同学没有找到房子的，务必告知学联会的接机同学，让他们当天临时安排一下，让有房的新生先帮助一下。</p>\r\n \r\n<p>第六，（感谢蔡延玲同学提供以下租房信息。）\r\n<a href="http://www.uppsalastudentkar.nu/bostadsjour">http://www.uppsalastudentkar.nu/bostadsjour</a> 是个人发的租房信息，可能是转租，要注意有的房子要求了搬出时间。</p>\r\n \r\n<p>还有一个房子特少还特贵的，优点是不需要累计天数。<a href="http://www.upplandsknekten.se">http://www.upplandsknekten.se</a> 地址在MIC 后面的河边。\r\n有的教授可以帮要来的学生联系那里，房租就很便宜。但是贴在网上的就很贵了。\r\n点att söka bostad 点lediga objekt看有没有新发布的房间，点 Intresseanmälan填表排房。公司会按照填表的顺序，填得越早越优先，公司会接待你去看房，然后决定要不要。\r\n<br />TIPS: 发布新房间信息一般是在每个月第三或四周的周一，9点多。所以要尽快填表。</p>\r\n\r\n</p></p>\r\n<p>\r\n<h3>6，写在最后，欢迎所有同学，祝你们在 Uppsala 顺利找到自己温暖的小窝！</h3>\r\n</p>\r\n \r\n <p>\r\n【The End，转载请注明作者--胡劲之（芝麻烧饼，Merlin250）】</p>\r\n<p>转自 <a href="http://merlin250.spaces.live.com/blog/cns!48962ECAF0B29852!1679.entry?sa=498701378">http://merlin250.spaces.live.com/blog/cns!48962ECAF0B29852!1679.entry?sa=498701378</a></p>\r\n'),
(3, 3, '<h2>隆德(Lund) 找房贴士</h2>\r\n<p>即将要来Lund读书的各位，想必或多或少知道一些这边的住房情况，就说说我的了解，给大家提供一个方便。</p>\r\n\r\n\r\n\r\n<p>首先解释一下所为的一手房和二手房，概念和国内的不太一样。一手房是指和房产公司直接租（不是国内的房屋中介），二手房是和房东租。房东有可能拥有该房子的产权或者使用权，或者他也是从房屋公司租来的。</p>\r\n\r\n<p>\r\n\r\n\r\n<h3>1-A 一手房</h3>\r\n<p>一手房是最佳选择，优点如下：价格便宜（二手比一手贵20％左右），稳定（你不走没人赶你走）。缺点是不带家具（但厨房的橱柜和炉灶都是有的，卫生间一般也有水盆和镜子，马桶等），所以需要自己购买家具。一手房不容易租到！房屋公司在出租房子的时候需要人口号，工资证明等等……而且可能有其它人和你竞争。\r\n\r\n\r\n<p><a href="http://bovision.com/Default.aspx">Bovision</a><br />\r\n这个网站有英文版本，比较方便。选择国家，城市后，选择房屋类型，Tenancy rights是租房子，而Tenancy－ower flats是买房子。所以选择前者，点击Show Results，括号中的是符合条件的房子数量。之后会显示符合条件的房子列表。点击相应条目，再点击show more info，会出来详细情况...\r\n</p>\r\n\r\n\r\n<p><a href="http://www.mkbfastighet.se/">MKB</a><br />\r\n属于排队性质，先登记，然后他们会根据你的要求给你发相关的信息。但人太多，而且不知道这个公司的选择标准，我已经排了8个月都没有一点消息。据说如果在公司有内线的话很快就能租到，咱没有，也就干耗着了。这个网站也是瑞典语的。登记的时候需要人口号。</p>\r\n\r\n<p><a >IHO</a><br />\r\n这是专门给国际学生提供住房服务的机构。当你被录取后，你会收到IHO发给你的邮件，应该会链接到学校网页上，如果想省事的话，可以要求IHO给解决房子问题。缺点是贵，不仅每月房租高，而且还有一个手续费或者叫做中介费，去年是600kr。而且也不一定就百分之百有房子。还有一个误区就是“先拿IHO的房子住两个月再说”，如果IHO通知你有你的房子，那么在报道当天拿钥匙的时候要签合同。一旦你签了合同，你就必须住一个学期，大概是4个月左右。报到当天和次日，如果你不满意，可以退掉房子，但是600kr中介费不退。错过了这两天，之后如果你不满意，你只能要求IHO给你换房子，或者有人愿意住你的房子，否则你就要住一个学期了。IHO换房子要交500kr。IHO的房子都有家具。</p>\r\n\r\n<p><a href="www.afb.se">AFB</a><br />\r\nAFB 是一个给所有学生提供住房的机构，比IHO便宜的多，需要排队，以注册天数长短来统计，拿到房子后累计天数清零，重新计算。AFB在每学期开学时有新生房。只要出具新生证明就可以。新生房都是corridor。只是新生房可能没那么容易排，僧多粥少。AFB的corridor有的有家具。 Apartment没家具。</p>\r\n\r\n</p>\r\n</p>\r\n\r\n\r\n\r\n\r\n<p>\r\n<h3>1-B 二手房</h3>\r\n<p>二手房相对容易找到，缺点有：价格一般较高，不稳定（房东可以随时让你走人，一般是半年到一年租期）。优点是一般都带家具，不用自己购置。\r\n大家可以根据个人情况选择。我的建议是新到租二手的，情况熟悉以后，尽量租一手的。长期呆的话还是一手合算方便。也可以考虑合租。\r\n\r\n\r\n<p><a href="http://www.blocket.se/">Blocket</a><br />\r\n进去以后选省份skane，然后选市malmo，然后在alla  kategorier里的bostad大项下面再选Lagenheter小项目\r\n之后下面会有一行选择房子类型的，选Uthyres，意思是出租（房东出租）</p>\r\n\r\n<p><a href="http://kina.cc">kina.cc</a><br />\r\n需注册才能看帖，是华人信息交流的综合论坛</p>\r\n\r\n\r\n<p><a href="http://www.bopoolen.se">Bopoolen</a><br />\r\n这上面都是出租房子的广告，有学生合租，也有当地人出租房子的。</p>\r\n\r\n</p>\r\n\r\n</p>');


-- 2013-06-11
ALTER TABLE `advertisement` CHANGE COLUMN `status` `status` ENUM('draft','active','deleted','closed','spam') NOT NULL DEFAULT 'active'  ;