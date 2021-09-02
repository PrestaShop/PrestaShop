SET SESSION sql_mode='';
SET NAMES 'utf8mb4';

INSERT INTO `PREFIX_product_supplier` (id_product, id_supplier, product_supplier_reference, product_supplier_price_te, id_currency)
SELECT ps.id_product, ps.id_supplier, ps.product_supplier_reference, ps.product_supplier_price_te, ps.id_currency
FROM `PREFIX_product_supplier` as ps
ON DUPLICATE KEY UPDATE `PREFIX_product_supplier`.id_product = ps.id_product;
