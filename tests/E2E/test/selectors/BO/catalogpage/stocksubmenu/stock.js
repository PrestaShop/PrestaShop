module.exports = {
  Stock: {
    product_quantity_input: '(//*[@id="app"]//div[contains(@class,"edit-qty")])[%O]/input',
    product_quantity: '//*[@id="app"]//tr[%O]/td[7]',
    product_quantity_modified: '(//*[@id="app"]//tr[%O]//span[contains(@class,"qty-update")])[1]',
    save_product_quantity_button: '(//*[@id="app"]//button[contains(@class,"check-button")])[1]',
    group_apply_button: '//*[@id="app"]//button[contains(@class,"update-qty")]',
    add_quantity_button: '(//*[@id="app"]//span[contains(@class,"ps-number-up")])[%ITEM]',
    remove_quantity_button: '(//*[@id="app"]//span[contains(@class,"ps-number-down")])[%ITEM]',
    success_panel: '//*[@id="growls"]',
    search_input: '(//*[@id="search"]//input[contains(@class,"input")])[1]',
    search_button: '//*[@id="search"]//button[contains(@class,"search-button")]',
    sort_product_icon: '//*[@id="app"]//table//div[contains(@data-sort-direction,"asc")]',
    check_sign: '//*[@id="app"]//button[@class="check-button"]',
    physical_column: '//*[@id="app"]//div//table[@class="table"]//tr[%ID]//td[5]',
    green_validation: '//*[@id="search"]/div[2]/div/button',
    product_column: '//*[@id="app"]//div/table[@class="table"]//tr[%O]/td[1]',
    available_column: '//*[@id="app"]//div//table[@class="table"]//tr[%ID]/td[7]',
    reference_product_column: '//*[@id="app"]//div/table[@class="table"]//tr[%O]/td[2]',
    employee_column: '//*[@id="app"]//div/table[@class="table"]//tr[%O]/td[6]',
    product_selector: '//*[@id="app"]//table//tr//p[contains(text(),"%ProductName")]',
    success_hidden_panel: '//*[@id="search"]//div[contains(@class,"alert-box")]//div[contains(@class,"alert-success")]/p/span'
  }
};
