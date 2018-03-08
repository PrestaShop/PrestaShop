module.exports = {
  Category: {
    add_category_button: '//*[@id="page-header-desc-cms-new_cms_category"]',
    name_input: '//*[@id="name_1"]',
    enable_display_option: '//label[text()="Yes"]',
    parent_category_select: '//select[@name="id_parent"]',
    description_textarea: '//*[@id="description_1"]',
    meta_title_input: '//*[@id="meta_title_1"]',
    meta_description_input: '//*[@id="meta_description_1"]',
    meta_keywords_input: '//*[@id="meta_keywords_1"]',
    save_button: '//*[@id="cms_category_form_submit_btn"]',
    success_panel: '//*[@id="content"]/div[@class="bootstrap"]/div[contains(@class, "success")]',
    name_filter: '//*[@id="table-cms_category"]//input[@name="cms_categoryFilter_name"]',
    dropdown_toggle: '//*[@id="table-cms_category"]//button[@data-toggle="dropdown"]',
    edit_button: '//*[@id="table-cms_category"]//a[@title="Edit"]',
    search_name_result: '//*[@id="table-cms_category"]//td[%ID]',
    delete_button: '//*[@id="table-cms_category"]//a[@title="Delete"]',
    bulk_actions_button:'//*[@id="bulk_action_menu_cms_category"]',
    bulk_actions_select_all_button:'//*[@id="form-cms_category"]//div[contains(@class,"bulk-actions")]//ul[@class="dropdown-menu"]//a[contains(@onclick, "true")]',
    bulk_actions_delete_button:'//*[@id="form-cms_category"]//div[contains(@class,"bulk-actions")]//ul[@class="dropdown-menu"]//a[contains(@onclick, "submitBulkdeletecms_category")]'
  }
};