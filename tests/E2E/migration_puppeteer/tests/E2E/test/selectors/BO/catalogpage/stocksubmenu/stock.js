module.exports = {
  Stock: {
    edit_quantity_input: '#app tr:nth-of-type(1) div[name="qty"] input',
    product_quantity_input: '#app tr:nth-child(%O) div.edit-qty input',
    product_quantity: '#app tr:nth-of-type(%O) > td:nth-of-type(7)',
    product_quantity_modified: '#app tr:nth-child(%O) span.qty-update',
    available_quantity_modified: '#app tr:nth-child(%O) td:nth-child(7) span.qty-update',
    save_product_quantity_button: '#app tr:nth-child(%I) button.check-button',
    group_apply_button: '#app button.update-qty',
    add_quantity_button: '#app tr:nth-child(%ITEM) span.ps-number-up',
    remove_quantity_button: '#app tr:nth-child(%ITEM) span.ps-number-down',
    success_panel: '#growls ',
    search_input: '#search form div.search-input.search input',
    search_button: '#search button.search-button',
    sort_product_icon: '#app table div[data-sort-direction*="asc"]',
    check_sign: '#app button.check-button ',
    physical_column: '#app div > table.table tr:nth-of-type(%ID) > td:nth-of-type(5)',
    green_validation: '#search div:nth-child(2) div button',
    product_column: '#app div > table.table tr:nth-of-type(%O) > td:nth-of-type(1)',
    available_column: '#app div table.table tr:nth-of-type(%ID) > td:nth-of-type(7)',
    reference_product_column: '#app div > table.table tr:nth-of-type(%O) > td:nth-of-type(2)',
    employee_column: '#app div > table.table tr:nth-of-type(%O) > td:nth-of-type(6)',
    product_selector: '#app table tr p',
    success_hidden_panel: '#search div.alert-box div.alert-success p span'
  }
};