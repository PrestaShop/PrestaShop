module.exports = {
  Employee:{
    advanced_menu: '//*[@id="subtab-AdminAdvancedParameters"]/a',
    employee_menu: '//*[@id="subtab-AdminParentEmployees"]/a',
    new_employee_button: '#page-header-desc-employee-new_employee',
    first_name_input: '#firstname',
    last_name_input: '#lastname',
    email_input: '#email',
    password_input: '#passwd',
    profile_select: '#id_profile',
    save_button: '#employee_form_submit_btn',
    orders_page: '//*[@id="subtab-AdminParentOrders"]/a',
    email_search_input: '[name="employeeFilter_email"]',
    search_button_team: '#submitFilterButtonemployee',
    search_result: '.badge',
    team_employee_name: '//*[@id="form-employee"]//tbody//td[3]',
    team_employee_last_name: '//*[@id="form-employee"]//tbody//td[4]',
    team_employee_email: '//*[@id="form-employee"]//tbody//td[5]',
    team_employee_profile: '//*[@id="form-employee"]//tbody//td[6]',
    reset_search_button: '[name="submitResetemployee"]',
    dropdown_toggle: '//*[@id="form-employee"]//tbody//button',
    delete_link: '//*[@id="form-employee"]//tbody//li/a'
  }
};