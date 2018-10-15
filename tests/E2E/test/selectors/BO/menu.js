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
        feature_tab: '//*[@id="subtab-AdminFeatures"]',
        manufacturers_submenu: '#subtab-AdminParentManufacturers',
        supplier_tab: '//*[@id="subtab-AdminSuppliers"]',
        files_submenu: '#subtab-AdminAttachments',
        discounts_submenu: '#subtab-AdminParentCartRules',
        catalog_price_rules_tab: '#subtab-AdminSpecificPriceRule',
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
        stats_menu: '//*[@id="subtab-AdminStats"]/a'
      }
    },
    Improve: {
      Modules: {
        modules_menu: '//*[@id="subtab-AdminParentModulesSf"]/a',
        modules_services_submenu: '#subtab-AdminModulesSf',
        modules_catalog: '#subtab-AdminParentModulesCatalog > a',
        installed_modules_tabs: '//*[@id="subtab-AdminModulesManage"]',
        notifications_tabs: '//*[@id="subtab-AdminModulesNotifications"]',
        selection_tab: '//*[@id="subtab-AdminModulesCatalog"]',
        modules_catalog_submenu: '#subtab-AdminParentModulesCatalog'
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
        languages_tab: '//*[@id="subtab-AdminLanguages"]',
        currencies_tab: '//*[@id="subtab-AdminCurrencies"]',
        geolocation_tab: '//*[@id="subtab-AdminGeolocation"]',
        locations_submenu: '#subtab-AdminParentCountries',
        countries_tab: '//*[@id="subtab-AdminCountries"]',
        states_tab: '//*[@id="subtab-AdminStates"]',
        taxes_submenu: '#subtab-AdminParentTaxes',
        taxe_rules_tab: '#subtab-AdminTaxRulesGroup',
        translations_submenu: '#subtab-AdminTranslations'
      }
    },
    Configure: {
      ShopParameters: {
        shop_parameters_menu: '//*[@id="subtab-ShopParameters"]/a',
        general_submenu: '#subtab-AdminParentPreferences',
        maintenance_tab: '//*[@id="subtab-AdminMaintenance"]',
        order_settings_submenu: '#subtab-AdminParentOrderPreferences',
        statuses_tab: '//*[@id="subtab-AdminStatuses"]',
        product_settings_submenu: '#subtab-AdminPPreferences',
        customer_settings_submenu: '#subtab-AdminParentCustomerPreferences',
        groups_tab: '//*[@id="subtab-AdminGroups"]',
        titles_tab: '//*[@id="subtab-AdminGenders"]',
        contact_submenu: '#subtab-AdminParentStores',
        stores_tab: '//*[@id="main"]//div[@class="page-head-tabs"]//a[text()="Stores"]',
        search_engines_tab: '//*[@id="main"]//div[@class="page-head-tabs"]//a[text()="Search Engines"]',
        referrers_tab: '//*[@id="main"]//div[@class="page-head-tabs"]//a[text()="Referrers"]',
        search_submenu: '#subtab-AdminParentSearchConf',
        tags_tab: '//*[@id="subtab-AdminTags"]',
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
        profiles_tab: '//*[@id="subtab-AdminProfiles"]',
        permissions_tab: '//*[@id="subtab-AdminAccess"]',
        database_submenu: '#subtab-AdminParentRequestSql',
        logs_submenu: '#subtab-AdminLogs',
        webservice_submenu: '#subtab-AdminWebservice',
        multistore_submenu: '#subtab-AdminShopGroup'
      }
    }
  }
};