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
