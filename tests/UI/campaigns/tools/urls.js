require('module-alias/register');
const {DefaultCustomer} = require('@data/demo/customer');

/**
 * Login into BO to access BO Urls
 * @param page
 * @return {Promise<void>}
 */
const loginBO = async function (page) {
  await page.type('#email', global.BO.EMAIL);
  await page.type('#passwd', global.BO.PASSWD);

  await Promise.all([
    page.click('#submit_login'),
    page.waitForNavigation({waitUntil: 'networkidle'}),
  ]);

  const block = await page.$('button.onboarding-button-shut-down');

  if (block !== null) {
    await page.click('button.onboarding-button-shut-down');
    await page.waitForSelector('a.onboarding-button-stop', {visible: true});
    await page.click('a.onboarding-button-stop');
  }
};

/**
 * Login into FO to access user information pages
 * @param page
 * @return {Promise<void>}
 */
const loginFO = async function (page) {
  await page.type('#login-form input[name=email]', DefaultCustomer.email);
  await page.type('#login-form input[name=password]', DefaultCustomer.password);

  await Promise.all([
    page.click('#submit-login'),
    page.waitForNavigation('networkidle'),
  ]);
};

/**
 * Waiting for tinyMCE to load in pages :
 * Add Product, Add Brand, Add supplier
 *
 * @param page
 * @return {Promise<void>}
 */
const waitForTinyMCEToLoad = async function (page) {
  await page.waitForFunction(
    'typeof(tinyMCE)!== \'undefined\' && tinyMCE.activeEditor!==undefined',
    {timeout: 10000},
  );
};

