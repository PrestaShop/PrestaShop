SET NAMES 'utf8';

UPDATE `PREFIX_image_shop` ish, `PREFIX_image` i SET ish.id_product = i.id_product WHERE i.id_image=ish.id_image;