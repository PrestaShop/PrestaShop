module.exports = {
  Menu: {
    dashboard_menu: '//*[@id="tab-AdminDashboard"]/a',
    Sell: {
      Orders: {
        orders_menu: '//*[@id="subtab-AdminParentOrders"]/a',
        orders_submenu: '#subtab-AdminOrders',
        invoices_submenu: '#subtab-AdminInvoices',
        credit_slips_submenu: '#subtab-AdminSlip',
        delivery_slips_submenu: '#subtab-AdminDeliverySlip',
        shopping_carts_submenu: '#subtab-AdminCarts'
      },
      Catalog: {
        catalog_menu: '//*[@id="subtab-AdminCatalog"]/a',
        products_submenu: '#subtab-AdminProducts',
        category_submenu: '#subtab-AdminCategories',
        monitoring_submenu: '#subtab-AdminTracking',
        attributes_features_submenu: '#subtab-AdminParentAttributesGroups',
        feature_tab: '//*[@id="content"]//div[@class="page-head-tabs"]//a[text()="Features"]',
        manufacturers_submenu: '#subtab-AdminParentManufacturers',
        supplier_tab: '//*[@id="content"]//div[@class="page-head-tabs"]//a[text()="Suppliers"]',
        files_submenu: '#subtab-AdminAttachments',
        discounts_submenu: '#subtab-AdminParentCartRules',
        catalog_price_rules_tab: '//*[@id="content"]//div[@class="page-head-tabs"]//a[text()="Catalog Price Rules"]',
        stocks_submenu: '#subtab-AdminStockManagement',
        stock_tab: '//*[@id="head_tabs"]//a[text()="Stock"]',
        movement_tab: '//*[@id="head_tabs"]//a[text()="Movements"]'
      },
      Customers: {
        customers_menu: '//*[@id="subtab-AdminParentCustomer"]/a',
        customers_submenu: '#subtab-AdminCustomers',
        addresses_submenu: '#subtab-AdminAddresses'
      },
      CustomerService: {
        customer_service_menu: '//*[@id="subtab-AdminParentCustomerThreads"]/a',
        customer_service_submenu: '#subtab-AdminCustomerThreads',
        order_messages_submenu: '#subtab-AdminOrderMessage',
        merchandise_returns_submenu: '#subtab-AdminReturn'
      },
      Stats: {
        stats_menu: '//*[@id="subtab-AdminStats"]/a',
      }
    },
    Improve: {
      Modules: {
        modules_menu: '//*[@id="subtab-AdminParentModulesSf"]/a',
        modules_services_submenu: '#subtab-AdminModulesSf',
        installed_modules_tabs: '//*[@id="subtab-AdminModulesManage"]',
        notifications_tabs: '//*[@id="subtab-AdminModulesNotifications"]',
        selection_tab: '//*[@id="subtab-AdminModulesCatalog"]',
        modules_catalog_submenu: '#subtab-AdminAddonsCatalog',
      },
      Design: {
        design_menu: '//*[@id="subtab-AdminParentThemes"]/a',
        theme_logo_submenu: '#subtab-AdminThemesParent',
        theme_catalog_submenu: '#subtab-AdminThemesCatalog',
        pages_submenu: '#subtab-AdminCmsContent',
        positions_submenu: '#subtab-AdminModulesPositions',
        image_settings_submenu: '#subtab-AdminImages',
        link_widget_submenu: '#subtab-AdminLinkWidget'
      },
      Shipping: {
        shipping_menu: '//*[@id="subtab-AdminParentShipping"]/a',
        carriers_submenu: '#subtab-AdminCarriers',
        preferences_submenu: '#subtab-AdminShipping'
      },
      Payment: {
        payment_menu: '//*[@id="subtab-AdminParentPayment"]/a',
        payment_methods_submenu: '#subtab-AdminPayment',
        preferences_submenu: '#subtab-AdminPaymentPreferences'
      },
      International: {
        international_menu: '//*[@id="subtab-AdminInternational"]/a',
        localization_submenu: '#subtab-AdminParentLocalization',
        languages_tab: '//*[@id="main"]//div[@class="page-head-tabs"]//a[text()="Languages"]',
        currencies_tab: '//*[@id="main"]//div[@class="page-head-tabs"]//a[text()="Currencies"]',
        geolocation_tab: '//*[@id="main"]//div[@class="page-head-tabs"]//a[text()="Geolocation"]',
        locations_submenu: '#subtab-AdminParentCountries',
        countries_tab: '//*[@id="main"]//div[@class="page-head-tabs"]//a[text()="Countries"]',
        states_tab: '//*[@id="main"]//div[@class="page-head-tabs"]//a[text()="States"]',
        taxes_submenu: '#subtab-AdminParentTaxes',
        taxe_rules_tab: '//*[@id="main"]//div[@class="page-head-tabs"]//a[text()="Tax Rules"]',
        translations_submenu: '#subtab-AdminTranslations'
      }
    },
    Configure: {
      ShopParameters: {
        shop_parameters_menu: '//*[@id="subtab-ShopParameters"]/a',
        general_submenu: '#subtab-AdminParentPreferences',
        maintenance_tab: '//a[text() = "Maintenance"]',
        order_settings_submenu: '#subtab-AdminParentOrderPreferences',
        statuses_tab: '//*[@id="head_tabs"]//a[text()="Statuses"]',
        product_settings_submenu: '#subtab-AdminPPreferences',
        customer_settings_submenu: '#subtab-AdminParentCustomerPreferences',
        groups_tab: '//*[@id="head_tabs"]//a[text()="Groups"]',
        titles_tab: '//*[@id="head_tabs"]//a[text()="Titles"]',
        contact_submenu: '#subtab-AdminParentStores',
        stores_tab: '//*[@id="main"]//div[@class="page-head-tabs"]//a[text()="Stores"]',
        traffic_seo_submenu: '#subtab-AdminParentMeta',
        search_engines_tab: '//*[@id="main"]//div[@class="page-head-tabs"]//a[text()="Search Engines"]',
        referrers_tab: '//*[@id="main"]//div[@class="page-head-tabs"]//a[text()="Referrers"]',
        search_submenu: '#subtab-AdminParentSearchConf',
        tags_tab: '//*[@id="main"]//div[@class="page-head-tabs"]//a[text()="Tags"]',
        merchant_expertise_submenu: '#subtab-AdminGamification'
      },
      AdvancedParameters: {
        advanced_parameters_menu: '//*[@id="subtab-AdminAdvancedParameters"]/a',
        information_submenu: '#subtab-AdminInformation',
        performance_submenu: '#subtab-AdminPerformance',
        administration_submenu: '#subtab-AdminAdminPreferences',
        email_submenu: '#subtab-AdminEmails',
        import_submenu: '#subtab-AdminImport',
        team_submenu: '#subtab-AdminParentEmployees',
        profiles_tab: '//*[@id="main"]//div[@class="page-head-tabs"]//a[text()="Profiles"]',
        permissions_tab: '//*[@id="main"]//div[@class="page-head-tabs"]//a[text()="Permissions"]',
        database_submenu: '#subtab-AdminParentRequestSql',
        logs_submenu: '#subtab-AdminLogs',
        webservice_submenu: '#subtab-AdminWebservice',
        multistore_submenu: '#subtab-AdminShopGroup'
      }
    }
  }
};
