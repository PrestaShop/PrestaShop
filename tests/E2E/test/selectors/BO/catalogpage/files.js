module.exports = {
  Files: {
    add_new_file_button: '//*[@id="page-header-desc-attachment-new_attachment"]',
    filename_input: '//*[@id="name_1"]',
    description_textarea: '//*[@id="description_1"]',
    file: '//*[@id="file"]',
    save_button: '//*[@id="attachment_form_submit_btn"]',
    filter_name_input: '//*[@id="table-attachment"]/thead//input[@name="attachmentFilter_name"]',
    filter_size_input: '//*[@id="table-attachment"]/thead//input[@name="attachmentFilter_file_size"]',
    filter_associated_input: '//*[@id="table-attachment"]/thead//input[@name="attachmentFilter_virtual_product_attachment!products"]',
    filter_search_button: '//*[@id="submitFilterButtonattachment"]',
    filter_reset_button: '//*[@id="table-attachment"]/thead//button[@name="submitResetattachment"]',
    edit_button: '//*[@id="table-attachment"]/tbody//a[@title="Edit"]',
    dropdown_button: '//*[@id="table-attachment"]/tbody//button[@data-toggle="dropdown"]',
    action_button: '//*[@id="table-attachment"]/tbody//a[@title="%B"]',
    bulk_action_button: '//*[@id="bulk_action_menu_attachment"]',
    bulk_actions_select_all_button: '//*[@id="form-attachment"]//div[contains(@class,"bulk-actions")]//ul[@class="dropdown-menu"]//a[contains(@onclick, "true")]',
    bulk_actions_delete_button: '//*[@id="form-attachment"]//div[contains(@class,"bulk-actions")]//ul[@class="dropdown-menu"]//a[contains(@onclick, "submitBulkdeleteattachment")]',
    files_number: '//*[@id="form-attachment"]//span[@class="badge"]',
    files_table: '//*[@id="table-attachment"]/tbody/tr[%R]/td[%D]',
    sort_by_icon: '//*[@id="table-attachment"]/thead/tr[1]/th[%H]//i[contains(@class, "%BY")]',
    empty_list: '//*[@id="table-attachment"]/tbody//td[@class="list-empty"]'
  }
};