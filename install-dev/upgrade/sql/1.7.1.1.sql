DELETE from `PREFIX_tab_lang` WHERE id_tab NOT IN (SELECT id_tab from `PREFIX_tab`);
