module.exports = {
  Installation:{
    language_select: '//*[@id="langList"]',
    next_step_button:'//*[@id="btNext"]',
    agree_terms_and_conditions_button:'//*[@id="sheet_"]/div[3]/div/label',
    compatibility_green_box:'//*[@id="sheet_"]/h3',
    shop_name_input: '//*[@id="infosShop"]',
    country_select: '//*[@id="infosCountry_chosen"]',

    search_country_input:'//*[@id="infosCountry_chosen"]//div[contains(@class,"chosen-search")]/input',

    country_france_option: '//*[@id="infosCountry_chosen"]/div/ul/li',
    first_name_input: '//*[@id="infosFirstname"]',
    last_name_input: '//*[@id="infosName"]',
    email_address_input: '//*[@id="infosEmail"]',
    shop_password_input: '//*[@id="infosPassword"]',
    retype_password_input: '//*[@id="infosPasswordRepeat"]',
    database_address_input: '//*[@id="dbServer"]',
    database_name_input: '//*[@id="dbName"]',
    database_login_input: '//*[@id="dbLogin"]',
    database_password_input: '//*[@id="dbPassword"]',
    test_connection_button: '#btTestDB',
    check_data_base:'//*[@id="dbResultCheck"]',
    create_DB_button:'//*[@id="btCreateDB"]',
    create_file_parameter_step: '//li[@id="process_step_generateSettingsFile" and @class="process_step success"]',
    create_database_step: '//li[@id="process_step_installDatabase" and @class="process_step success"]',
    create_default_shop_step: '//li[@id="process_step_installDefaultData" and @class="process_step success"]',
    create_database_table_step: '//li[@id="process_step_populateDatabase" and @class="process_step success"]',
    create_shop_informations_step: '//li[@id="process_step_configureShop" and @class="process_step success"]',
    create_demonstration_data_step: '//li[@id="process_step_installFixtures" and @class="process_step success"]',
    install_module_step: '//li[@id="process_step_installModules" and @class="process_step success"]',
    install_addons_modules_step: '//li[@id="process_step_installModulesAddons" and @class="process_step success"]',
    install_theme_step: '//li[@id="process_step_installTheme" and @class="process_step success"]',
    finish_step: '//*[@id="install_process_success"]/div[1]/h2',
    finished_installation_msg:'//*[@id="install_process_success"]/div[1]/h2'
  }
};
