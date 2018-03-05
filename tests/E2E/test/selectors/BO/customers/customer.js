module.exports = {
  Customer: {
    customer_menu: '//*[@id="subtab-AdminParentCustomer"]/a',
    customers_subtab: '//*[@id="subtab-AdminCustomers"]/a',
    new_customer_button: '//*[@id="page-header-desc-customer-new_customer"]',
    social_title_button: '//*[@id="gender_1"]',
    first_name_input: '//*[@id="firstname"]',
    last_name_input: '//*[@id="lastname"]',
    email_address_input: '//*[@id="email"]',
    password_input: '//*[@id="passwd"]',
    days_select: '//*[@id="fieldset_0"]//select[@name="days"]',
    month_select: '//*[@id="fieldset_0"]//select[@name="months"]',
    years_select: '//*[@id="fieldset_0"]//select[@name="years"]',
    save_button: '//*[@id="customer_form_submit_btn"]',
    customer_filter_by_email_input: '//*[@id="form-customer"]//input[@name="customerFilter_email"]',
    email_address_value: '//*[@id="form-customer"]//td[%ID]',
    reset_button: '//*[@id="table-customer"]//button[@name="submitResetcustomer"]',
    edit_button:'//*[@id="form-customer"]//a[@title="Edit"]'
  }
};