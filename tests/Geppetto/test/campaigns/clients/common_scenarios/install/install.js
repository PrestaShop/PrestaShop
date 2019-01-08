const {InstallPage} = require('../../../../uimap/installation/install');

module.exports = {
  installShop(language = '', selectedValue, close = false) {
    // do some setup
    scenario('Install new shop', client => {
      const lang = language === '' ? global.language : language;
      if (selectedValue.includes(lang)) {
        // Select language
        scenario('Step 1 : Choose your language', client => {
          const lang = language === '' ? global.language : language;
          test('should choose the language "' + lang.toUpperCase() + '" from the list', async () => await client.waitForAndSelect(InstallPage.StepOne.installation_language_installation_select, lang, 2000));
          test('should click on "Next" button', () => client.waitForAndClick(InstallPage.Common.installation_next_button));
        }, 'common_client');
        // Accept the terms and conditions
        scenario('Step 2 : License agreements', client => {
          test('should accept the terms and conditions', () => client.waitForAndClick(InstallPage.StepTwo.installation_agree_terms_and_conditions_checkbox));
          test('should click on "Next" button', () => client.waitForAndClick(InstallPage.Common.installation_next_button));
        }, 'common_client');
        // Checking prestashop compatibility
        scenario('Step 3 : System compatibility', client => {
          test('should check that the "System compatibility" is in green', () => client.checkAttributeValue(InstallPage.StepThree.installation_compatibility_green_box, 'class', 'okBlock', 'equal', 7000));
          test('should click on "Next" button', () => client.waitForAndClick(InstallPage.Common.installation_next_button));
        }, 'common_client');
        //  Fill the store information form
        scenario('Step 4 : Store information', client => {
          test('should set the "Name" input of shop', () => client.waitForAndType(InstallPage.StepFour.installation_shop_name_input_field, 'puppeteerDemo', 2000));
          test('should choose the "Country" from the dropdown list', async () => {
            await client.waitForAndClick(InstallPage.StepFour.installation_country_list_select);
            await client.waitForAndType(InstallPage.StepFour.installation_country_search_input_field, global.country);
            await client.waitForAndClick(InstallPage.StepFour.installation_country_list_select);
          });
          test('should set the "Firstname" input', () => client.waitForAndType(InstallPage.StepFour.installation_account_first_name_input_field, global.firstName, 2000));
          test('should set the "Lastname" input', () => client.waitForAndType(InstallPage.StepFour.installation_account_last_name_input_field, global.lastName, 2000));
          test('should set the "Email" input', () => client.waitForAndType(InstallPage.StepFour.installation_account_email_input_field, global.email, 2000));
          test('should set the "Password" input', () => client.waitForAndType(InstallPage.StepFour.installation_account_password_input_field, global.password, 2000));
          test('should set the "Confirm password" input', () => client.waitForAndType(InstallPage.StepFour.installation_re_type_password_input_field, global.password, 2000));
          test('should click on "Next" button', () => client.waitForAndClick(InstallPage.Common.installation_next_button));
        }, 'common_client');
        // Fill the database configuration form
        scenario('Step 5 : System configuration', client => {
          test('should set the "Database server" input', () => client.waitForAndSetValue(InstallPage.StepFive.installation_database_server_address_input_field, global.dbServer, 3000));
          test('should set the "Database name" input', () => client.waitForAndSetValue(InstallPage.StepFive.installation_database_name_input_field, 'database' + global.dateTime, 1000));
          test('should set the "Database user" input', () => client.waitForAndSetValue(InstallPage.StepFive.installation_database_login_input_field, global.dbUser, 1000));
          test('should set the "Database password" input', () => client.waitForAndSetValue(InstallPage.StepFive.installation_database_password_input_field, global.dbPassword, 1000));
          test('should click on "Test your database connection now!" button', () => client.waitForAndClick(InstallPage.StepFive.installation_test_database_connection_button));
          test('should click on "Create database" button', () => client.waitForAndClick(InstallPage.StepFive.installation_database_connection_box, 3000));
          test('should check that the "Database" is well created', () => client.checkAttributeValue(InstallPage.StepFive.database_created_box, 'class', 'okBlock', 'equal', 4000));
          test('should click on "Next" button', () => client.waitForAndClick(InstallPage.Common.installation_next_button, 2000));
        }, 'common_client');
        //  The installation is started
        scenario('Step 6 : Store installation', client => {
          test('should check that the "Create file parameters" is in green', () => client.waitFor(InstallPage.StepSix.installation_success_create_file_parameters_title, {timeout: 360000}));
          test('should check that the "Create database tables" is in green', () => client.waitFor(InstallPage.StepSix.installation_success_create_database_tables_title, {timeout: 360000}));
          test('should check that the "Create default shop and languages" is in green', () => client.waitFor(InstallPage.StepSix.installation_success_create_default_shop_language_title, {timeout: 360000}));
          test('should check that the "Populate database tables" is in green', () => client.waitFor(InstallPage.StepSix.installation_success_populate_database_tables_title, {timeout: 360000}));
          test('should check that the "Configure shop information" is in green', () => client.waitFor(InstallPage.StepSix.installation_success_configure_shop_information_title, {timeout: 360000}));
          test('should check that the "Install demonstration data" is in green', () => client.waitFor(InstallPage.StepSix.installation_success_install_demonstration_data_title, {timeout: 360000}));
          test('should check that the "Install modules" is in green', () => client.waitFor(InstallPage.StepSix.installation_success_install_modules_title, {timeout: 360000}));
          test('should check that the "Install addons modules" is in green', () => client.waitFor(InstallPage.StepSix.installation_success_install_addons_modules_title, {timeout: 360000}));
          test('should check that the "Install theme" is in green', () => client.waitFor(InstallPage.StepSix.installation_success_install_theme_title, {timeout: 360000}));
          test('should check that the "Finish installation" is in green', () => client.waitFor(InstallPage.StepSix.installation_installation_finished_title, {timeout: 360000}));
        }, 'common_client');
      }
      else {
        test('should check the selected language', async () => {
          await client.waitFor(4000);
          await expect(selectedValue.includes(lang), 'Failed to select the "' + lang.toUpperCase() + '" language !').to.be.true;
        });
      }
    }, 'common_client', close);
  },

  async installStepOne(language = '', selectedValue, client) {
    const lang = language === await '' ? global.language : language;
    global.continueInstallation = true;
    if (selectedValue.includes(lang)) {
      await client.waitForAndSelect(InstallPage.StepOne.installation_language_installation_select, lang, 2000);
      await client.waitForAndClick(InstallPage.Common.installation_next_button);
    }
    else {
      await client.waitFor(4000);
      global.continueInstallation = false;
      await expect(selectedValue.includes(lang), 'Failed to select the "' + lang.toUpperCase() + '" language !').to.be.true;
    }
  },

  async installStepTwo(client) {
    if (global.continueInstallation) {
      await client.waitForAndClick(InstallPage.StepTwo.installation_agree_terms_and_conditions_checkbox);
      await client.waitForAndClick(InstallPage.Common.installation_next_button);
    }
  }
  ,
  async installStepThree(client) {
    if (global.continueInstallation) {
      await client.checkAttributeValue(InstallPage.StepThree.installation_compatibility_green_box, 'class', 'okBlock', 'equal', 4000);
      await client.waitForAndClick(InstallPage.Common.installation_next_button, 3000);
    }
  }
  ,
  async installStepFour(client) {
    if (global.continueInstallation) {
      await client.waitForAndType(InstallPage.StepFour.installation_shop_name_input_field, 'puppeteerDemo', 2000);
      await client.waitForAndClick(InstallPage.StepFour.installation_country_list_select);
      await client.waitForAndType(InstallPage.StepFour.installation_country_search_input_field, global.country);
      await client.waitForAndClick(InstallPage.StepFour.installation_country_list_select, 1000);
      await client.waitForAndType(InstallPage.StepFour.installation_account_first_name_input_field, global.firstName, 3000);
      await client.waitForAndType(InstallPage.StepFour.installation_account_last_name_input_field, global.lastName, 2000);
      await client.waitForAndType(InstallPage.StepFour.installation_account_email_input_field, global.email, 2000);
      await client.waitForAndType(InstallPage.StepFour.installation_account_password_input_field, global.password, 2000);
      await client.waitForAndType(InstallPage.StepFour.installation_re_type_password_input_field, global.password, 2000);
      await client.waitForAndClick(InstallPage.Common.installation_next_button);
    }
  },

  async installStepFive(client, dbName = '') {
    if (global.continueInstallation) {
      await client.waitForAndSetValue(InstallPage.StepFive.installation_database_server_address_input_field, global.dbServer, 3000);
      await client.waitForAndSetValue(InstallPage.StepFive.installation_database_name_input_field, 'database' + dbName + global.dateTime, 1000);
      await client.waitForAndSetValue(InstallPage.StepFive.installation_database_login_input_field, global.dbUser, 1000);
      await client.waitForAndSetValue(InstallPage.StepFive.installation_database_password_input_field, global.dbPassword, 1000);
      await client.waitForAndClick(InstallPage.StepFive.installation_test_database_connection_button);
      await client.waitForAndClick(InstallPage.StepFive.installation_database_connection_box, 3000);
      await client.checkAttributeValue(InstallPage.StepFive.database_created_box, 'class', 'okBlock', 'equal', 5000);
      await client.waitForAndClick(InstallPage.Common.installation_next_button, 2000);
    }
  },

  async installStepSix(client) {
    if (global.continueInstallation) {
      await client.waitFor(InstallPage.StepSix.installation_success_create_file_parameters_title, {timeout: 400000});
      await client.waitFor(InstallPage.StepSix.installation_success_create_database_tables_title, {timeout: 400000});
      await client.waitFor(InstallPage.StepSix.installation_success_create_default_shop_language_title, {timeout: 400000});
      await client.waitFor(InstallPage.StepSix.installation_success_populate_database_tables_title, {timeout: 400000});
      await client.waitFor(InstallPage.StepSix.installation_success_configure_shop_information_title, {timeout: 400000});
      await client.waitFor(InstallPage.StepSix.installation_success_install_demonstration_data_title, {timeout: 400000});
      await client.waitFor(InstallPage.StepSix.installation_success_install_modules_title, {timeout: 400000});
      await client.waitFor(InstallPage.StepSix.installation_success_install_addons_modules_title, {timeout: 400000});
      await client.waitFor(InstallPage.StepSix.installation_success_install_theme_title, {timeout: 400000});
      await client.waitFor(InstallPage.StepSix.installation_installation_finished_title, {timeout: 400000});
    }
  }
};