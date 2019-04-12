module.exports = {
  Menu: {
    dashboard_menu: '#tab-AdminDashboard a',
    Sell: {
      Orders: {
        orders_menu: '#subtab-AdminParentOrders',
        orders_submenu: '#subtab-AdminOrders a',
        invoices_submenu: '#subtab-AdminInvoices a',
        credit_slips_submenu: '#subtab-AdminSlip a',
        delivery_slips_submenu: '#subtab-AdminDeliverySlip a',
        shopping_carts_submenu: '#subtab-AdminCarts a'
      },
      Catalog: {
        catalog_menu: '#subtab-AdminCatalog',
        catalog_menu_href: '#subtab-AdminCatalog a',
        products_submenu: '#subtab-AdminProducts a',
        category_submenu: '#subtab-AdminCategories a',
        monitoring_submenu: '#subtab-AdminTracking a',
        attributes_features_submenu: '#subtab-AdminParentAttributesGroups a',
        feature_tab: "#subtab-AdminFeatures",
        manufacturers_submenu: '#subtab-AdminParentManufacturers a',
        supplier_tab: '#subtab-AdminSuppliers ',
        files_submenu: '#subtab-AdminAttachments a',
        discounts_submenu: '#subtab-AdminParentCartRules a',
        catalog_price_rules_tab: '#subtab-AdminSpecificPriceRule a',
        stocks_submenu: '#subtab-AdminStockManagement a',
        stock_tab: '#head_tabs ul li:nth-child(1) a:not([href*="movement"])',
        movement_tab: '#head_tabs ul a[href*="movement"]'
      },
      Customers: {
        customers_menu: '#subtab-AdminParentCustomer',
        customers_submenu: '#subtab-AdminCustomers a',
        addresses_submenu: '#subtab-AdminAddresses a'
      },
      CustomerService: {
        customer_service_menu: '#subtab-AdminParentCustomerThreads > a',
        customer_service_submenu: '#subtab-AdminCustomerThreads a',
        order_messages_submenu: '#subtab-AdminOrderMessage a',
        merchandise_returns_submenu: '#subtab-AdminReturn a'
      },
      Stats: {
        stats_menu: '#subtab-AdminStats a'
      }
    },
    Improve: {
      Modules: {
        modules_menu: '#subtab-AdminParentModulesSf',
        modules_manager_submenu: '#subtab-AdminModulesSf > a',
        modules_catalog_submenu: '#subtab-AdminParentModulesCatalog > a',
        installed_modules_tabs: '#subtab-AdminModulesManage',
        catalog_tab: '#subtab-AdminModulesCatalog | #subtab-AdminPsMboModule ',
        modules_selections_submenu: '#subtab-AdminAddonsCatalog a',
        selection_tab: '#subtab-AdminAddonsCatalog',
        alerts_subTab: '#subtab-AdminModulesNotifications ',
        updates_subTab: '#subtab-AdminModulesUpdates ',
      },
      Design: {
        design_menu: '#subtab-AdminParentThemes  a',
        theme_logo_submenu: '#subtab-AdminThemesParent a',
        theme_catalog_submenu: '#subtab-AdminPsMboTheme a, #subtab-AdminThemesCatalog a',
        pages_submenu: '#subtab-AdminCmsContent a',
        positions_submenu: '#subtab-AdminModulesPositions a',
        image_settings_submenu: '#subtab-AdminImages a',
        link_widget_submenu: '#subtab-AdminLinkWidget a',
        homepage_configuration_subTab: '#subtab-AdminPsThemeCustoConfiguration ',
        advanced_customization_subTab: '#subtab-AdminPsThemeCustoAdvanced ',
      },
      Shipping: {
        shipping_menu: '#subtab-AdminParentShipping a',
        carriers_submenu: '#subtab-AdminCarriers a',
        preferences_submenu: '#subtab-AdminShipping a'
      },
      Payment: {
        payment_menu: '#subtab-AdminParentPayment  a',
        payment_methods_submenu: '#subtab-AdminPayment a',
        preferences_submenu: '#subtab-AdminPaymentPreferences a'
      },
      International: {
        international_menu: '#subtab-AdminInternational',
        localization_submenu: '#subtab-AdminParentLocalization a',
        languages_tab: '#subtab-AdminLanguages ',
        currencies_tab: '#subtab-AdminCurrencies',
        geolocation_tab: '#subtab-AdminGeolocation ',
        locations_submenu: '#subtab-AdminParentCountries a',
        countries_tab: '#subtab-AdminCountries ',
        states_tab: '#subtab-AdminStates ',
        taxes_submenu: '#subtab-AdminParentTaxes a',
        taxe_rules_tab: '#subtab-AdminTaxRulesGroup',
        translations_submenu: '#subtab-AdminTranslations a'
      }
    },
    Configure: {
      ShopParameters: {
        shop_parameters_menu: '#subtab-ShopParameters',
        general_submenu: '#subtab-AdminParentPreferences a',
        maintenance_tab: '#subtab-AdminMaintenance ',
        order_settings_submenu: '#subtab-AdminParentOrderPreferences a',
        statuses_tab: '#subtab-AdminStatuses ',
        product_settings_submenu: '#subtab-AdminPPreferences a',
        customer_settings_submenu: '#subtab-AdminParentCustomerPreferences a',
        groups_tab: '#subtab-AdminGroup',
        titles_tab: '#subtab-AdminGenders',
        contact_submenu: '#subtab-AdminParentStores a',
        stores_tab: '#subtab-AdminStores',
        search_engines_tab: '#subtab-AdminSearchEngines ',
        referrers_tab: '#subtab-AdminReferrers ',
        search_submenu: '#subtab-AdminParentSearchConf a',
        tags_tab: '#subtab-AdminTags ',
        merchant_expertise_submenu: '#subtab-AdminGamification a',
        traffic_seo_submenu: '#subtab-AdminParentMeta a',
      },
      AdvancedParameters: {
        advanced_parameters_menu: '#subtab-AdminAdvancedParameters',
        information_submenu: '#subtab-AdminInformation a',
        performance_submenu: '#subtab-AdminPerformance a',
        administration_submenu: '#subtab-AdminAdminPreferences a',
        email_submenu: '#subtab-AdminEmails a',
        import_submenu: '#subtab-AdminImport a',
        team_submenu: '#subtab-AdminParentEmployees a',
        profiles_tab: '#subtab-AdminProfiles ',
        permissions_tab: '#subtab-AdminAccess ',
        database_submenu: '#subtab-AdminParentRequestSql a',
        logs_submenu: '#subtab-AdminLogs a',
        webservice_submenu: '#subtab-AdminWebservice a',
        multistore_submenu: '#subtab-AdminShopGroup a',
        db_backup_tab: '#subtab-AdminBackup',
      }
    }
  }
};