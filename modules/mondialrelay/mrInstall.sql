CREATE TABLE IF NOT EXISTS `PREFIX_mr_method` (
	`id_mr_method` int(10) unsigned NOT NULL auto_increment,
	`mr_Name` varchar(255)  NOT NULL,
	`mr_Pays_list` varchar(1000)  NOT NULL,
	`mr_ModeCol` varchar(3)  NOT NULL,
	`mr_ModeLiv` varchar(3)  NOT NULL,
	`mr_ModeAss` varchar(3)  NOT NULL DEFAULT '0',
	`id_carrier` int(10) NOT NULL,
	PRIMARY KEY  (`id_mr_method`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `PREFIX_mr_selected` (
    `id_mr_selected` int(10) unsigned NOT NULL auto_increment,
	`id_customer` int(10) unsigned NULL,
	`id_method` int(10) unsigned NULL,
	`id_cart` int(10) unsigned NULL,
	`id_order` int(10) unsigned NULL,	
	`MR_poids` varchar(7)  NULL,
	`MR_Selected_Num` varchar(6)  NULL,	
	`MR_Selected_LgAdr1` varchar(36) NULL,
	`MR_Selected_LgAdr2` varchar(36) NULL,
	`MR_Selected_LgAdr3` varchar(36) NULL,
	`MR_Selected_LgAdr4` varchar(36) NULL,
	`MR_Selected_CP` varchar(10) NULL,
	`MR_Selected_Ville` varchar(32) NULL,
	`MR_Selected_Pays` varchar(2) NULL,	
	`url_suivi` varchar(1000) NULL,	
	`url_etiquette` varchar(1000) NULL,	
	`exp_number` varchar(8) NULL,
    `date_add` datetime NOT NULL,
    `date_upd` datetime NOT NULL,
	PRIMARY KEY  (`id_mr_selected`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `PREFIX_mr_historique` (
`id` int(10) unsigned NOT NULL auto_increment,
`order` TEXT NOT NULL ,
`exp` TEXT NOT NULL ,
`url_a4` VARCHAR( 1000 ) NOT NULL ,
`url_a5` VARCHAR( 1000 ) NOT NULL ,
PRIMARY KEY ( `id` )
)ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

