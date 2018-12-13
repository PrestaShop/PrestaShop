module.exports = {
  PagesForm: {
    calendar_form: '//*[@id="calendar_form"]',
    Orders: {
      order_form: '//*[@id="form-order"]',
      invoice_form: '//*[@id="main-div"]//form[@name="generate_by_date"]',
      order_slip_form: '//*[@id="form-order_slip"]',
      delivery_form: '//*[@id="delivery_pdf_fieldset"]',
      shopping_cart_form: '//*[@id="form-cart"]',
    },
    Catalog: {
      product_form: '//*[@id="product_catalog_list"]',
      category_form: '//*[@id="form-category"]',
      empty_category_form: '//*[@id="form-empty_categories"]',
      attribute_form: '//*[@id="form-attribute_group"]',
      feature_form: '//*[@id="form-feature"]',
      manufacturer_form: '//*[@id="form-manufacturer"]',
      supplier_form: '//*[@id="subtab-AdminSuppliers"]',
      attachment_form: '//*[@id="form-attachment"]',
      cart_rule_form: '//*[@id="form-cart_rule"]',
      cart_price_rule_form: '//*[@id="form-specific_price_rule"]',
      search_box: '//*[@id="search"]' // Search products in stock and movements page
    },
    Customers: {
      customer_form: '//*[@id="form-customer"]',
      address_form: '//*[@id="form-address"]'
    },
    CustomerService: {
      customer_service_form: '//*[@id="form-customer_thread"]',
      order_message_form: '//*[@id="form-order_message"]',
      order_returns_form: '//*[@id="form-order_return"]'
    },
    Stats: {
      stats_dashboard: '//*[@id="statsContainer"]'
    },
    Modules: {
      modules_list: '//*[@id="modules-list-container-all"]',
      modules_search_input: '.pstaggerAddTagInput', // search input in installed modules tab
      modules_to_configure: '//*[@id="module-short-list-configure"]', // the text in notifications tab
      addons_search_form: '//*[@id="addons-search-form"]',
      module_list_updates: '//*[@id="modules-list-container-update"]',
    },
    Design: {
      design_form: '//*[@id="configuration_form"]',
      catalog_theme: '//div[contains(@class, "addons-catalog-theme")]/div[1]',
      configuration_fieldset: '//*[@id="configuration_fieldset_appearance"]',
      cms_category_form: '//*[@id="form-cms_category"]',
      position_module_form: '//*[@id="module-positions-form"]',
      image_type_form: '//*[@id="form-image_type"]',
      configuration_link_form: '//*[@id="configuration_form"]',
      menu_module_name: '(//*[@id="psthemecusto"]//div[@data-module_name="menu"])[2]',
      download_theme_button: '//*[@id="download_child_theme"]',
    },
    Shipping: {
      carrier_form: '//*[@id="form-carrier"]',
      configuration_form: '//*[@id="configuration_form"]'
    },
    Payment: {
      active_payment: '//*[@id="main-div"]//h3[text()[contains(., "Active payment")]]',
      currency_form: '//*[@id="main-div"]/div[@class="content-div  "]//form'
    },
    International: {
      localization_pack_select: '//*[@id="main-div"]//form[@name="import_localization_pack"]',
      languages_form: '//*[@id="form-lang"]',
      currency_form: '//*[@id="form-currency"]',
      geolocation_by_address: '//*[@id="main-div"]/div[@class="content-div  with-tabs"]//form',
      zone_form: '//*[@id="form-zone"]',
      country_form: '//*[@id="form-country"]',
      state_form: '//*[@id="form-state"]',
      tax_from: '//*[@id="form-tax"]',
      tax_rules_from: '//*[@id="form-tax_rules_group"]',
      translation_form: '//*[@id="main-div"]//form[@name="modify_translations"]',
    },
    ShopParameters: {
      general_form: '//*[@id="configuration_form"]',
      maintenance_tab_form: '//*[@id="main-div"]//form[@name="form"]',
      order_settings_form: '//*[@id="configuration_form"]',
      statuses_form: '//*[@id="form-order_state"]',
      product_settings_form: '//*[@id="configuration_form"]',
      customers_form: '//*[@id="configuration_form"]',
      groups_form: '//*[@id="form-group"]',
      titles_form: '//*[@id="form-gender"]',
      contact_form: '//*[@id="form-contact"]',
      stores_form: '//*[@id="form-store"]',
      meta_form: '//*[@id="form-meta"]', //SEO & URLs form
      search_engine_form: '//*[@id="form-search_engine"]',
      index_form: '//*[@id="refresh_index_form"]', //Referrers  tab
      alias_form: '//*[@id="form-alias"]', //Search page
      tags_form: '//*[@id="form-tag"]',
      gamification_box: '//*[@id="intro_gamification"]',
      seo_url_showcase_card: '//*[@id="seo-urls-showcase-card"]',
    },
    AdvancedParameters: {
      check_configuration_box: '//*[@id="checkConfiguration"]',
      debug_mode_button: '//form[contains(@class, "form-horizontal")]', // performance page
      administration_form: '//form[contains(@class, "form-horizontal")]',
      mail_form: '//*[@id="email_logs_grid_panel"] | //*[@id="form_mail"]',
      preview_import_form: '//form[contains(@class, "import")]',
      employee_form: '//*[@id="form-employee"]', //team page
      profiles_form: '//*[@id="form-profile"]',
      permissions_form: '//*[@id="access_form"]',
      request_sql_form: '//*[@id="form-request_sql"]', //database page
      log_form: '//*[@id="logs_grid_panel"]',
      webservice_form: '//*[@id="form-webservice_account"]',
      multistore_form: '//*[@id="form-shop_group"]',
      backup_filter_form: '//*[@id="backup_filter_form"]',
    }
  }
};
