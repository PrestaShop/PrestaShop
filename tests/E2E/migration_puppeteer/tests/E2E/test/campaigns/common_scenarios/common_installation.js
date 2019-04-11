let promise = Promise.resolve();

module.exports = {
  prestaShopInstall: function (selector, language, country) {
    scenario('Step 1 : Choosing language', client => {
      test('should choose "' + language + '" language', () => client.waitAndSelectByValue(selector.language_select, language, 2000));
      test('should click on "Next" button', () => client.waitForVisibleAndClick(selector.next_step_button));
    }, 'installation');
    scenario('Step 2 : Agreeing license agreements', client => {
      test('should click on "I agree to the above terms and conditions " button', () => client.waitForExistAndClick(selector.agree_terms_and_conditions_button));
      test('should click on "Next" button', () => client.waitForVisibleAndClick(selector.next_step_button));
    }, 'installation');
    scenario('Step 3 : Checking system compatibility', client => {
      test('should check if step 3 is skipped', () => client.isVisible(selector.refresh_button, 3000));
      test('should check the test compatibility green box', async () => {
        if (global.isVisible) {
          await client.isExisting(selector.compatibility_green_box);
          await client.waitForVisibleAndClick(selector.next_step_button, 2000);
        } else {
          await client.pause(0);
        }
      });
    }, 'installation');
    scenario('Step 4 : Inserting the shop information', client => {
      test('should set the "Shop name" input', () => client.waitAndSetValue(selector.shop_name_input, "prestashop_demo", 2000));
      test('should set the "Country" input', () => {
        return promise
          .then(() => client.waitForExistAndClick(selector.country_select))
          .then(() => client.waitAndSetValue(selector.search_country_input, country))
          .then(() => client.waitForExistAndClick(selector.country_france_option));
      });
      test('should set the "First name" input', () => client.waitAndSetValue(selector.first_name_input, "demo"));
      test('should set the "Last name" input', () => client.waitAndSetValue(selector.last_name_input, "prestashop"));
      test('should set the "E-mail address" input', () => client.waitAndSetValue(selector.email_address_input, global.adminEmail));
      test('should set the "Shop password" input', () => client.waitAndSetValue(selector.shop_password_input, global.adminPassword));
      test('should set the "Re-type to confirm" input', () => client.waitAndSetValue(selector.retype_password_input, global.adminPassword));
      test('should click on "Next" button', () => client.waitForVisibleAndClick(selector.next_step_button));
    }, 'installation');
    scenario('Step 5 : Setting the BD configuration', client => {
      test('should set the "Database server address" input', () => client.waitAndSetValue(selector.database_address_input, global.db_server, 2000));
      test('should set the "Database name" input', () => client.waitAndSetValue(selector.database_name_input, 'database' + new Date().getTime()));
      test('should set the "Database login" input', () => client.waitAndSetValue(selector.database_login_input, db_user));
      test('should set the "Database password" input', () => {
        if (global.db_empty_password) {
          return promise
            .then(() => client.waitAndSetValue(selector.database_password_input, ""));
        } else {
          return promise
            .then(() => client.waitAndSetValue(selector.database_password_input, db_passwd));
        }
      });
      test('should click on "Test your database connection now!" button', () => client.waitForExistAndClick(selector.test_connection_button));
      test('should check for the connection and click on "Attempt to create the database automatically" button', () => client.dataBaseCreation(selector.create_DB_button));
      test('should check that the Database is created', () => client.waitForVisible(selector.created_DB_bloc));
      test('should click on "Next" button', () => client.waitForExistAndClick(selector.next_step_button));
    }, 'installation');
    scenario('Step 6 : Checking installation', client => {
      test('should create file parameter', () => client.waitForVisible(selector.create_file_parameter_step, {
        visible: true,
        timeout: 360000
      }));
      test('should create database', () => client.waitForVisible(selector.create_database_step, {
        visible: true,
        timeout: 360000
      }));
      test('should create default shop', () => client.waitForVisible(selector.create_default_shop_step, {
        visible: true,
        timeout: 360000
      }));
      test('should create database table', () => client.waitForVisible(selector.create_database_table_step, {
        visible: true,
        timeout: 360000
      }));
      test('should create shop information', () => client.waitForVisible(selector.create_shop_informations_step, {
        visible: true,
        timeout: 360000
      }));
      test('should create demonstration data', () => client.waitForVisible(selector.create_demonstration_data_step, {
        visible: true,
        timeout: 360000
      }));
      test('should create install module', () => client.waitForVisible(selector.install_module_step, {
        visible: true,
        timeout: 360000
      }));
      test('should create addons modules', () => client.waitForVisible(selector.install_addons_modules_step, {
        visible: true,
        timeout: 360000
      }));
      test('should create install theme', () => client.waitForVisible(selector.install_theme_step, {
        visible: true,
        timeout: 360000
      }));
      test('should finish installation', () => client.waitForVisible(selector.finish_step, {
        visible: true,
        timeout: 360000
      }));
    }, 'installation');
    scenario('Step 7 : Checking that installation finished', client => {
      test('should check that the installation is finished!', () => client.isExisting(selector.finished_installation_msg, 2000));
    }, 'installation');
  }
};