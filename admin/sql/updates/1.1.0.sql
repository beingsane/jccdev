ALTER TABLE `#__jccdev_fields` ADD `rule` VARCHAR( 50 ) NOT NULL AFTER `dbtype`;

CREATE TABLE IF NOT EXISTS `#__jccdev_formfields` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary key',
  `name` varchar(40) NOT NULL,
  `source` text NOT NULL,
  `params` text NOT NULL COMMENT 'JSON encoded params',
  PRIMARY KEY (`id`)
)
CHARACTER SET utf8
COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__jccdev_formrules` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary key',
  `name` varchar(40) NOT NULL,
  `source` text NOT NULL,
  `params` text NOT NULL COMMENT 'JSON encoded params',
  PRIMARY KEY (`id`)
)
CHARACTER SET utf8
COLLATE utf8_general_ci;