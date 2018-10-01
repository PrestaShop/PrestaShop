module.exports = {
  Localization: {
    Localization: {
      pack_select: '//*[@id="import_localization_pack_iso_localization_pack"]',
      pack_search_input: '//*[@id="import_localization_pack_iso_localization_pack"]//div[@class="chosen-search"]//input',
      pack_option: '//*[@id="import_localization_pack_iso_localization_pack"]//ul[@class="chosen-results"]//li',
      import_button: '//*[@name="import_localization_pack"]//div[@class="card-footer"]//button',
      local_unit_input: '//*[@id="form_local_units_%D_unit"]',
      save_local_units_button: '(//*[@id="main-div"]//button[text()="Save"])[2]',
      success_alert_panel: '//*[@id="main-div"]//div[contains(@class, "%B") and @role="alert"]/div[@class="alert-text"]',
    },
    languages: {
      add_new_language_button: '//*[@id="page-header-desc-lang-new_language"]',
      name_input: '//*[@id="name"]',
      iso_code_input: '//*[@id="iso_code"]',
      language_code_input: '//*[@id="language_code"]',
      date_format_input: '//*[@id="date_format_lite"]',
      date_format_full_input: '//*[@id="date_format_full"]',
      flag_file: '//*[@id="flag"]',
      no_picture_file: '//*[@id="no_picture"]',
      is_rtl_button: '//*[@id="fieldset_0"]//label[@for="is_rtl_%S"]',
      status_button: '//*[@id="fieldset_0"]//label[@for="active_%S"]',
      save_button: '//*[@id="lang_form_submit_btn"]',
      success_alert: '(//div[contains(@class,"alert-success")])[1]',
      filter_name_input: '//*[@id="table-lang"]//input[@name="langFilter_name"]',
      filter_search_button: '//*[@id="submitFilterButtonlang"]',
      edit_button: '//*[@id="table-lang"]/tbody//a[@title="Edit"]',
      dropdown_button: '//*[@id="table-lang"]/tbody//button[@data-toggle="dropdown"]',
      delete_button: '//*[@id="table-lang"]/tbody//a[@title="Delete"]',
      reset_button: '//*[@id="table-lang"]//button[contains(@name, "Reset")]'
    }
  }
};
