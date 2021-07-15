CREATE TABLE `ps_memcached` (
  `table_name` varchar(255) NOT NULL,
  `expiry` int NOT NULL,
  PRIMARY KEY (`table_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
