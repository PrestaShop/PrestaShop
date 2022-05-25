SET
  SESSION sql_mode='';
SET
  NAMES 'utf8mb4';

INSERT INTO `PREFIX_feature_flag` (`name`, `state`, `label_wording`, `label_domain`, `description_wording`, `description_domain`, `is_beta`)
VALUES
	('product_page_v2', 0, 'New product page - Single store', 'Admin.Advparameters.Feature', 'This page benefits from increased performance and includes new features such as a new combination management system.', 'Admin.Advparameters.Help', 0),
	('product_page_v2_beta', 0, 'New product page - Multi store', 'Admin.Advparameters.Feature', 'Access the new product page, even in a multistore context. This is a work in progress and some features are not available.', 'Admin.Advparameters.Help', 1);
