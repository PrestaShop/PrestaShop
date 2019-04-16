module.exports = {
  Movement: {
    variation: '#app section table tr > td:nth-child(4) span > span',
    variation_value: '#app section table tr:nth-child(%P) > td:nth-child(4) span > span',
    quantity_value: '#app section table tr:nth-child(%P) > td:nth-child(4) > span',
    type_value: '#app tr:nth-of-type(%P) > td:nth-of-type(3)',
    type_value_href: '#app tr:nth-of-type(%P) > td:nth-of-type(3) a',
    reference_value: '#app tr:nth-of-type(%P) > td:nth-of-type(2)',
    time_movement: '#app tr:nth-of-type(%P) > td:nth-of-type(5)',
    sort_data_time_icon: '#app table th:nth-of-type(5) div[data-sort-direction] span.ps-sort',
    employee_value: '#app tr:nth-of-type(%P) > td:nth-of-type(6)',
    sort_data_time_icon_desc: '#app table th:nth-of-type(5) div[data-sort-direction*="desc"]',
    sort_data_time_icon_asc: '#app table th:nth-of-type(5) div[data-sort-direction*="asc"]',
    product_value: '#app tr:nth-of-type(%P) > td:nth-of-type(1) p',
    search_input: '#search div[class*="search-input search"] input',
    search_button: '#search button[class*="search-button"]',
    advanced_filters_button: '#filters-container > button[data-target="#filters"]',
    movement_type_select: '#filters div > select',
    searched_product_close_icon: '#search div[class*="tags-input"] span.tag > i'
  }
};



