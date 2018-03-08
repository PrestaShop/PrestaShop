module.exports = {
  Pages: {
    Common: {
      name_input: '//*[@id="name_1"]',
      meta_description_input: '//*[@id="meta_description_1"]',
      enable_display_option: '//label[@for="active_on"]',
      success_panel: '//*[@id="content"]/div[@class="bootstrap"]/div[contains(@class, "success")]'
    },
    Category: {
      add_category_button: '//*[@id="page-header-desc-cms-new_cms_category"]',
      parent_category_select: '//select[@name="id_parent"]',
      description_textarea: '//*[@id="description_1"]',
      meta_title_input: '//*[@id="meta_title_1"]',
      meta_keywords_input: '//*[@id="meta_keywords_1"]',
      save_button: '//*[@id="cms_category_form_submit_btn"]',
      name_filter: '//*[@id="table-cms_category"]//input[@name="cms_categoryFilter_name"]',
      dropdown_toggle: '//*[@id="table-cms_category"]//button[@data-toggle="dropdown"]',
      edit_button: '//*[@id="table-cms_category"]//a[@title="Edit"]',
      search_name_result: '//*[@id="table-cms_category"]//td[%ID]',
      delete_button: '//*[@id="table-cms_category"]//a[@title="Delete"]',
      bulk_actions_button: '//*[@id="bulk_action_menu_cms_category"]',
      bulk_actions_select_all_button: '//*[@id="form-cms_category"]//div[contains(@class,"bulk-actions")]//ul[@class="dropdown-menu"]//a[contains(@onclick, "true")]',
      bulk_actions_delete_button: '//*[@id="form-cms_category"]//div[contains(@class,"bulk-actions")]//ul[@class="dropdown-menu"]//a[contains(@onclick, "submitBulkdeletecms_category")]'
    },
    Page: {
      add_new_page_button: '//*[@id="page-header-desc-cms-new_cms_page"]',
      meta_keywords_input: '//div[@class="tagify-container"]//input',
      enable_indexation_option: '//label[@for="indexation_on"]',
      save_button: '//*[@id="cms_form_submit_btn"]',
      url_filter_input: '//input[@name="cmsFilter_link_rewrite"]',
      search_url_result: '//*[@id="table-cms"]//td[%ID]'
    }
  }
};