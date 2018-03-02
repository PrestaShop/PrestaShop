module.exports = {
  Files: {
    add_new_file_button: '//*[@id="page-header-desc-attachment-new_attachment"]',
    filename_input: '//*[@id="name_1"]',
    description_textarea: '//*[@id="description_1"]',
    file: '//*[@id="file"]',
    save_button: '//*[@id="attachment_form_submit_btn"]',
    success_alert: '(//div[contains(@class,"alert-success")])[1]',
    filter_name_input: '//*[@id="table-attachment"]/thead//input[@name="attachmentFilter_name"]',
    filter_search_button: '//*[@id="submitFilterButtonattachment"]',
    edit_button: '//*[@id="table-attachment"]/tbody//a[@title="Edit"]',
    dropdown_button: '//*[@id="table-attachment"]/tbody//button[@data-toggle="dropdown"]',
    delete_button: '//*[@id="table-attachment"]/tbody//a[@title="Delete"]',
    bulk_action_button: '//*[@id="bulk_action_menu_attachment"]',
    action_group_button: '(//*[@id="form-attachment"]//div[contains(@class, "bulk-actions")]//a)[%ID]',
  }
};