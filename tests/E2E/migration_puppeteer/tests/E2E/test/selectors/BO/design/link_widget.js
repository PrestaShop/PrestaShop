module.exports = {
  LinkWidget: {
    new_block_button: ' a [contains(text(),"New block"]',
    name_of_the_link_block_input: '#name_1',
    hook_select: '#id_hook',
    select_all_content_page: ' input[contains(onclick,"cms"]',
    select_all_product_page: ' input[contains(onclick,"product"]',
    select_all_static_content: ' input[contains(onclick,"static"]',
    first_custom_content_name_input: ' table.js-custom-links-table-1 input[contains(name,"custom:nth-child(1):nth-child(1)[title]")]',
    first_custom_content_url_input: ' table.js-custom-links-table-1 input[contains(name,"custom:nth-child(1):nth-child(1)[url]")]',
    add_custom_content_button: ' a .js-add-custom-link-1',
    second_custom_content_name_input: ' table.js-custom-links-table-1 input[contains(name,"custom:nth-child(1):nth-child(2)[title]")]',
    second_custom_content_url_input: ' table.js-custom-links-table-1 input[contains(name,"custom:nth-child(1):nth-child(2)[url]")]',
    save_button: '#configuration_form_submit_btn',
    link_widget_table: ' div[contains(text(),"%HOOK")] following-sibling::table tbody tr:nth-child(2)',
    link_widget_configuration_bloc: '#configuration_form h3',
    hook_in_table: ' div[contains(text(),"%HOOK")] following-sibling::table tbody tr:nth-child(2)',
    last_widget_name_block: ' div[normalize-space(text())="%HOOK"] following-sibling::table tbody tr[last()] td:nth-child(3)',
    last_widget_drag_in_displayFooter_block: ' div[contains(text(),"%HOOK")] following-sibling::table tbody tr[last()] td:nth-child(2) div',
    first_widget_drag_in_displayFooter_block: ' div[contains(text(),"%HOOK")] following-sibling::table tbody tr:nth-child(1)',
    second_widget_in_displayFooter_block: ' div[contains(text(),"%HOOK")] following-sibling::table tbody tr:nth-child(2) td:nth-child(3)',
    edit_display_footer_created_hook: ' div[contains(text(),"displayFooter")] following-sibling::table tbody tr[last()] td:nth-child(4) a',
    delete_display_footer_created_hook: ' div[contains(text(),"displayLeftColumn"] following-sibling::table tbody tr[last()] td:nth-child(4) button',
    delete_button: ' div[contains(text(),"displayLeftColumn"] following-sibling::table tbody tr[last] td:nth-child(4) ul li a'
  }
};
