var PrestashopClient = require('./prestashop_client');
var {selector} = require('../globals.webdriverio.js');

class CreateCustomer extends PrestashopClient {

  goToCustomersMenu() {
    return this.client
      .waitForExist(selector.BO.Customers.Customer.customer_menu, 90000)
      .click(selector.BO.Customers.Customer.customer_menu)
      .pause(2000)
  }

  addNewCustomer() {
    return this.client
      .waitForExist(selector.BO.Customers.Customer.new_customer_button, 90000)
      .click(selector.BO.Customers.Customer.new_customer_button)
      .pause(2000)
  }

  addSocialTitle() {
    return this.client
      .waitForExist(selector.BO.Customers.Customer.social_title_button, 90000)
      .click(selector.BO.Customers.Customer.social_title_button)
      .pause(2000)
  }

  addCustomerFirstName() {
    return this.client
      .waitForExist(selector.BO.Customers.Customer.first_name_input, 90000)
      .setValue(selector.BO.Customers.Customer.first_name_input, 'Marion')
      .pause(2000)
  }

  addCustomerLastName() {
    return this.client
      .waitForExist(selector.BO.Customers.Customer.last_name_input, 90000)
      .setValue(selector.BO.Customers.Customer.last_name_input, 'Francois')
      .pause(2000)
  }

  addEmailAddress() {
    return this.client
      .waitForExist(selector.BO.Customers.Customer.email_address_input, 90000)
      .setValue(selector.BO.Customers.Customer.email_address_input, 'demo' + global.product_id + '@prestashop.com')
      .pause(1000)
  }

  addPassword() {
    return this.client
      .waitForExist(selector.BO.Customers.Customer.password_input, 90000)
      .setValue(selector.BO.Customers.Customer.password_input, '123456789')
      .pause(2000)
  }

  addBirthday() {
    return this.client
      .waitForExist(selector.BO.Customers.Customer.days_select, 90000)
      .selectByValue(selector.BO.Customers.Customer.days_select, '18')
      .waitForExist(selector.BO.Customers.Customer.month_select, 90000)
      .selectByValue(selector.BO.Customers.Customer.month_select, '12')
      .waitForExist(selector.BO.Customers.Customer.years_select, 90000)
      .selectByValue(selector.BO.Customers.Customer.years_select, '1991')
  }

  saveCustomer() {
    return this.client
      .waitForExist(selector.BO.Customers.Customer.save_button, 90000)
      .click(selector.BO.Customers.Customer.save_button)
      .pause(1000)
  }

  goToCustomersAddress() {
    return this.client
      .waitForExist(selector.BO.Customers.Customer.customer_menu, 90000)
      .moveToObject(selector.BO.Customers.Customer.customer_menu)
      .waitForExist(selector.BO.Customers.addresses.addresses_menu, 90000)
      .click(selector.BO.Customers.addresses.addresses_menu)
      .pause(2000)
  }

  newAddress() {
    return this.client
      .waitForExist(selector.BO.Customers.addresses.new_address_button, 90000)
      .click(selector.BO.Customers.addresses.new_address_button)
      .pause(2000)
  }

  addCustomerEmail() {
    return this.client
      .waitForExist(selector.BO.Customers.addresses.email_input, 90000)
      .setValue(selector.BO.Customers.addresses.email_input, 'demo' + global.product_id + '@prestashop.com')
      .pause(1000)
  }

  addIdNumber() {
    return this.client
      .waitForExist(selector.BO.Customers.addresses.id_number_input, 90000)
      .setValue(selector.BO.Customers.addresses.id_number_input, '0123456789')
      .pause(1000)
  }

  addAliasAddress() {
    return this.client
      .waitForExist(selector.BO.Customers.addresses.address_alias_input, 90000)
      .setValue(selector.BO.Customers.addresses.address_alias_input, 'Ma super addresse')
      .pause(1000)
  }

  addFirstName() {
    return this.client
      .waitForExist(selector.BO.Customers.addresses.first_name_input, 90000)
      .setValue(selector.BO.Customers.addresses.first_name_input, 'demo')
      .pause(1000)
  }

  addLastName() {
    return this.client
      .waitForExist(selector.BO.Customers.addresses.last_name_input, 90000)
      .setValue(selector.BO.Customers.addresses.last_name_input, 'demo')
      .pause(1000)
  }

  addCompanyName() {
    return this.client
      .waitForExist(selector.BO.Customers.addresses.company, 90000)
      .setValue(selector.BO.Customers.addresses.company, 'Presta')
      .pause(1000)
  }

  addTVANumber() {
    return this.client
      .waitForExist(selector.BO.Customers.addresses.VAT_number_input, 90000)
      .setValue(selector.BO.Customers.addresses.VAT_number_input, '0123456789')
      .pause(1000)
  }

  addAddress() {
    return this.client
      .waitForExist(selector.BO.Customers.addresses.address_input, 90000)
      .setValue(selector.BO.Customers.addresses.address_input, "12 rue d'amsterdam")
      .pause(1000)
  }

  addSecondAddress() {
    return this.client
      .waitForExist(selector.BO.Customers.addresses.address_second_input, 90000)
      .setValue(selector.BO.Customers.addresses.address_second_input, "RDC")
      .pause(1000)
  }

  addZIPCode() {
    return this.client
      .waitForExist(selector.BO.Customers.addresses.zip_code_input, 90000)
      .setValue(selector.BO.Customers.addresses.zip_code_input, "75009")
      .pause(1000)
  }

  addCity() {
    return this.client
      .waitForExist(selector.BO.Customers.addresses.city_input, 90000)
      .setValue(selector.BO.Customers.addresses.city_input, "Paris")
      .pause(2000)
  }

  addCountry() {
    return this.client
      .waitForExist(selector.BO.Customers.addresses.country_input, 90000)
      .selectByValue(selector.BO.Customers.addresses.country_input, "8")
      .pause(1000)
  }

  addHomePhone() {
    return this.client
      .waitForExist(selector.BO.Customers.addresses.phone_input, 90000)
      .setValue(selector.BO.Customers.addresses.phone_input, "0123456789")
      .pause(1000)
  }

  addOther() {
    return this.client
      .waitForExist(selector.BO.Customers.addresses.other_input, 90000)
      .setValue(selector.BO.Customers.addresses.other_input, "azerty")
      .pause(1000)
  }

  saveAddress() {
    return this.client
      .waitForExist(selector.BO.Customers.addresses.save_button, 90000)
      .click(selector.BO.Customers.addresses.save_button)
      .pause(1000)
  }
}

module.exports = CreateCustomer;
