module.exports = Object.assign(
  {
    CatalogPage: {
      menu_button: '//*[@id="subtab-AdminCatalog"]/a',
      success_panel: '//*[@id="content"]/div[@class="bootstrap"]/div[contains(@class, "success")]',
      select_all_product_button: '//*[@id="bulk_action_select_all"]',
      action_group_button: '//*[@id="product_bulk_menu"]',
      disable_all_selected: '//*[@id="main-div"]/div[3]/div/div/div[2]/div/div[2]/div[2]/div/div/a[2]',
      enable_all_selected: '//*[@id="main-div"]/div[3]/div/div/div[2]/div/div[2]/div[2]/div/div/a[1]',
      succes_panel_all_item: '//*[@id="main-div"]/div[3]/div/div/div[2]',
      succes_panel_all_item_message: '//*[@id="main-div"]/div[3]/div[2]/div/div[2]/div/p',
      etat_first_product: '//*[@id="product_catalog_list"]/div[2]/div/table/tbody/tr[1]/td[8]/a/i',
      etat_last_product: '//*[@id="product_catalog_list"]/div[2]/div/table/tbody/tr[1]/td[8]/a/i',
      duplicate_button: '//*[@id="main-div"]/div[3]/div/div/div[2]/div/div[2]/div[2]/div/div/a[3]'
    }
  },
  require('./feature_submenu'),
  require('./category_submenu'),
  require('./attribute_submenu'),
  require('./Manufacturers'),
  require('./stocksubmenu')
);
