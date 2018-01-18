module.exports = {
  PagesForm: {
    calendar_form: '//*[@id="calendar_form"]',
    Orders: {
      order_form: '//*[@id="form-order"]',
      invoice_form: '//*[@id="invoice_date_form"]',
      order_slip_form: '//*[@id="form-order_slip"]',
      delivery_form: '//*[@id="delivery_form"]',
      shopping_cart_form: '//*[@id="form-cart"]',
    },
    Catalog: {
      product_form: '//*[@id="product_catalog_list"]',
      category_form: '//*[@id="form-category"]',
      empty_category_form: '//*[@id="form-empty_categories"]',
      attribute_form: '//*[@id="form-attribute_group"]',
      manufacturer_form: '//*[@id="form-manufacturer"]',
      attachment_form: '//*[@id="form-attachment"]',
      cart_rule_form: '//*[@id="form-cart_rule"]',
      search_box: '//*[@id="search"]' // Search products in stock page

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
      addons_search_form: '//*[@id="addons-search-form"]'
    },
    Design: {
      design_form: '//*[@id="configuration_form"]',
      catalog_theme: '//div[contains(@class, "addons-catalog-theme")]/div[1]',
      cms_category_form: '//*[@id="form-cms_category"]',
      position_filter_form: '//*[@id="position_filer"]',
      image_type_form: '//*[@id="form-image_type"]',
      configuration_link_form: '//*[@id="configuration_form"]'
    },
    Shipping: {
      carrier_form: '//*[@id="form-carrier"]',
      delivery_form: '//*[@id="delivery_form"]'
    },
    Payment: {
      recommended_payment: '//*[@id="recommended-payment-gateways-panel"]',
      currency_form: '//*[@id="form_currency"]'
    },
    International: {
      localization_pack_select: '//*[@id="iso_localization_pack_chosen"]',
      zone_form: '//*[@id="form-zone"]',
      tax_from: '//*[@id="form-tax"]',
      translation_form: '//*[@id="typeTranslationForm"]',
    },
    ShopParameters: {
      general_form: '//*[@id="configuration_form"]',
      order_settings_form: '//*[@id="configuration_form"]',
      product_settings_form: '//*[@id="configuration_form"]',
      customers_form: '//*[@id="configuration_form"]',
      contact_form: '//*[@id="form-contact"]',
      meta_form: '//*[@id="form-meta"]', //SEO & URLs form
      alias_form: '//*[@id="form-alias"]', //Search page
      gamification_box: '//*[@id="intro_gamification"]',
    },
    AdvancedParameters: {
      check_configuration_box: '//*[@id="checkConfiguration"]',
      debug_mode_select: '//*[@id="form_debug_mode_debug_mode"]', // performance page
      administration_form: '//*[@id="configuration_form"]',
      mail_form: '//*[@id="form-mail"]',
      preview_import_form: '//*[@id="preview_import"]',
      employee_form: '//*[@id="form-employee"]', //team page
      request_sql_form: '//*[@id="request_sql_form"]', //database page
      log_form: '//*[@id="form-log"]',
      webservice_form: '//*[@id="form-webservice_account"]',
      multistore_form: '//*[@id="form-shop_group"]'
    }
  }
};