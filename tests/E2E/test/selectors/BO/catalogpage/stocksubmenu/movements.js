module.exports = {
  Movement: {
    variation: '(//*[@id="app"]//span[contains(@class,"qty-number")]/span)[1]',
    variation_value: '(//*[@id="app"]//span[contains(@class,"qty-number")]/span)[%P]',
    quantity_value: '(//*[@id="app"]//span[contains(@class,"qty-number")])[%P]',
    type_value: '//*[@id="app"]//tr[%P]/td[3]',
    reference_value: '//*[@id="app"]//tr[%P]/td[2]',
    time_movement: '//*[@id="app"]//tr[%P]/td[5]',
    sort_data_time_icon: '//*[@id="app"]//table//th[5]//div[contains(@data-sort-direction,"asc")]',
    employee_value: '//*[@id="app"]//tr[%P]/td[6]',
    sort_data_time_icon_desc: '//*[@id="app"]//table//th[5]//div[contains(@data-sort-direction,"desc")]',
    sort_data_time_icon_asc: '//*[@id="app"]//table//th[5]//div[contains(@data-sort-direction,"asc")]',
    product_value: '//*[@id="app"]//tr[%P]/td[1]//p',
    search_input: '//*[@id="search"]//div[contains(@class,"search-input search")]//input',
    search_button: '//*[@id="search"]//button[contains(@class,"search-button")]',
    advanced_filters_button: '//*[@id="filters-container"]/button[@data-target="#filters"]',
    movement_type_select: '//*[@id="filters"]//div/select',
    searched_product_close_icon: '//*[@id="search"]//div[contains(@class,"tags-input")]//span[@class="tag"]/i'
  }
};



