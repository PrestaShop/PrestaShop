module.exports = {
  Location: {
    State: {
      country_select: '//*[@id="table-state"]//select[@name="stateFilter_cl!id_country"]',
      search_button: '//*[@id="submitFilterButtonstate"]',
      state_field_column: '//*[@id="table-state"]//tr[%L]//td[%C]'
    },
    Zone: {
      add_new_zone_button: '//*[@id="page-header-desc-zone-new_zone"]',
      cancel_button: '//*[@id="zone_form_cancel_btn"]',
      zones_table: '//*[@id="table-zone"]',
      name_input: '//*[@id="name"]',
      save_button: '//*[@id="zone_form_submit_btn"]',
      reset_button: '//*[@id="table-zone"]//button[@name="submitResetzone"]',
      alert_panel: '//*[@id="content"]/div[@class="bootstrap"]/div[contains(@class, "%I")]',
      search_zone_input: '//*[@id="table-zone"]//input[@name="zoneFilter_name"]',
      search_id_input: '//*[@id="table-zone"]//input[@name="zoneFilter_id_zone"]',
      search_button: '//*[@id="submitFilterButtonzone"]',
      enabled_disabled_icon: '//*[@id="table-zone"]//tr[%ID]/td[4]/a/i[@class="%ICON"]',
      sort_icon: '//*[@id="table-zone"]//span[contains(text(),"%B")]/a[%W]',
      number_zone_span: '//*[@id="form-zone"]//span[@class="badge"]',
      element_zone_table: '//*[@id="table-zone"]//tbody/tr[%ID]/td[%B]',
      search_enabled_list: '//*[@id="table-zone"]//select[@name="zoneFilter_active"]',
      edit_delete_button: '//*[@id="table-zone"]//td[5]//a[@title="%B"]',
      dropdown_button: '//*[@id="table-zone"]//td[5]//button',
      bulk_action_button: '//*[@id="bulk_action_menu_zone"]',
      action_group_button: '(//*[@id="form-zone"]//div[contains(@class, "bulk-actions")]//a)[%ID]',
      checkbox_input: '#table-zone > tbody > tr:nth-child(%B) > td.row-selector.text-center > input',
      search_by_zone_checkbox: '//*[@id="table-zone"]//tbody//td[contains(text(),"%B")]/../td[1]',
      search_by_zone_icon: '//*[@id="table-zone"]//tbody//td[contains(text(),"%B")]/../td[4]/a/i[@class="%ICON"]',
    },
    Country: {
      search_zone_list: '//*[@id="table-country"]//select[@name="countryFilter_z!id_zone"]',
      reset_button: '//*[@id="table-country"]//button[@name="submitResetcountry"]',
      search_zone_option_list: '//*[@id="table-country"]//select[@name="countryFilter_z!id_zone"]/option[text()="Asiaaa"]',
    }
  }
};
