module.exports = {
  Brands:{
    new_brand_button: '//*[@id="page-header-desc-address-new_manufacturer"]',
    name_input: '//*[@id="name"]',
    image_input: '//*[@id="logo"]',
    meta_title_input: '//*[@id="meta_title_1"]',
    meta_description_input: '//*[@id="meta_description_1"]',
    meta_keywords_input:'//*[@id="manufacturer_form"]//div[contains(@class,"lang-1")]//div[@class="tagify-container"]/input',
    active_button: '//*[@id="fieldset_0"]//label[@for="active_on"]',
    save_button: '//*[@id="manufacturer_form_submit_btn"]',
    short_description_input: '(//*[@id="manufacturer_form"]//div[@class="mce-tinymce mce-container mce-panel"])[1]',
    description_input: '(//*[@id="manufacturer_form"]//div[@class="mce-tinymce mce-container mce-panel"])[3]',
    filter_name_input: '//*[@id="table-manufacturer"]//input[@name="manufacturerFilter_name"]',
    brand_column: '//*[@id="table-manufacturer"]//tr[%TR]/td[%COL]',
    brand_search_button: '//*[@id="submitFilterButtonmanufacturer"]',
    brand_reset_button: '//*[@id="submitFilterButtonmanufacturer"]',
  }
};
