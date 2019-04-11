module.exports = {
  Stock: {
    edit_quantity_input: '#app tr:nth-of-type(1) div[name="qty"] input',
    product_quantity_input: '#app div.edit-qty:nth-child(%O) input',
    product_quantity: '#app tr:nth-of-type(%O) > td:nth-of-type(7)',
    product_quantity_modified: '#app tr:nth-child(%O) span.qty-update:nth-child(1)',
    available_quantity_modified: '#app tr:nth-child(%O) span.qty-update:nth-child(2)',
    save_product_quantity_button: '#app button.check-button:nth-child(%I)',
    group_apply_button: '#app button.update-qty',
    add_quantity_button: '#app span.ps-number-up:nth-child(%ITEM)',
    remove_quantity_button: '#app span.ps-number-down:nth-child(%ITEM)',
    success_panel: '#growls ',
    search_input: '#search form div.search-input.search input',
    search_button: '#search button[class*=search-button]',
    sort_product_icon: '#app table div[contains(data-sort-direction,"asc")]',
    check_sign: '#app button.check-button ',
    physical_column: '#app div > table.table tr:nth-of-type(%ID) > td:nth-of-type(5)',
    green_validation: '#search div:nth-child(2) div button',
    product_column: '#app div > table.table tr:nth-of-type(%O) > td:nth-of-type(1)',
    available_column: '#app div table.table tr:nth-of-type(%ID) > td:nth-of-type(7)',
    reference_product_column: '#app div > table.table tr:nth-of-type(%O) > td:nth-of-type(2)',
    employee_column: '#app div > table.table tr:nth-of-type(%O) > td:nth-of-type(6)',
    product_selector: '#app table tr p[contains(text(),"%ProductName")]',
    success_hidden_panel: '#search div.alert-box div.alert-success p span'
  }
};
