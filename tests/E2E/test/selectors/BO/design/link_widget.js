module.exports = {
  LinkWidget: {
    new_block_button: '//a[contains(text(),"New block")]',
    name_of_the_link_block_input: '//*[@id="name_1"]',
    hook_select: '//*[@id="id_hook"]',
    select_all_content_page: '//input[contains(@onclick,"cms")]',
    select_all_product_page: '//input[contains(@onclick,"product")]',
    select_all_static_content: '//input[contains(@onclick,"static")]',
    first_custom_content_name_input: '//table[contains(@class,"js-custom-links-table-1")]//input[contains(@name,"custom[1][1][title]")]',
    first_custom_content_url_input: '//table[contains(@class,"js-custom-links-table-1")]//input[contains(@name,"custom[1][1][url]")]',
    add_custom_content_button: '//a[contains(@class,"js-add-custom-link-1")]',
    second_custom_content_name_input: '//table[contains(@class,"js-custom-links-table-1")]//input[contains(@name,"custom[1][2][title]")]',
    second_custom_content_url_input: '//table[contains(@class,"js-custom-links-table-1")]//input[contains(@name,"custom[1][2][url]")]',
    save_button: '//*[@id="configuration_form_submit_btn"]',
    link_widget_table: '//div[contains(text(),"%HOOK")]/following-sibling::table/tbody/tr[2]',
    link_widget_configuration_bloc: '//*[@id="configuration_form"]//h3',

    second_element_hook_in_table:'//div[contains(text(),"%HOOK")]/following-sibling::table/tbody/tr[2]',

    last_widget_name_in_displayFooter_block: '//div[contains(text(),"%HOOK")]/following-sibling::table/tbody/tr[last()]//td[3]',
    last_widget_drag_in_displayFooter_block: '//div[contains(text(),"%HOOK")]/following-sibling::table/tbody/tr[last()]//td[2]/div',
    first_widget_drag_in_displayFooter_block: '//div[contains(text(),"%HOOK")]/following-sibling::table/tbody/tr[1]',





    second_widget_in_displayFooter_block: '//div[contains(text(),"%HOOK")]/following-sibling::table/tbody/tr[2]//td[3]'
  }
};

