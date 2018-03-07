module.exports = {
  PageCategory: {
    add_category_button: '//*[@id="page-header-desc-cms-new_cms_category"]',
    name_input: '//*[@id="name_1"]',
    enable_display_option: '//label[text()="Yes"]',
    parent_category_select: '//select[@name="id_parent"]',
    description_textarea: '//*[@id="description_1"]',
    meta_title_input: '//*[@id="meta_title_1"]',
    meta_description_input: '//*[@id="meta_description_1"]',
    meta_keywords_input: '//*[@id="meta_keywords_1"]',
    save_button:'//*[@id="cms_category_form_submit_btn"]',
    success_panel: '//*[@id="content"]/div[@class="bootstrap"]/div[contains(@class, "success")]',
  }
};