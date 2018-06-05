module.exports = {
  Menu: {
    dashboard_menu: '//*[@id="tab-AdminDashboard"]/a',
    Sell: {
      Orders: {
        orders_menu: '//*[@id="subtab-AdminParentOrders"]/a',
        orders_submenu: '//*[@id="subtab-AdminOrders"]/a',
        invoices_submenu: '//*[@id="subtab-AdminInvoices"]/a',
        credit_slips_submenu: '//*[@id="subtab-AdminSlip"]/a',
        delivery_slips_submenu: '//*[@id="subtab-AdminDeliverySlip"]/a',
        shopping_carts_submenu: '//*[@id="subtab-AdminCarts"]/a'
      },
      Catalog: {
        catalog_menu: '//*[@id="subtab-AdminCatalog"]/a',
        products_submenu: '//*[@id="subtab-AdminProducts"]/a',
        category_submenu: '//*[@id="subtab-AdminCategories"]/a',
        monitoring_submenu: '//*[@id="subtab-AdminTracking"]/a',
        attributes_features_submenu: '//*[@id="subtab-AdminParentAttributesGroups"]/a',
        feature_tab: '//*[@id="content"]//div[@class="page-head-tabs"]//a[text()="Features"]',
        manufacturers_submenu: '//*[@id="subtab-AdminParentManufacturers"]/a',
        supplier_tab: '//*[@id="content"]//div[@class="page-head-tabs"]//a[text()="Suppliers"]',
        files_submenu: '//*[@id="subtab-AdminAttachments"]/a',
        discounts_submenu: '//*[@id="subtab-AdminParentCartRules"]/a',
        catalog_price_rules_tab: '//*[@id="content"]//div[@class="page-head-tabs"]//a[text()="Catalog Price Rules"]',
        stocks_submenu: '//*[@id="subtab-AdminStockManagement"]/a',
        movement_tab: '//*[@id="app"]//a[text()="Movements" and @role="tab"]'
      },
      Customers: {
        customers_menu: '//*[@id="subtab-AdminParentCustomer"]/a',
        customers_submenu: '//*[@id="subtab-AdminCustomers"]/a',
        addresses_submenu: '//*[@id="subtab-AdminAddresses"]/a'
      },
      CustomerService: {
        customer_service_menu: '//*[@id="subtab-AdminParentCustomerThreads"]/a',
        customer_service_submenu: '//*[@id="subtab-AdminCustomerThreads"]/a',
        order_messages_submenu: '//*[@id="subtab-AdminOrderMessage"]/a',
        merchandise_returns_submenu: '//*[@id="subtab-AdminReturn"]/a'
      },
      Stats: {
        stats_menu: '//*[@id="subtab-AdminStats"]/a',
      }
    },
    Improve: {
      Modules: {
        modules_menu: '//*[@id="subtab-AdminParentModulesSf"]/a',
        modules_services_submenu: '//*[@id="subtab-AdminModulesSf"]/a',
        installed_modules_tabs: '//*[@id="main-div"]//div[@class="page-head-tabs"]//a[text()="Installed modules"]',
        notifications_tabs: '//*[@id="main-div"]//div[@class="page-head-tabs"]//a[text()="Notifications  "]',
        modules_catalog_submenu: '//*[@id="subtab-AdminAddonsCatalog"]/a'
      },
      Design: {
        design_menu: '//*[@id="subtab-AdminParentThemes"]/a',
        theme_logo_submenu: '//*[@id="subtab-AdminThemes"]/a',
        theme_catalog_submenu: '//*[@id="subtab-AdminThemesCatalog"]/a',
        pages_submenu: '//*[@id="subtab-AdminCmsContent"]/a',
        positions_submenu: '//*[@id="subtab-AdminModulesPositions"]/a',
        image_settings_submenu: '//*[@id="subtab-AdminImages"]/a',
        link_widget_submenu: '//*[@id="subtab-AdminLinkWidget"]/a'
      },
      Shipping: {
        shipping_menu: '//*[@id="subtab-AdminParentShipping"]/a',
        carriers_submenu: '//*[@id="subtab-AdminCarriers"]/a',
        preferences_submenu: '//*[@id="subtab-AdminShipping"]/a'
      },
      Payment: {
        payment_menu: '//*[@id="subtab-AdminParentPayment"]/a',
        payment_methods_submenu: '//*[@id="subtab-AdminPayment"]/a',
        preferences_submenu: '//*[@id="subtab-AdminPaymentPreferences"]/a'
      },
      International: {
        international_menu: '//*[@id="subtab-AdminInternational"]/a',
        localization_submenu: '//*[@id="subtab-AdminParentLocalization"]/a',
        languages_tab: '//*[@id="main"]//div[@class="page-head-tabs"]//a[text()="Languages"]',
        currencies_tab: '//*[@id="main"]//div[@class="page-head-tabs"]//a[text()="Currencies"]',
        geolocation_tab: '//*[@id="main"]//div[@class="page-head-tabs"]//a[text()="Geolocation"]',
        locations_submenu: '//*[@id="subtab-AdminParentCountries"]/a',
        countries_tab: '//*[@id="main"]//div[@class="page-head-tabs"]//a[text()="Countries"]',
        states_tab: '//*[@id="main"]//div[@class="page-head-tabs"]//a[text()="States"]',
        taxes_submenu: '//*[@id="subtab-AdminParentTaxes"]/a',
        taxe_rules_tab: '//*[@id="main"]//div[@class="page-head-tabs"]//a[text()="Tax Rules"]',
        translations_submenu: '//*[@id="subtab-AdminTranslations"]/a'
      }
    },
    Configure: {
      ShopParameters: {
        shop_parameters_menu: '//*[@id="subtab-ShopParameters"]/a',
        general_submenu: '//*[@id="subtab-AdminParentPreferences"]/a',
        maintenance_tab: '//a[text() = "Maintenance"]',
        order_settings_submenu: '//*[@id="subtab-AdminParentOrderPreferences"]/a',
        statuses_tab: '//*[@id="main"]//div[@class="page-head-tabs"]//a[text()="Statuses"]',
        product_settings_submenu: '//*[@id="subtab-AdminPPreferences"]/a',
        customer_settings_submenu: '//*[@id="subtab-AdminParentCustomerPreferences"]/a',
        groups_tab: '//*[@id="head_tabs"]/a[text()="Groups"]',
        titles_tab: '//*[@id="head_tabs"]/a[text()="Titles"]',
        contact_submenu: '//*[@id="subtab-AdminParentStores"]/a',
        stores_tab: '//*[@id="main"]//div[@class="page-head-tabs"]//a[text()="Stores"]',
        traffic_seo_submenu: '//*[@id="subtab-AdminParentMeta"]/a',
        search_engines_tab: '//*[@id="main"]//div[@class="page-head-tabs"]//a[text()="Search Engines"]',
        referrers_tab: '//*[@id="main"]//div[@class="page-head-tabs"]//a[text()="Referrers"]',
        search_submenu: '//*[@id="subtab-AdminParentSearchConf"]/a',
        tags_tab: '//*[@id="main"]//div[@class="page-head-tabs"]//a[text()="Tags"]',
        merchant_expertise_submenu: '//*[@id="subtab-AdminGamification"]/a'
      },
      AdvancedParameters: {
        advanced_parameters_menu: '//*[@id="subtab-AdminAdvancedParameters"]/a',
        information_submenu: '//*[@id="subtab-AdminInformation"]/a',
        performance_submenu: '//*[@id="subtab-AdminPerformance"]/a',
        administration_submenu: '//*[@id="subtab-AdminAdminPreferences"]/a',
        email_submenu: '//*[@id="subtab-AdminEmails"]/a',
        import_submenu: '//*[@id="subtab-AdminImport"]/a',
        team_submenu: '//*[@id="subtab-AdminParentEmployees"]/a',
        profiles_tab: '//*[@id="main"]//div[@class="page-head-tabs"]//a[text()="Profiles"]',
        permissions_tab: '//*[@id="main"]//div[@class="page-head-tabs"]//a[text()="Permissions"]',
        database_submenu: '//*[@id="subtab-AdminParentRequestSql"]/a',
        logs_submenu: '//*[@id="subtab-AdminLogs"]/a',
        webservice_submenu: '//*[@id="subtab-AdminWebservice"]/a',
        multistore_submenu: '//*[@id="subtab-AdminShopGroup"]/a'
      }
    }
  }
};
