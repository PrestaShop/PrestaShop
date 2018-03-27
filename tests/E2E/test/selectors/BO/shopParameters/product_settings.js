module.exports = {
  ProductSettings:{
    menu:'//*[@id="subtab-AdminPPreferences"]/a',
    disableOrderOutOfStock_button:'//*[@id="conf_id_PS_ORDER_OUT_OF_STOCK"]//label[contains(@for, "PS_ORDER_OUT_OF_STOCK_off")]',
    stockManagement_button:'//*[@id="PS_PACK_STOCK_TYPE"]',
    save_button:'//*[@id="configuration_fieldset_stock"]//button[contains(@name, "submitOptionsconfiguration")]',
  }
};
