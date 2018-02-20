module.exports = {
  Movement:{
    tabs: '//*[@id="tab"]/li[2]/a',
    variation: '(//*[@id="app"]//span[contains(@class,"qty-number")]/span)[1]',
    variation_value: '(//*[@id="app"]//span[contains(@class,"qty-number")]/span)[%P]',
    quantity_value: '(//*[@id="app"]//span[contains(@class,"qty-number")])[%P]',
    type_value: '//*[@id="app"]//tr[%P]/td[3]',
    time_movement: '//*[@id="app"]//tr[%P]/td[5]'
  }
};



