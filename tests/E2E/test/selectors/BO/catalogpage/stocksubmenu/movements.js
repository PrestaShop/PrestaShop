module.exports = {
  Movement:{
    variation: '(//*[@id="app"]//span[contains(@class,"qty-number")]/span)[1]',
    variation_value: '(//*[@id="app"]//span[contains(@class,"qty-number")]/span)[%P]',
    quantity_value: '(//*[@id="app"]//span[contains(@class,"qty-number")])[%P]',
    type_value: '//*[@id="app"]//tr[%P]/td[3]',
    time_movement: '//*[@id="app"]//tr[%P]/td[5]',
    sort_data_time_icon: '//*[@id="app"]//th[5]/div[@data-sort-col-name="id_product"]/span[@role="button"]'
  }
};