module.exports = [
  {
    name: 'BO',
    urlPrefix: global.BO.URL,
    description: 'Crawling Back Office',
    urls: [
      {
        name: 'BO_login',
        url: 'index.php?controller=AdminLogin',
        async customAction(page) {
          await loginBO(page);
        },
      },
      {name: 'BO_dashboard', url: 'index.php?controller=AdminDashboard'},
      {name: 'BO_orders', url: 'index.php/sell/orders/orders/'},
      {name: 'BO_add_orders', url: 'index.php/sell/orders/orders/new'},
      {name: 'BO_invoices', url: 'index.php/sell/orders/invoices/'},
      {name: 'BO_credits_slips', url: 'index.php/sell/orders/credit-slips'},
      {name: 'BO_delivery_slips', url: 'index.php/sell/orders/delivery-slips/'},
      {name: 'BO_products', url: 'index.php/sell/catalog/products'},
      {
        name: 'BO_add_product',
        url: 'index.php/sell/catalog/products/new',
        async customAction(page) {
          await waitForTinyMCEToLoad(page);
        },
      },
      {name: 'BO_categories', url: 'index.php/sell/catalog/categories'},
      {
        name: 'BO_add_category',
        url: 'index.php/sell/catalog/categories/new',
        async customAction(page) {
          await waitForTinyMCEToLoad(page);
        },
      },
      {name: 'BO_monitoring', url: 'index.php/sell/catalog/monitoring'},
      {name: 'BO_attributes', url: 'index.php?controller=AdminAttributesGroups'},
      {name: 'BO_add_attribute', url: 'index.php?controller=AdminAttributesGroups&addattribute_group'},
      {name: 'BO_add_attribute_value', url: 'index.php?controller=AdminAttributesGroups&updateattribute'},
      {name: 'BO_features', url: 'index.php?controller=AdminFeatures'},
      {name: 'BO_add_feature', url: 'index.php?controller=AdminFeatures&addfeature'},
      {name: 'BO_add_feature_value', url: 'index.php?controller=AdminFeatures&addfeature_value'},
      {name: 'BO_brands', url: 'index.php/sell/catalog/brands/'},
      {
        name: 'BO_add_brand',
        url: 'index.php/sell/catalog/brands/new',
        async customAction(page) {
          await waitForTinyMCEToLoad(page);
        },
      },
      {name: 'BO_add_brand_address', url: 'index.php/sell/catalog/brands/addresses/new'},
      {name: 'BO_suppliers', url: 'index.php/sell/catalog/suppliers'},
      {
        name: 'BO_add_supplier',
        url: 'index.php/sell/catalog/suppliers/new',
        async customAction(page) {
          await waitForTinyMCEToLoad(page);
        },
      },
      {name: 'BO_files', url: 'index.php/sell/attachments'},
      {name: 'BO_add_file', url: 'index.php/sell/attachments/new'},
      {name: 'BO_discounts', url: 'index.php?controller=AdminCartRules'},
      {name: 'BO_cart_rules', url: 'index.php?controller=AdminCartRules'},
      {name: 'BO_add_cart_rule', url: 'index.php?controller=AdminCartRules&addcart_rule'},
      {name: 'BO_catalog_price_rules', url: 'index.php?controller=AdminSpecificPriceRule'},
      {name: 'BO_add_catalog_price_rule', url: 'index.php?controller=AdminSpecificPriceRule&addspecific_price_rule'},
      {name: 'BO_stock', url: 'index.php/sell/stocks/'},
      {name: 'BO_movements', url: 'index.php/sell/stocks/movements'},
      {name: 'BO_customers', url: 'index.php/sell/customers/'},
      {name: 'BO_add_customer', url: 'index.php/sell/customers/new'},
      {name: 'BO_addresses', url: 'index.php/sell/addresses/'},
      {name: 'BO_add_address', url: 'index.php/sell/addresses/new'},
      {name: 'BO_customer_service', url: 'index.php?controller=AdminCustomerThreads'},
      {name: 'BO_order_messages', url: 'index.php/sell/customer-service/order-messages/'},
      {name: 'BO_add_order_message', url: 'index.php/sell/customer-service/order-messages/new'},
      {name: 'BO_merchandise_returns', url: 'index.php?controller=AdminReturn'},
      {name: 'BO_stats', url: 'index.php?controller=AdminStats'},
      {name: 'BO_module_manager', url: 'index.php/improve/modules/manage'},
      {name: 'BO_module_manager_alerts', url: 'index.php/improve/modules/alerts'},
      {name: 'BO_module_manager_updates', url: 'index.php/improve/modules/updates'},
      {name: 'BO_module_catalog', url: 'index.php/modules/addons/modules/catalog'},
      {name: 'BO_module_catalog_selection', url: 'index.php/improve/modules/addons-store'},
      {name: 'BO_theme_logo', url: 'index.php/improve/design/themes/'},
      {name: 'BO_add_theme', url: 'index.php/improve/design/themes/import'},
      {name: 'BO_homepage_configuration', url: 'index.php?controller=AdminPsThemeCustoConfiguration'},
      {name: 'BO_advanced_customization', url: 'index.php?controller=AdminPsThemeCustoAdvanced'},
      {name: 'BO_theme_catalog', url: 'index.php/modules/addons/themes/catalog'},
      {name: 'BO_email_theme', url: 'index.php/improve/design/mail_theme/'},
      {name: 'BO_pages', url: 'index.php/improve/design/cms-pages/'},
      {name: 'BO_add_page_category', url: 'index.php/improve/design/cms-pages/category/new'},
      {
        name: 'BO_add_page',
        url: 'index.php/improve/design/cms-pages/new',
        async customAction(page) {
          await waitForTinyMCEToLoad(page);
        },
      },
      {name: 'BO_positions', url: 'index.php/improve/design/modules/positions/'},
      {name: 'BO_transplant_module', url: 'index.php?controller=AdminModulesPositions&addToHook='},
      {name: 'BO_image_settings', url: 'index.php?controller=AdminImages'},
      {name: 'BO_add_image_type', url: 'index.php?controller=AdminImages&addimage_type'},
      {name: 'BO_link_widget', url: 'index.php/modules/link-widget/list'},
      {name: 'BO_link_widget_new_block', url: 'index.php/modules/link-widget/create'},
      {name: 'BO_carriers', url: 'index.php?controller=AdminCarriers'},
      {name: 'BO_add_carrier', url: 'index.php?controller=AdminCarrierWizard'},
      {name: 'BO_shipping_preferences', url: 'index.php/improve/shipping/preferences'},
      {name: 'BO_payment_methods', url: 'index.php/improve/payment/payment_methods'},
      {name: 'BO_payment_preferences', url: 'index.php/improve/payment/preferences'},
      {name: 'BO_localization', url: 'index.php/improve/international/localization/'},
      {name: 'BO_localization_languages', url: 'index.php/improve/international/languages/'},
      {name: 'BO_add_localization_language', url: 'index.php/improve/international/languages/new'},
      {name: 'BO_localization_currencies', url: 'index.php/improve/international/currencies/'},
      {name: 'BO_add_localization_currency', url: 'index.php/improve/international/currencies/new'},
      {name: 'BO_localization_geolocation', url: 'index.php/improve/international/geolocation/'},
      {name: 'BO_locations_zones', url: 'index.php?controller=AdminZones'},
      {name: 'BO_add_locations_zone', url: 'index.php?controller=AdminZones&addzone'},
      {name: 'BO_locations_countries', url: 'index.php?controller=AdminCountries'},
      {name: 'BO_add_locations_country', url: 'index.php?controller=AdminCountries&addcountry'},
      {name: 'BO_locations_states', url: 'index.php?controller=AdminStates'},
      {name: 'BO_add_locations_state', url: 'index.php?controller=AdminStates&addstate'},
      {name: 'BO_taxes', url: 'index.php/improve/international/taxes/'},
      {name: 'BO_add_tax', url: 'index.php/improve/international/taxes/new'},
      {name: 'BO_tax_rules', url: 'index.php?controller=AdminTaxRulesGroup'},
      {name: 'BO_add_tax_rule', url: 'index.php?controller=AdminTaxRulesGroup&addtax_rules_group'},
      {name: 'BO_translations', url: 'index.php/improve/international/translations/settings'},
      {name: 'BO_parameters_general', url: 'index.php/configure/shop/preferences/preferences'},
      {name: 'BO_parameters_maintenance', url: 'index.php/configure/shop/maintenance/'},
      {name: 'BO_orders_settings', url: 'index.php/configure/shop/order-preferences/'},
      {name: 'BO_orders_statuses', url: 'index.php?controller=AdminStatuses'},
      {name: 'BO_add_orders_status', url: 'index.php?controller=AdminStatuses&addorder_state'},
      {name: 'BO_add_orders_return_status', url: 'index.php?controller=AdminStatuses&addorder_return_state'},
      {name: 'BO_product_settings', url: 'index.php/configure/shop/product-preferences/'},
      {name: 'BO_customer_settings', url: 'index.php/configure/shop/customer-preferences/'},
      {name: 'BO_customer_settings_groups', url: 'index.php?controller=AdminGroups'},
      {name: 'BO_add_customer_settings_group', url: 'index.php?controller=AdminGroups&addgroup'},
      {name: 'BO_customer_settings_titles', url: 'index.php?controller=AdminGenders'},
      {name: 'BO_add_customer_settings_title', url: 'index.php?controller=AdminGenders&addgender'},
      {name: 'BO_contact', url: 'index.php/configure/shop/contacts/'},
      {name: 'BO_add_contact', url: 'index.php/configure/shop/contacts/new'},
      {name: 'BO_traffic_seo_urls', url: 'index.php/configure/shop/seo-urls/'},
      {name: 'BO_add_page_traffic_seo_url', url: 'index.php/configure/shop/seo-urls/new'},
      {name: 'BO_traffic_seo_search_engines', url: 'index.php?controller=AdminSearchEngines'},
      {name: 'BO_add_traffic_seo_search_engine', url: 'index.php?controller=AdminSearchEngines&addsearch_engine'},
      {name: 'BO_traffic_seo_referrers', url: 'index.php?controller=AdminReferrers'},
      {name: 'BO_add_traffic_seo_referrer', url: 'index.php?controller=AdminReferrers&addreferrer'},
      {name: 'BO_search', url: 'index.php?controller=AdminSearchConf'},
      {name: 'BO_add_search_alias', url: 'index.php?controller=AdminSearchConf&addalias'},
      {name: 'BO_search_tags', url: 'index.php?controller=AdminTags'},
      {name: 'BO_add_search_tag', url: 'index.php?controller=AdminTags&addtag'},
      {name: 'BO_merchant_expertise', url: 'index.php?controller=AdminGamification'},
      {name: 'BO_information', url: 'index.php/configure/advanced/system-information/'},
      {name: 'BO_performance', url: 'index.php/configure/advanced/performance/'},
      {name: 'BO_administration', url: 'index.php/configure/advanced/administration/'},
      {name: 'BO_emails', url: 'index.php/configure/advanced/emails/'},
      {name: 'BO_import', url: 'index.php/configure/advanced/import/'},
      {name: 'BO_employees', url: 'index.php/configure/advanced/employees/'},
      {name: 'BO_add_employee', url: 'index.php/configure/advanced/employees/new'},
      {name: 'BO_profiles', url: 'index.php/configure/advanced/profiles/'},
      {name: 'BO_add_profile', url: 'index.php/configure/advanced/profiles/new'},
      {name: 'BO_permissions', url: 'index.php?controller=AdminAccess'},
      {name: 'BO_database', url: 'index.php/configure/advanced/sql-requests/'},
      {name: 'BO_add_query', url: 'index.php/configure/advanced/sql-requests/new'},
      {name: 'BO_webservice', url: 'index.php/configure/advanced/webservice-keys/'},
      {name: 'BO_add_webservice_key', url: 'index.php/configure/advanced/webservice-keys/new'},
    ],
  },
  {
    name: 'FO',
    urlPrefix: global.FO.URL,
    description: 'Crawling Front Office',
    urls: [
      {name: 'FO_homepage_en', url: 'index.php?id_lang=1'},
      {name: 'FO_homepage_fr', url: 'index.php?id_lang=2'},
      {name: 'FO_login', url: 'index.php?controller=authentication&back=my-account'},
      {
        name: 'FO_my_account',
        url: 'index.php?controller=authentication&back=my-account',
        async customAction(page) {
          await loginFO(page);
        },
      },
      {
        name: 'FO_product_1_en',
        url: 'index.php?id_product=1&id_product_attribute=1&rewrite=hummingbird-printed-t-shirt'
          + '&controller=product&id_lang=1#/1-size-s/8-color-white',
      },
      {
        name: 'FO_product_1_fr',
        url: 'index.php?id_product=1&id_product_attribute=1&rewrite=hummingbird-printed-t-shirt'
          + '&controller=product&id_lang=2#/1-size-s/8-color-white',
      },
      {name: 'FO_category_clothes_en', url: 'index.php?id_category=3&controller=category&id_lang=1'},
      {name: 'FO_category_clothes_fr', url: 'index.php?id_category=3&controller=category&id_lang=2'},
      {name: 'FO_contact_us_en', url: 'index.php?controller=contact&id_lang=1'},
      {name: 'FO_contact_us_fr', url: 'index.php?controller=contact&id_lang=2'},
      {name: 'FO_prices_drop_en', url: 'index.php?controller=prices-drop&id_lang=1'},
      {name: 'FO_prices_drop_fr', url: 'index.php?controller=prices-drop&id_lang=2'},
      {name: 'FO_new_products_en', url: 'index.php?controller=new-products&id_lang=1'},
      {name: 'FO_new_products_fr', url: 'index.php?controller=new-products&id_lang=2'},
      {name: 'FO_best_sales_en', url: 'index.php?controller=best-sales&id_lang=1'},
      {name: 'FO_best_sales_fr', url: 'index.php?controller=best-sales&id_lang=2'},
      {name: 'FO_search_mug_en', url: 'index.php?controller=search&s=mug&id_lang=1'},
      {name: 'FO_search_mug_fr', url: 'index.php?controller=search&s=mug&id_lang=2'},
      {name: 'FO_cms_delivery_en', url: 'index.php?id_cms=1&controller=cms&id_lang=1'},
      {name: 'FO_cms_delivery_fr', url: 'index.php?id_cms=1&controller=cms&id_lang=2'},
      {name: 'FO_cms_legal_notice_en', url: 'index.php?id_cms=2&controller=cms&id_lang=1'},
      {name: 'FO_cms_legal_notice_fr', url: 'index.php?id_cms=2&controller=cms&id_lang=2'},
      {name: 'FO_cms_terms_and_conditions_en', url: 'index.php?id_cms=3&controller=cms&id_lang=1'},
      {name: 'FO_cms_terms_and_conditions_fr', url: 'index.php?id_cms=3&controller=cms&id_lang=2'},
      {name: 'FO_cms_about_us_en', url: 'index.php?id_cms=4&controller=cms&id_lang=1'},
      {name: 'FO_cms_about_us_fr', url: 'index.php?id_cms=4&controller=cms&id_lang=2'},
      {name: 'FO_cms_secure_payment_en', url: 'index.php?id_cms=5&controller=cms&id_lang=1'},
      {name: 'FO_cms_secure_payment_fr', url: 'index.php?id_cms=5&controller=cms&id_lang=2'},
      {name: 'FO_sitmap_en', url: 'index.php?controller=sitemap&id_lang=1'},
      {name: 'FO_sitmap_fr', url: 'index.php?controller=sitemap&id_lang=2'},
      {name: 'FO_stores_en', url: 'index.php?controller=stores&id_lang=1'},
      {name: 'FO_stores_fr', url: 'index.php?controller=stores&id_lang=2'},
      {name: 'FO_account_information_en', url: 'index.php?controller=identity&id_lang=1'},
      {name: 'FO_account_information_fr', url: 'index.php?controller=identity&id_lang=2'},
      {name: 'FO_account_addresses_en', url: 'index.php?controller=addresses&id_lang=1'},
      {name: 'FO_account_addresses_fr', url: 'index.php?controller=addresses&id_lang=2'},
      {name: 'FO_account_history_en', url: 'index.php?controller=history&id_lang=1'},
      {name: 'FO_account_history_fr', url: 'index.php?controller=history&id_lang=2'},
      {name: 'FO_account_credit_slips_en', url: 'index.php?controller=order-slip&id_lang=1'},
      {name: 'FO_account_credit_slips_fr', url: 'index.php?controller=order-slip&id_lang=2'},
      {name: 'FO_account_gdpr_en', url: 'index.php?fc=module&module=psgdpr&controller=gdpr&id_lang=1'},
      {name: 'FO_account_gdpr_fr', url: 'index.php?fc=module&module=psgdpr&controller=gdpr&id_lang=2'},
    ],
  },
];
