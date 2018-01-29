const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {Employee} = require('../../../selectors/BO/employee_page');

scenario('Create employee', client => {
  test('should open the browser', () => client.open());
  test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  test('should go to "Team" menu', () => client.goToSubtabMenuPage(Employee.advanced_menu, Employee.employee_menu));
  test('should click on "Add new employee" button', () => client.waitForExistAndClick(Employee.new_employee_button));
  test('should set "First name" input', () => client.waitAndSetValue(Employee.first_name_input, 'Demo'));
  test('should set "Last name" input', () => client.waitAndSetValue(Employee.last_name_input, 'Prestashop'));
  test('should set "Email" input', () => client.waitAndSetValue(Employee.email_input, 'demo' + date_time + '@prestashop.com'));
  test('should set "Password" input', () => client.waitAndSetValue(Employee.password_input, '123456789'));
  test('should choose "Permission profile" option', () => client.waitAndSelectByValue(Employee.profile_select, '4'));
  test('should click on "Save" button', () => client.waitForExistAndClick(Employee.save_button));
}, 'common_client');

scenario('Check the employee creation', client => {
  test('should search the created employee', () => client.waitAndSetValue(Employee.email_search_input, 'demo' + date_time + '@prestashop.com'));
  test('should click on "Search" button', () => client.waitForExistAndClick(Employee.search_button_team));
  test('should check the result', () => client.checkTextValue(Employee.search_result,"1"));
  test('should check that the "First name" of employee is equal to "Demo"', () => client.checkTextValue(Employee.team_employee_name, 'Demo'));
  test('should check that the "Last name" of employee is equal to "Prestashop"', () => client.checkTextValue(Employee.team_employee_last_name, 'Prestashop'));
  test('should check that the "Email" of employee is equal to "demo' + date_time + '@prestashop.com"', () => client.checkTextValue(Employee.team_employee_email, 'demo' + date_time + '@prestashop.com'));
  test('should check that the "Permission profile" of employee is equal to "Salesman"', () => client.checkTextValue(Employee.team_employee_profile, 'Salesman'));
  test('should click on "Reset" button', () => client.waitForExistAndClick(Employee.reset_search_button));
  test('should click on "employee info" icon', () => client.waitForExistAndClick(AccessPageBO.info_employee));
  test('should click on "Sign out" icon', () => client.waitForVisibleAndClick(AccessPageBO.sign_out));
  test('should logout from the Back Office', () => client.signOutBO());
}, 'common_client');

scenario('Login with the new employee', client => {
  test('should set "Email" input', () => client.waitAndSetValue(AccessPageBO.login_input, 'demo' + date_time + '@prestashop.com'));
  test('should set "Password" input', () => client.waitAndSetValue(AccessPageBO.password_inputBO, '123456789'));
  test('should click on "Save" button', () => client.waitForExistAndClick(AccessPageBO.login_buttonBO));
  test('should go to orders page', () => client.waitForExistAndClick(Employee.orders_page));
  test('should logout from the Back Office', () => client.signOutBO());
}, 'common_client');