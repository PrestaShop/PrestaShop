const {Menu} = require('../../selectors/BO/menu.js');
const {Employee} = require('../../selectors/BO/employee_page');
let promise = Promise.resolve();

/****Example of employee data ****
 * let employeeData = {
 *  firstname: 'employee_firstname',
 *  lastname: 'employee_lastname',
 *  email: 'employee_email',
 *  password: 'employee_password',
 *  profile: 'employee_profile',
 *  language: 'employee_language'
 * };
 */

module.exports = {
  editEmployee(employeeData) {
    scenario('Edit employee', client => {
      test('should go to "Team" menu', () => client.goToSubtabMenuPage(Menu.Configure.AdvancedParameters.advanced_parameters_menu, Menu.Configure.AdvancedParameters.team_submenu));
      test('should click on "Edit" button', () => client.waitForExistAndClick(Employee.edit_button));
      test('should set "First name" input', () => client.waitAndSetValue(Employee.first_name_input, employeeData.firstname));
      test('should set "Last name" input', () => client.waitAndSetValue(Employee.last_name_input, employeeData.lastname));
      test('should set "Email" input', () => client.waitAndSetValue(Employee.email_input, employeeData.email));
      test('should select the language', () => client.waitAndSelectByVisibleText(Employee.language_select, employeeData.language));
      test('should click on "Save" button', () => client.waitForExistAndClick(Employee.save_button));
    }, 'common_client');
  }
};