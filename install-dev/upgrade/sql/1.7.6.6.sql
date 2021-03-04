SET SESSION sql_mode='';
SET NAMES 'utf8';

CREATE TABLE `PREFIX_employee_session` (
  `id_employee_session` int(11) unsigned NOT NULL auto_increment,
  `id_employee` int(10) unsigned DEFAULT NULL,
  `token` varchar(40) DEFAULT NULL,
  PRIMARY KEY `id_employee_session` (`id_employee_session`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE `PREFIX_customer_session` (
  `id_customer_session` int(11) unsigned NOT NULL auto_increment,
  `id_customer` int(10) unsigned DEFAULT NULL,
  `token` varchar(40) DEFAULT NULL,
  PRIMARY KEY `id_customer_session` (`id_customer_session`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;
