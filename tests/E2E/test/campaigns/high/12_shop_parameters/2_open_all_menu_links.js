const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {PagesForm} = require('../../../selectors/BO/pages_form.js');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const {Menu} = require('../../../selectors/BO/menu.js');
const {OnBoarding} = require('../../../selectors/BO/onboarding');
const common = require('../../common_scenarios/shop_parameters');
let promise = Promise.resolve();

scenario('Open all menu links in the Back Office', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
    test('should check and click on "Stop the OnBoarding" button', () => {
      return promise
        .then(() => client.isVisible(OnBoarding.stop_button))
        .then(() => client.stopOnBoarding(OnBoarding.stop_button))
    });
  }, 'onboarding');
  scenario('Go to "Dashboard" page in the Back Office', client => {
    common.clickOnMenuLinksAndCheckElement(client, "", Menu.dashboard_menu, PagesForm.calendar_form, "Dashboard");
  }, 'common_client');
  scenario('Check all the menu links of "SELL"', () => {
    scenario('Check all the menu links of "Orders" in the Back Office', client => {
      common.clickOnMenuLinksAndCheckElement(client, Menu.Sell.Orders.orders_menu, Menu.Sell.Orders.orders_submenu, PagesForm.Orders.order_form, "Orders");
      common.clickOnMenuLinksAndCheckElement(client, Menu.Sell.Orders.orders_menu, Menu.Sell.Orders.invoices_submenu, PagesForm.Orders.invoice_form, "Invoices");
      common.clickOnMenuLinksAndCheckElement(client, Menu.Sell.Orders.orders_menu, Menu.Sell.Orders.credit_slips_submenu, PagesForm.Orders.order_slip_form, "Credit slips");
      common.clickOnMenuLinksAndCheckElement(client, Menu.Sell.Orders.orders_menu, Menu.Sell.Orders.delivery_slips_submenu, PagesForm.Orders.delivery_form, "Delivery slips");
      common.clickOnMenuLinksAndCheckElement(client, Menu.Sell.Orders.orders_menu, Menu.Sell.Orders.shopping_carts_submenu, PagesForm.Orders.shopping_cart_form, "Shopping cart");
    }, 'common_client');
    scenario('Check all the menu links of "Catalog" in the Back Office', client => {
      common.clickOnMenuLinksAndCheckElement(client, Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.products_submenu, PagesForm.Catalog.product_form, "Catalog", "Products");
      common.clickOnMenuLinksAndCheckElement(client, Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.category_submenu, PagesForm.Catalog.category_form, "Category");
      common.clickOnMenuLinksAndCheckElement(client, Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.monitoring_submenu, PagesForm.Catalog.empty_category_form, "Monitoring");
      common.clickOnMenuLinksAndCheckElement(client, Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.attributes_features_submenu, PagesForm.Catalog.attribute_form, "Attributes & Features", "Attributes");
      common.clickOnMenuLinksAndCheckElement(client, Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.attributes_features_submenu, PagesForm.Catalog.feature_form, "Attributes & Features", "Features", 0, Menu.Sell.Catalog.feature_tab);
      common.clickOnMenuLinksAndCheckElement(client, Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.manufacturers_submenu, PagesForm.Catalog.manufacturer_form, "Brands & Suppliers", "Brands");
      common.clickOnMenuLinksAndCheckElement(client, Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.manufacturers_submenu, PagesForm.Catalog.supplier_form, "Brands & Suppliers", "Suppliers", 0, Menu.Sell.Catalog.supplier_tab);
      common.clickOnMenuLinksAndCheckElement(client, Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.files_submenu, PagesForm.Catalog.attachment_form, "Files");
      common.clickOnMenuLinksAndCheckElement(client, Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.discounts_submenu, PagesForm.Catalog.cart_rule_form, "Discounts", "Carte Rules");
      common.clickOnMenuLinksAndCheckElement(client, Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.discounts_submenu, PagesForm.Catalog.cart_price_rule_form, "Discounts", "Carte Price Rules", 0, Menu.Sell.Catalog.catalog_price_rules_tab);
      common.clickOnMenuLinksAndCheckElement(client, Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.stocks_submenu, PagesForm.Catalog.search_box, "Stocks", "Stock", 4000);
      common.clickOnMenuLinksAndCheckElement(client, Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.stocks_submenu, PagesForm.Catalog.search_box, "Stocks", "Movements", 0, Menu.Sell.Catalog.movement_tab);
    }, 'common_client');
    scenario('Check all the menu links of "Customers" in the Back Office', client => {
      common.clickOnMenuLinksAndCheckElement(client, Menu.Sell.Customers.customers_menu, Menu.Sell.Customers.customers_submenu, PagesForm.Customers.customer_form, "Customers");
      common.clickOnMenuLinksAndCheckElement(client, Menu.Sell.Customers.customers_menu, Menu.Sell.Customers.addresses_submenu, PagesForm.Customers.address_form, "Addresses");
    }, 'common_client');
    scenario('Check all the menu links of "Customer Service" in the Back Office', client => {
      common.clickOnMenuLinksAndCheckElement(client, Menu.Sell.CustomerService.customer_service_menu, Menu.Sell.CustomerService.customer_service_submenu, PagesForm.CustomerService.customer_service_form, "Customer Service");
      common.clickOnMenuLinksAndCheckElement(client, Menu.Sell.CustomerService.customer_service_menu, Menu.Sell.CustomerService.order_messages_submenu, PagesForm.CustomerService.order_message_form, "Order messages");
      common.clickOnMenuLinksAndCheckElement(client, Menu.Sell.CustomerService.customer_service_menu, Menu.Sell.CustomerService.merchandise_returns_submenu, PagesForm.CustomerService.order_returns_form, "Merchandise returns");
    }, 'common_client');
    scenario('Check the menu links of "Stats" in the Back Office', client => {
      common.clickOnMenuLinksAndCheckElement(client, "", Menu.Sell.Stats.stats_menu, PagesForm.Stats.stats_dashboard, "Stats");
    }, 'common_client');
  }, 'common_client');
  scenario('Check all the menu links of "IMPROVE"', () => {
    scenario('Check all the menu links of "Modules" in the Back Office', client => {
      common.clickOnMenuLinksAndCheckElement(client, Menu.Improve.Modules.modules_menu, Menu.Improve.Modules.modules_catalog_submenu, PagesForm.Modules.modules_list, "Module Catalog", "Module Catalog");
      common.clickOnMenuLinksAndCheckElement(client, Menu.Improve.Modules.modules_menu, Menu.Improve.Modules.modules_catalog_submenu, PagesForm.Modules.addons_search_form, "Module Catalog", "Module Selections", 0, Menu.Improve.Modules.selection_tab);
      common.clickOnMenuLinksAndCheckElement(client, Menu.Improve.Modules.modules_menu, Menu.Improve.Modules.modules_manager_submenu, PagesForm.Modules.modules_search_input, "Module Manager", "Modules", 5000);
      common.clickOnMenuLinksAndCheckElement(client, Menu.Improve.Modules.modules_menu, Menu.Improve.Modules.modules_manager_submenu, PagesForm.Modules.modules_to_configure, "Module Manager", "Alerts", 1000, Menu.Improve.Modules.alerts_subTab);
      common.clickOnMenuLinksAndCheckElement(client, Menu.Improve.Modules.modules_menu, Menu.Improve.Modules.modules_manager_submenu, PagesForm.Modules.module_list_updates, "Modules", "Updates", 1000, Menu.Improve.Modules.updates_subTab);
    }, 'common_client');
    scenario('Check all the menu links of "Design" in the Back Office', client => {
      common.clickOnMenuLinksAndCheckElement(client, Menu.Improve.Design.design_menu, Menu.Improve.Design.theme_logo_submenu, PagesForm.Design.configuration_fieldset, "Design", "Theme & Logo");
      common.clickOnMenuLinksAndCheckElement(client, Menu.Improve.Design.design_menu, Menu.Improve.Design.theme_logo_submenu, PagesForm.Design.menu_module_name, "Theme & Logo", "Homepage Configuration", 0, Menu.Improve.Design.homepage_configuration_subTab);
      common.clickOnMenuLinksAndCheckElement(client, Menu.Improve.Design.design_menu, Menu.Improve.Design.theme_logo_submenu, PagesForm.Design.download_theme_button, "Theme & Logo", "Advanced Customization", 0, Menu.Improve.Design.advanced_customization_subTab);
      common.clickOnMenuLinksAndCheckElement(client, Menu.Improve.Design.design_menu, Menu.Improve.Design.theme_catalog_submenu, PagesForm.Design.catalog_theme, "Theme catalog");
      common.clickOnMenuLinksAndCheckElement(client, Menu.Improve.Design.design_menu, Menu.Improve.Design.pages_submenu, PagesForm.Design.cms_category_form, "Pages");
      common.clickOnMenuLinksAndCheckElement(client, Menu.Improve.Design.design_menu, Menu.Improve.Design.positions_submenu, PagesForm.Design.position_module_form, "Positions");
      common.clickOnMenuLinksAndCheckElement(client, Menu.Improve.Design.design_menu, Menu.Improve.Design.image_settings_submenu, PagesForm.Design.image_type_form, "Image settings");
      common.clickOnMenuLinksAndCheckElement(client, Menu.Improve.Design.design_menu, Menu.Improve.Design.link_widget_submenu, PagesForm.Design.configuration_link_form, "Link widget");
    }, 'common_client');
    scenario('Check all the menu links of "Shipping" in the Back Office', client => {
      common.clickOnMenuLinksAndCheckElement(client, Menu.Improve.Shipping.shipping_menu, Menu.Improve.Shipping.carriers_submenu, PagesForm.Shipping.carrier_form, "Shipping", "Carrier");
      common.clickOnMenuLinksAndCheckElement(client, Menu.Improve.Shipping.shipping_menu, Menu.Improve.Shipping.preferences_submenu, PagesForm.Shipping.configuration_form, "Delivery");
    }, 'common_client');
    scenario('Check all the menu links of "Payment" in the Back Office', client => {
      common.clickOnMenuLinksAndCheckElement(client, Menu.Improve.Payment.payment_menu, Menu.Improve.Payment.payment_methods_submenu, PagesForm.Payment.active_payment, "Payment");
      common.clickOnMenuLinksAndCheckElement(client, Menu.Improve.Payment.payment_menu, Menu.Improve.Payment.preferences_submenu, PagesForm.Payment.currency_form, "Currency");
    }, 'common_client');
    scenario('Check all the menu links of "International" in the Back Office', client => {
      common.clickOnMenuLinksAndCheckElement(client, Menu.Improve.International.international_menu, Menu.Improve.International.localization_submenu, PagesForm.International.localization_pack_select, "International", "Localization", 2000);
      common.clickOnMenuLinksAndCheckElement(client, Menu.Improve.International.international_menu, Menu.Improve.International.localization_submenu, PagesForm.International.languages_form, "International", "Languages", 2000, Menu.Improve.International.languages_tab);
      common.clickOnMenuLinksAndCheckElement(client, Menu.Improve.International.international_menu, Menu.Improve.International.localization_submenu, PagesForm.International.currency_form, "International", "Currencies", 2000, Menu.Improve.International.currencies_tab);
      common.clickOnMenuLinksAndCheckElement(client, Menu.Improve.International.international_menu, Menu.Improve.International.localization_submenu, PagesForm.International.geolocation_by_address, "International", "Geolocation", 2000, Menu.Improve.International.geolocation_tab);
      common.clickOnMenuLinksAndCheckElement(client, Menu.Improve.International.international_menu, Menu.Improve.International.locations_submenu, PagesForm.International.zone_form, "Locations", "Zones");
      common.clickOnMenuLinksAndCheckElement(client, Menu.Improve.International.international_menu, Menu.Improve.International.locations_submenu, PagesForm.International.country_form, "Locations", "Countries", 0, Menu.Improve.International.countries_tab);
      common.clickOnMenuLinksAndCheckElement(client, Menu.Improve.International.international_menu, Menu.Improve.International.locations_submenu, PagesForm.International.state_form, "Locations", "States", 0, Menu.Improve.International.states_tab);
      common.clickOnMenuLinksAndCheckElement(client, Menu.Improve.International.international_menu, Menu.Improve.International.taxes_submenu, PagesForm.International.tax_from, "Taxes", "Taxes");
      common.clickOnMenuLinksAndCheckElement(client, Menu.Improve.International.international_menu, Menu.Improve.International.taxes_submenu, PagesForm.International.tax_rules_from, "Taxes", "Tax Rules", 0, Menu.Improve.International.taxe_rules_tab);
      common.clickOnMenuLinksAndCheckElement(client, Menu.Improve.International.international_menu, Menu.Improve.International.translations_submenu, PagesForm.International.translation_form, "Translations");
    }, 'common_client');
  }, 'common_client');
  scenario('Check all the menu links of "CONFIGURE"', () => {
    scenario('Check all the menu links of "Shop Parameters" in the Back Office', client => {
      test('should check then close the "Symfony" toolbar', () => {
        return promise
          .then(() => {
            if (global.ps_mode_dev) {
              client.waitForExistAndClick(AddProductPage.symfony_toolbar);
            }
          })
          .then(() => client.pause(1000));
      });
      test('should stop the "On Boarding"', () => {
        return promise
          .then(() => client.isVisible(OnBoarding.stop_button))
          .then(() => client.closeBoarding(OnBoarding.stop_button));
      });
      common.clickOnMenuLinksAndCheckElement(client, Menu.Configure.ShopParameters.shop_parameters_menu, Menu.Configure.ShopParameters.general_submenu, PagesForm.ShopParameters.general_form, "Shop Parameters", "General");
      common.clickOnMenuLinksAndCheckElement(client, Menu.Configure.ShopParameters.shop_parameters_menu, Menu.Configure.ShopParameters.general_submenu, PagesForm.ShopParameters.maintenance_tab_form, "Shop Parameters", "Maintenance", 0, Menu.Configure.ShopParameters.maintenance_tab);
      common.clickOnMenuLinksAndCheckElement(client, Menu.Configure.ShopParameters.shop_parameters_menu, Menu.Configure.ShopParameters.order_settings_submenu, PagesForm.ShopParameters.order_settings_form, "Order settings");
      common.clickOnMenuLinksAndCheckElement(client, Menu.Configure.ShopParameters.shop_parameters_menu, Menu.Configure.ShopParameters.order_settings_submenu, PagesForm.ShopParameters.statuses_form, "Order settings", "Statuses", 0, Menu.Configure.ShopParameters.statuses_tab);
      common.clickOnMenuLinksAndCheckElement(client, Menu.Configure.ShopParameters.shop_parameters_menu, Menu.Configure.ShopParameters.product_settings_submenu, PagesForm.ShopParameters.product_settings_form, "Product Parameters");
      common.clickOnMenuLinksAndCheckElement(client, Menu.Configure.ShopParameters.shop_parameters_menu, Menu.Configure.ShopParameters.customer_settings_submenu, PagesForm.ShopParameters.customers_form, "Customer Parameters", "Customers");
      common.clickOnMenuLinksAndCheckElement(client, Menu.Configure.ShopParameters.shop_parameters_menu, Menu.Configure.ShopParameters.customer_settings_submenu, PagesForm.ShopParameters.groups_form, "Customer Parameters", "Groups", 0, Menu.Configure.ShopParameters.groups_tab);
      common.clickOnMenuLinksAndCheckElement(client, Menu.Configure.ShopParameters.shop_parameters_menu, Menu.Configure.ShopParameters.customer_settings_submenu, PagesForm.ShopParameters.titles_form, "Customer Parameters", "Titles", 0, Menu.Configure.ShopParameters.titles_tab);
      common.clickOnMenuLinksAndCheckElement(client, Menu.Configure.ShopParameters.shop_parameters_menu, Menu.Configure.ShopParameters.contact_submenu, PagesForm.ShopParameters.contact_form, "Contact", "Contacts");
      common.clickOnMenuLinksAndCheckElement(client, Menu.Configure.ShopParameters.shop_parameters_menu, Menu.Configure.ShopParameters.contact_submenu, PagesForm.ShopParameters.stores_form, "Contact", "Stores", 0, Menu.Configure.ShopParameters.stores_tab);
      common.clickOnMenuLinksAndCheckElement(client, Menu.Configure.ShopParameters.shop_parameters_menu, Menu.Configure.ShopParameters.traffic_seo_submenu, PagesForm.ShopParameters.seo_url_showcase_card, "Traffic & SEO", "SEO & URLs");
      common.clickOnMenuLinksAndCheckElement(client, Menu.Configure.ShopParameters.shop_parameters_menu, Menu.Configure.ShopParameters.traffic_seo_submenu, PagesForm.ShopParameters.search_engine_form, "Traffic & SEO", "Search Engines", 0, Menu.Configure.ShopParameters.search_engines_tab);
      common.clickOnMenuLinksAndCheckElement(client, Menu.Configure.ShopParameters.shop_parameters_menu, Menu.Configure.ShopParameters.traffic_seo_submenu, PagesForm.ShopParameters.index_form, "Traffic & SEO", "Referres", 0, Menu.Configure.ShopParameters.referrers_tab);
      common.clickOnMenuLinksAndCheckElement(client, Menu.Configure.ShopParameters.shop_parameters_menu, Menu.Configure.ShopParameters.search_submenu, PagesForm.ShopParameters.alias_form, "Search", "Search box");
      common.clickOnMenuLinksAndCheckElement(client, Menu.Configure.ShopParameters.shop_parameters_menu, Menu.Configure.ShopParameters.search_submenu, PagesForm.ShopParameters.tags_form, "Search", "Tags", 0, Menu.Configure.ShopParameters.tags_tab);
      test('should check if "Merchant Expertise" menu is existing', () => {
        return promise
          .then(() => client.isVisible(Menu.Configure.ShopParameters.merchant_expertise_submenu))
          .then(() => {
            if (isVisible) {
              common.clickOnMenuLinksAndCheckElement(client, Menu.Configure.ShopParameters.shop_parameters_menu, Menu.Configure.ShopParameters.merchant_expertise_submenu, PagesForm.ShopParameters.gamification_box, "Merchant Expertise", "Gamification")
            }
          });
      });
    }, 'common_client');
    scenario('Check all the menu links of "Advanced parameters" in the Back Office',
      client => {
        test('should click on "Shop Parameters" menu', () => {
          return promise
            .then(() => client.waitForExistAndClick(Menu.Configure.ShopParameters.shop_parameters_menu))
            .then(() => client.pause(2000));
        });
        common.clickOnMenuLinksAndCheckElement(client, Menu.Configure.AdvancedParameters.advanced_parameters_menu, Menu.Configure.AdvancedParameters.information_submenu, PagesForm.AdvancedParameters.check_configuration_box, "Informations");
        common.clickOnMenuLinksAndCheckElement(client, Menu.Configure.AdvancedParameters.advanced_parameters_menu, Menu.Configure.AdvancedParameters.performance_submenu, PagesForm.AdvancedParameters.debug_mode_button, "Performance");
        common.clickOnMenuLinksAndCheckElement(client, Menu.Configure.AdvancedParameters.advanced_parameters_menu, Menu.Configure.AdvancedParameters.administration_submenu, PagesForm.AdvancedParameters.administration_form, "Administration");
        common.clickOnMenuLinksAndCheckElement(client, Menu.Configure.AdvancedParameters.advanced_parameters_menu, Menu.Configure.AdvancedParameters.email_submenu, PagesForm.AdvancedParameters.mail_form, "Email");
        common.clickOnMenuLinksAndCheckElement(client, Menu.Configure.AdvancedParameters.advanced_parameters_menu, Menu.Configure.AdvancedParameters.import_submenu, PagesForm.AdvancedParameters.preview_import_form, "Import");
        common.clickOnMenuLinksAndCheckElement(client, Menu.Configure.AdvancedParameters.advanced_parameters_menu, Menu.Configure.AdvancedParameters.team_submenu, PagesForm.AdvancedParameters.employee_form, "Team", "Employees");
        common.clickOnMenuLinksAndCheckElement(client, Menu.Configure.AdvancedParameters.advanced_parameters_menu, Menu.Configure.AdvancedParameters.team_submenu, PagesForm.AdvancedParameters.profiles_form, "Team", "Profiles", 0, Menu.Configure.AdvancedParameters.profiles_tab);
        common.clickOnMenuLinksAndCheckElement(client, Menu.Configure.AdvancedParameters.advanced_parameters_menu, Menu.Configure.AdvancedParameters.team_submenu, PagesForm.AdvancedParameters.permissions_form, "Team", "Permissions", 0, Menu.Configure.AdvancedParameters.permissions_tab);
        common.clickOnMenuLinksAndCheckElement(client, Menu.Configure.AdvancedParameters.advanced_parameters_menu, Menu.Configure.AdvancedParameters.database_submenu, PagesForm.AdvancedParameters.request_sql_form, "Database", "Sql Manager");
        common.clickOnMenuLinksAndCheckElement(client, Menu.Configure.AdvancedParameters.advanced_parameters_menu, Menu.Configure.AdvancedParameters.database_submenu, PagesForm.AdvancedParameters.backup_filter_form, "Database", "DB Backup", 0, Menu.Configure.AdvancedParameters.db_backup_tab);
        common.clickOnMenuLinksAndCheckElement(client, Menu.Configure.AdvancedParameters.advanced_parameters_menu, Menu.Configure.AdvancedParameters.logs_submenu, PagesForm.AdvancedParameters.log_form, "Logs");
        common.clickOnMenuLinksAndCheckElement(client, Menu.Configure.AdvancedParameters.advanced_parameters_menu, Menu.Configure.AdvancedParameters.webservice_submenu, PagesForm.AdvancedParameters.webservice_form, "WebService");
      }, 'common_client');
  }, 'common_client');
}, 'common_client', true);
