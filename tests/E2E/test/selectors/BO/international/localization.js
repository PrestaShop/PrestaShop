module.exports = {
  Localization: {
    Localization: {
      pack_select: '//*[@id="iso_localization_pack_chosen"]',
      pack_search_input: '//*[@id="iso_localization_pack_chosen"]//div[@class="chosen-search"]//input',
      pack_option: '//*[@id="iso_localization_pack_chosen"]//ul[@class="chosen-results"]//li',
      import_button: '//*[@id="configuration_form_submit_btn_1"]'
    },
    languages: {
      add_new_language_button: '//*[@id="page-header-desc-lang-new_language"]',
      name_input: '//*[@id="name"]',
      iso_code_input: '//*[@id="iso_code"]',
      language_code_input: '//*[@id="language_code"]',
      flag_file: '//*[@id="flag"]',
      save_button: '//*[@id="lang_form_submit_btn"]',
      success_alert: '(//div[contains(@class,"alert-success")])[1]',
      filter_name_input: '//*[@id="table-lang"]//input[@name="langFilter_name"]',
      filter_search_button: '//*[@id="submitFilterButtonlang"]',
      edit_button: '//*[@id="table-lang"]/tbody//a[@title="Edit"]',
      dropdown_button: '//*[@id="table-lang"]/tbody//button[@data-toggle="dropdown"]',
      delete_button: '//*[@id="table-lang"]/tbody//a[@title="Delete"]'
    }
  }
};