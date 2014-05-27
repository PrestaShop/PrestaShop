SET NAMES 'utf8';

INSERT INTO `PREFIX_hook` (`id_hook` , `name` , `title` , `description` , `position` , `live_edit`)
VALUES (NULL , 'displayAdminOrderTabOrder', 'Display new elements in Back Office on AdminOrder, panel Order', 'This hook launches modules when the AdminOrder tab is displayed in the Back Office and extends / override Order panel tabs', '1', '0'),
(NULL , 'displayAdminOrderContentOrder', 'Display new elements in Back Office on AdminOrder, panel Order', 'This hook launches modules when the AdminOrder tab is displayed in the Back Office and extends / override Order panel content', '1', '0'),
(NULL , 'displayAdminOrderTabShip', 'Display new elements in Back Office, AdminOrder, panel Shipping', 'This hook launches modules when the AdminOrder tab is displayed in the Back Office and extends / override Shipping panel tabs', '1', '0'),
(NULL , 'displayAdminOrderContentShip', 'Display new elements in Back Office, AdminOrder, panel Shipping', 'This hook launches modules when the AdminOrder tab is displayed in the Back Office and extends / override Shipping panel content', '1', '0');
