<?php

function admin_stores_tab()
{
    if (!Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'tab` WHERE `class_name` = \'AdminStores\''))
    {
        Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'tab` (`class_name`, `id_parent`, `position`) VALUES (\'AdminStores\', 0, 11)');

        Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'tab_lang` (`id_lang`, `id_tab`, `name`)
            VALUES
            (1, (SELECT `id_tab` FROM `'._DB_PREFIX_.'tab` WHERE `class_name` = \'AdminStores\'), \'Stores\'),
            (2, (SELECT `id_tab` FROM `'._DB_PREFIX_.'tab` WHERE `class_name` = \'AdminStores\'), \'Magasins\'),
            (3, (SELECT `id_tab` FROM `'._DB_PREFIX_.'tab` WHERE `class_name` = \'AdminStores\'), \'Tiendas\')');


        Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'access` (`id_profile`, `id_tab`, `view`, `add`, `edit`, `delete`) (
        	SELECT `id_profile`, (SELECT `id_tab` FROM `'._DB_PREFIX_.'tab` WHERE `class_name` = \'AdminStores\' LIMIT 1), 1, 1, 1, 1 FROM `'._DB_PREFIX_.'profile`
          )');
   }
}

