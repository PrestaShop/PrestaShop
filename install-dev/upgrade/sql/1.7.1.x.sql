UPDATE `PREFIX_address_format` SET `format` = 'firstname lastname
company
vat_number
address1
address2
city
postcode
State:name
Country:name
phone' WHERE `id_country` = (SELECT `id_country` FROM `PREFIX_country` WHERE `iso_code` = 'IN');

/* See data/xml/tab.xml structure */
UPDATE `PREFIX_tab_lang` SET `name` = "Équipe" WHERE `name` = "Employés"
  AND `id_tab` = (SELECT `id_tab` FROM `PREFIX_tab` WHERE `class_name` = "AdminParentEmployees")
  AND `id_lang` = (SELECT `id_lang` FROM `PREFIX_lang` WHERE `iso_code` = "fr");

UPDATE `PREFIX_tab_lang` SET `name` = "Thème & logo" WHERE `name` = "Thèmes"
  AND `id_tab` = (SELECT `id_tab` FROM `PREFIX_tab` WHERE `class_name` = "AdminThemes")
  AND `id_lang` = (SELECT `id_lang` FROM `PREFIX_lang` WHERE `iso_code` = "fr");

UPDATE `PREFIX_tab_lang` SET `name` = "Trafic & SEO" WHERE `name` = "Trafic"
  AND `id_tab` = (SELECT `id_tab` FROM `PREFIX_tab` WHERE `class_name` = "AdminParentMeta")
  AND `id_lang` = (SELECT `id_lang` FROM `PREFIX_lang` WHERE `iso_code` = "fr");
