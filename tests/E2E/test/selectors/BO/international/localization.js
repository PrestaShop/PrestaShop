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
    },
    Currencies: {
      new_currency_button: '//*[@id="page-header-desc-currency-new_currency"]',
      currency_select: '//*[@id="iso_code"]',
      exchange_rate_input: '//*[@id="conversion_rate"]',
      save_button: '//*[@id="currency_form_submit_btn"]',
      success_danger_panel: '//*[@id="content"]/div[@class="bootstrap"]/div[contains(@class, "%B")]',
      search_iso_code_input: '(//*[@id="table-currency"]//input[@name="currencyFilter_iso_code"])[1]',
      search_button: '//*[@id="submitFilterButtoncurrency"]',
      reset_button: '//*[@id="table-currency"]//button[@name="submitResetcurrency"]',
      column_iso_code: '//*[@id="table-currency"]//td[3]',
      enable_icon: '//*[@id="table-currency"]//td[5]/a',
      currency_number_span: '//*[@id="form-currency"]//span[@class="badge"]',
      currency_iso_code_column: '//*[@id="table-currency"]//tbody/tr[%ID]/td[3]',
      sort_icon: '//*[@id="table-currency"]//span[contains(text(),"%B")]/a[%W]',
      currency_exchange_rate: '//*[@id="table-currency"]//tbody/tr[%ID]/td[4]',
      enabled_select: '//*[@id="table-currency"]//select[@name="currencyFilter_active"]',
      search_no_results: '//*[@id="table-currency"]//td[@class="list-empty"]',
      check_icon: '//*[@id="table-currency"]//tr[%ID]/td[5]/a/i[@class="%ICON"]',
      status_currency_toggle_button: '//*[@id="currencyStatus"]',
      column_exchange_rate: '//*[@id="table-currency"]//td[4]',
      edit_button: '//*[@id="table-currency"]//td[6]//a[contains(@class, "edit")]',
      cancel_button: '//*[@id="currency_form_cancel_btn"]',
      table_currencies: '//*[@id="table-currency"]',
      action_button: '//*[@id="table-currency"]//div[contains(@class, "pull-right")]/button[contains(@class, "dropdown-toggle")]',
      delete_action_button: '//*[@id="form-currency"]//a[contains(@onclick, "Delete selected")]',
      live_exchange_rate_toggle_button: '//*[@id="currencyCronjobLiveExchangeRate"]//div[contains(@class, "checkbox titatoggl")]',
      update_exchange_rate_button: '//*[@id="currency_form"]/button[@name="SubmitExchangesRates"]'
    }
  }
};
