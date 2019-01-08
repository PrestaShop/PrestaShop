module.exports = {
  Menu: {
    dashboard_menu_list: '#tab-AdminDashboard a', //@TODO
    Sell: {
      Orders: {
        orders_menu_link: '#subtab-AdminParentOrders a', //@TODO
        orders_submenu_link: '#subtab-AdminOrders a', //@TODO
        invoices_submenu_link: '#subtab-AdminInvoices a', //@TODO
        credit_slips_submenu_link: '#subtab-AdminSlip a', //@TODO
        delivery_slips_submenu_link: '#subtab-AdminDeliverySlip a', //@TODO
        shopping_carts_submenu_link: '#subtab-AdminCarts a' //@TODO
      },
      Catalog: {
        catalog_menu_link: '#subtab-AdminCatalog a', //@TODO
        products_submenu_link: '#subtab-AdminProducts a',  //@TODO
        categories_submenu_link: '#subtab-AdminCategories a', //@TODO
        monitoring_submenu_link: '#subtab-AdminTracking a', //@TODO
        attributes_features_submenu_link: '#subtab-AdminParentAttributesGroups a', //@TODO
        feature_tab: '#subtab-AdminFeatures',
        brands_suppliers_submenu_link: '#subtab-AdminParentManufacturers a', //@TODO
        supplier_tab: '#subtab-AdminSuppliers',
        files_submenu_link: '#subtab-AdminAttachments a',  //@TODO
        discounts_submenu_link: '#subtab-AdminParentCartRules a', //@TODO
        catalog_price_rules_tab: '#subtab-AdminSpecificPriceRule',
        stocks_submenu_link: '#subtab-AdminStockManagement a', //@TODO
        stock_tab: '#head_tabs li:nth-child(1) a',
        movement_tab: '#head_tabs li:nth-child(2) a'
      },
      Customers: {
        customers_menu_link: '#subtab-AdminParentCustomer a', //@TODO
        customers_submenu_link: '#subtab-AdminCustomers a', //@TODO
        addresses_submenu_link: '#subtab-AdminAddresses a' //@TODO
      },
      CustomerService: {
        customer_service_menu_link: '#subtab-AdminParentCustomerThreads a', //@TODO
        customer_service_submenu_link: '#subtab-AdminCustomerThreads a', //@TODO
        order_messages_submenu_link: '#subtab-AdminOrderMessage a', //@TODO
        merchandise_returns_submenu_link: '#subtab-AdminReturn a' //@TODO
      },
      Stats: {
        stats_menu_link: '#subtab-AdminStats', //@TODO
      }
    },
    Improve: {
      Modules: {
        modules_menu_link: '#subtab-AdminParentModulesSf a', //@TODO
        module_catalog_submenu_link: '#subtab-AdminModulesSf a', //@TODO
        installed_modules_tabs: '#subtab-AdminModulesManage',
        notifications_tabs: '#subtab-AdminModulesNotifications',
        selection_tab: '#subtab-AdminModulesCatalog',
        module_manager_submenu_link: '#subtab-AdminAddonsCatalog a, #subtab-AdminParentModulesCatalog a', //@TODO
      },
      Design: {
        design_menu_link: '#subtab-AdminParentThemes a',  //@TODO
        theme_logo_submenu_link: '#subtab-AdminThemesParent a', //@TODO
        theme_catalog_submenu_link: '#subtab-AdminThemesCatalog a', //@TODO
        pages_submenu_link: '#subtab-AdminCmsContent a', //@TODO
        positions_submenu_link: '#subtab-AdminModulesPositions a', //@TODO
        image_settings_submenu_link: '#subtab-AdminImages a', //@TODO
        link_widget_submenu_link: '#subtab-AdminLinkWidget'  //@TODO
      },
      Shipping: {
        shipping_menu_link: '#subtab-AdminParentShipping a', //@TODO
        carriers_submenu_link: '#subtab-AdminCarriers a', //@TODO
        preferences_submenu_link: '#subtab-AdminShipping a' //@TODO
      },
      Payment: {
        payment_menu_link: '#subtab-AdminParentPayment a', //@TODO
        payment_methods_submenu_link: '#subtab-AdminPayment a', //@TODO
        preferences_submenu_link: '#subtab-AdminPaymentPreferences a' //@TODO
      },
      International: {
        international_menu_link: '#subtab-AdminInternational a',
        localization_submenu_link: '#subtab-AdminParentLocalization a', //@TODO
        languages_tab: '#subtab-AdminLanguages',
        currencies_tab: '#subtab-AdminCurrencies',
        geolocation_tab: '#subtab-AdminGeolocation',
        locations_submenu_link: '#subtab-AdminParentCountries a', //@TODO
        countries_tab: '#subtab-AdminCountries',
        states_tab: '#subtab-AdminStates',
        taxes_submenu_link: '#subtab-AdminParentTaxes a',  //@TODO
        taxe_rules_tab: '#subtab-AdminTaxRulesGroup',
        translations_submenu_link: '#subtab-AdminTranslations a'  //@TODO
      }
    },
    Configure: {
      ShopParameters: {
        shop_parameters_menu_link: '#subtab-ShopParameters a', //@TODO
        general_submenu_link: '#subtab-AdminParentPreferences a', //@TODO
        maintenance_tab: '#subtab-AdminMaintenance',
        order_settings_submenu_link: '#subtab-AdminParentOrderPreferences a', //@TODO
        statuses_tab: '#subtab-AdminStatuses',
        product_settings_submenu_link: '#subtab-AdminPPreferences a', //@TODO
        customer_settings_submenu_link: '#subtab-AdminParentCustomerPreferences a', //@TODO
        groups_tab: '#subtab-AdminGroups',
        titles_tab: '#subtab-AdminGenders',
        contact_submenu_link: '#subtab-AdminParentStores a', //@TODO
        stores_tab: '#subtab-AdminStores',
        traffic_seo_submenu_link: '#subtab-AdminParentMeta a', //@TODO
        search_engines_tab: '#subtab-AdminSearchEngines',
        referrers_tab: '#subtab-AdminReferrers',
        search_submenu_link: '#subtab-AdminParentSearchConf a', //@TODO
        tags_tab: '#subtab-AdminTags',
        merchant_expertise_submenu_link: '#subtab-AdminGamification a' //@TODO
      },
      AdvancedParameters: {
        advanced_parameters_menu_link: '#subtab-AdminAdvancedParameters a', //@TODO
        information_submenu_link: '#subtab-AdminInformation a',  //@TODO
        performance_submenu_link: '#subtab-AdminPerformance a', //@TODO
        administration_submenu_link: '#subtab-AdminAdminPreferences a',  //@TODO
        email_submenu_link: '#subtab-AdminEmails a', //@TODO
        import_submenu_link: '#subtab-AdminImport a', //@TODO
        team_submenu_link: '#subtab-AdminParentEmployees a', //@TODO
        profiles_tab: '#subtab-AdminProfiles',
        permissions_tab: '#subtab-AdminAccess',
        database_submenu_link: '#subtab-AdminParentRequestSql a', //@TODO
        logs_submenu_link: '#subtab-AdminLogs a', //@TODO
        webservice_submenu_link: '#subtab-AdminWebservice a', //@TODO
        multistore_submenu_link: '#subtab-AdminShopGroup a', //@TODO
        db_backup_tab: '#subtab-AdminBackup',
      }
    }
  }
};
