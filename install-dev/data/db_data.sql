SET
  SESSION sql_mode='';
SET
  NAMES 'utf8mb4';

INSERT INTO `PREFIX_feature_flag` (`name`, `state`, `label_wording`, `label_domain`, `description_wording`, `description_domain`)
VALUES
	('product_page_v2', 0, 'Experimental product page', 'Admin.Advparameters.Feature', 'This page benefits from increased performance and includes new features such as a new combination management system. Please note this is a work in progress and some features are not available yet.', 'Admin.Advparameters.Help');
