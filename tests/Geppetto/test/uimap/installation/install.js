module.exports = {
  InstallPage: {
    Common: {
      installation_next_button: '#btNext'
    },
    StepOne: {
      installation_language_installation_select: '#langList'
    },
    StepTwo: {
      installation_agree_terms_and_conditions_checkbox: '#set_license'
    },
    StepThree: {
      installation_compatibility_green_box: '#sheet_system > h3.okBlock , #sheet_ > h3.okBlock'
    },
    StepFour: {
      installation_shop_name_input_field: '#infosShop',
      installation_country_list_select: '#infosCountry_chosen',
      installation_country_search_input_field: '#infosCountry_chosen div[class*=chosen-search] > input', //TODO
      installation_account_first_name_input_field: '#infosFirstname',
      installation_account_last_name_input_field: '#infosName',
      installation_account_email_input_field: '#infosEmail',
      installation_account_password_input_field: '#infosPassword',
      installation_re_type_password_input_field: '#infosPasswordRepeat'
    },
    StepFive: {
      installation_database_server_address_input_field: '#dbServer',
      installation_database_name_input_field: '#dbName',
      installation_database_login_input_field: '#dbLogin',
      installation_database_password_input_field: '#dbPassword',
      installation_test_database_connection_button: '#btTestDB',
      installation_database_connection_box: '#btCreateDB',
      database_created_box: '#dbResultCheck' //@TODO
    },
    StepSix: {
      installation_success_create_file_parameters_title: '#process_step_generateSettingsFile[class="process_step success"]',
      installation_success_create_database_tables_title: '#process_step_installDatabase[class="process_step success"]',
      installation_success_create_default_shop_language_title: '#process_step_installDefaultData[class="process_step success"]',
      installation_success_populate_database_tables_title: '#process_step_populateDatabase[class="process_step success"]',
      installation_success_configure_shop_information_title: '#process_step_configureShop[class="process_step success"]',
      installation_success_install_demonstration_data_title: '#process_step_installFixtures[class="process_step success"]',
      installation_success_install_modules_title: '#process_step_installModules[class="process_step success"]',
      installation_success_install_addons_modules_title: '#process_step_installModulesAddons[class="process_step success"]',
      installation_success_install_theme_title: '#process_step_installTheme[class="process_step success"]',
      installation_installation_finished_title: '#install_process_success > div:nth-of-type(1) > h2' //@TODO
    }
  }
};