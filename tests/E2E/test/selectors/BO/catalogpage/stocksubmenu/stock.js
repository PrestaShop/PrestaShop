module.exports = {
  Stock: {
    product_quantity_input: '(//*[@id="app"]//div[contains(@class,"edit-qty")])[%O]/input',
    product_quantity: '//*[@id="app"]//tr[%O]/td[7]',
    product_quantity_modified: '(//*[@id="app"]//tr[%O]//span[contains(@class,"qty-update")])[1]',
    save_product_quantity_button: '(//*[@id="app"]//button[contains(@class,"check-button")])[1]',
    group_apply_button: '//*[@id="app"]//button[contains(@class,"update-qty")]',
    add_quantity_button: '(//*[@id="app"]//span[contains(@class,"ps-number-up")])[1]',
    remove_quantity_button: '(//*[@id="app"]//span[contains(@class,"ps-number-down")])[1]',
    success_panel: '//*[@id="growls"]',
    search_input:'(//*[@id="search"]//input[contains(@class,"input")])[1]',
    search_button:'//*[@id="search"]//button[contains(@class,"search-button")]',
    sort_product_icon: '//*[@id="app"]//div[@data-sort-col-name="id_product" and @data-sort-direction="asc"]/span[@role="button"]'
  }
};