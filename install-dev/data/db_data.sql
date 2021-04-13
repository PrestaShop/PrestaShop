SET
  SESSION sql_mode='';
SET
  NAMES 'utf8mb4';

INSERT INTO `PREFIX_feature_flag` (`name`, `state`, `label_wording`, `label_domain`, `description_wording`, `description_domain`)
VALUES
	('product_page_v2', 0, 'Experimental product page', 'Admin.Advparameters.Feature', 'This page is a work in progress. It includes new combination management features and other features under development (virtual products, packs, etc.)', 'Admin.Advparameters.Help');
