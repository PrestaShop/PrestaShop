const {Installation} = require('../../../selectors/BO/installation');

scenario('The shop installation', client => {
  test('should open the browser', () => client.open());
  test('should log in install page ', () => client.localhost());

  scenario('Step 1 : Choosing language', client => {
    test('should choose english language', () => client.waitAndSelectByValue(Installation.language_select, "en"));
    test('should click on "Next" button', () => client.waitForVisibleAndClick(Installation.next_step_button));
  }, 'installation');

  scenario('Step 2 : Agreeing license agreements', client => {
    test('should click on "I agree to the above terms and conditions " button', () => client.waitForExistAndClick(Installation.agree_terms_and_conditions_button));
    test('should click on "Next" button', () => client.waitForVisibleAndClick(Installation.next_step_button));
  }, 'installation');

  scenario('Step 3 : Checking system compatibility', client => {
    test('should check the test compatibility green box', () => client.checkTextValue(Installation.compatibility_green_box, "PrestaShop compatibility with your system environment has been verified!"));
    test('should click on "Next" button', () => client.waitForVisibleAndClick(Installation.next_step_button));
  }, 'installation');

  scenario('Step 4 : Inserting the shop information', client => {
    test('should set the "Shop name" input', () => client.setNameInput(Installation.shop_name_input, "prestashop_1.7.3.0_beta.1-build.2"));
    test('should set the "Country" input', () => client.waitForExistAndClick(Installation.country_select));
    test('should select "France" country', () => client.waitForExistAndClick(Installation.country_france_option));
    test('should set the "First name" input', () => client.waitAndSetValue(Installation.first_name_input, "demo"));
    test('should set the "Last name name" input', () => client.waitAndSetValue(Installation.last_name_input, "prestashop"));
    test('should set the "E-mail address" input', () => client.waitAndSetValue(Installation.email_address_input, "demo@prestashop.com"));
    test('should set the "Shop password" input', () => client.waitAndSetValue(Installation.shop_password_input, "prestashop_demo"));
    test('should set the "Re-type to confirm" input', () => client.waitAndSetValue(Installation.retype_password_input, "prestashop_demo"));
    test('should click on "Next" button', () => client.waitForVisibleAndClick(Installation.next_step_button));
  }, 'installation');

  scenario('Step 5 : Setting the BD configuration', client => {
    test('should set the "Database server address" input', () => client.setNameInput(Installation.database_address_input, "127.0.0.1"));
    test('should set the "Database name" input', () => client.waitAndSetValue(Installation.database_name_input, "prestashop"+date_time));
    test('should set the "Database login" input', () => client.waitAndSetValue(Installation.database_login_input, "root"));
    test('should set the "Database password" input', () => client.waitAndSetValue(Installation.database_password_input, "sifast2016"));
    test('should click on "Test your database connection now!" button', () => client.waitForExistAndClick(Installation.test_connection_button));
    test('should check for the connection and click on "Attempt to create the database automatically" button', () => client.waitForVisibleAndClick(Installation.create_DB_button));
    test('should check that the Database is created', () => client.waitForVisibleElement(Installation.create_DB_button,'Database is created'));
    test('should click on "Next" button', () => client.goToTheNextPage(Installation.next_step_button));
  }, 'installation');

  scenario('Step 6 : Checking installation', client => {
    test('should create file parameter', () => client.waitForVisibleElement(Installation.create_file_parameter_step));
    test('should create database', () => client.waitForVisibleElement(Installation.create_database_step));
    test('should create default shop', () => client.waitForVisibleElement(Installation.create_default_shop_step));
    test('should create database table', () => client.waitForVisibleElement(Installation.create_database_table_step));
    test('should create shop information', () => client.waitForVisibleElement(Installation.create_shop_informations_step));
    test('should create demonstration data', () => client.waitForVisibleElement(Installation.create_demonstration_data_step,140000));
    test('should create install module', () => client.waitForVisibleElement(Installation.install_module_step, 140000));
    test('should create addons modules', () => client.waitForVisibleElement(Installation.install_addons_modules_step));
    test('should create install theme', () => client.waitForVisibleElement(Installation.install_theme_step));
    test('should finish installation', () => client.waitForVisibleElement(Installation.finish_step));
  }, 'installation');

  scenario('Step 7 : Checking that installation finished', client => {
    test('should check that the installation is finished!', () => client.checkTextValue(Installation.finished_installation_msg, 'Your installation is finished!'));
  }, 'installation');

  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'installation');

}, 'installation');
