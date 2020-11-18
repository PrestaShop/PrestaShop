module.exports = {
  ProductSettings:{
    menu:'//*[@id="subtab-AdminPPreferences"]/a',
    disableOrderOutOfStock_button:'//label[contains(@for, "form_stock_allow_ordering_oos_0")]',
    stockManagement_button:'//*[@id="form_stock_pack_stock_management"]',
    save_button:'(//button[text()="Save"])[%POS]',
    Pagination: {
      products_per_page_input: '//*[@id="form_pagination_products_per_page"]'
    }
  }
};
