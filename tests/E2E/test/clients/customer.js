var CommonClient = require('./common_client');
var {selector} = require('../globals.webdriverio.js');

class CreateCustomer extends CommonClient {

  goToCustomersMenu() {
    return this.client
      .waitForExist(selector.Customer.customer_menu, 90000)
      .click(selector.Customer.customer_menu)
      .pause(2000)
  }

  addNewCustomer() {
    return this.client
      .waitForExist(selector.Customer.new_customer_button, 90000)
      .click(selector.Customer.new_customer_button)
      .pause(2000)
  }

  addSocialTitle() {
    return this.client
      .waitForExist(selector.Customer.social_title_button, 90000)
      .click(selector.Customer.social_title_button)
      .pause(2000)
  }

  addCustomerFirstName() {
    return this.client
      .waitForExist(selector.Customer.first_name_input, 90000)
      .setValue(selector.Customer.first_name_input, 'Marion')
      .pause(2000)
  }

  addCustomerLastName() {
    return this.client
      .waitForExist(selector.Customer.last_name_input, 90000)
      .setValue(selector.Customer.last_name_input, 'Francois')
      .pause(2000)
  }

  addEmailAddress() {
    return this.client
      .waitForExist(selector.Customer.email_address_input, 90000)
      .setValue(selector.Customer.email_address_input, 'demo' + global.date_time + '@prestashop.com')
      .pause(1000)
  }

  addPassword() {
    return this.client
      .waitForExist(selector.Customer.password_input, 90000)
      .setValue(selector.Customer.password_input, '123456789')
      .pause(2000)
  }

  addBirthday() {
    return this.client
      .waitForExist(selector.Customer.days_select, 90000)
      .selectByValue(selector.Customer.days_select, '18')
      .waitForExist(selector.Customer.month_select, 90000)
      .selectByValue(selector.Customer.month_select, '12')
      .waitForExist(selector.Customer.years_select, 90000)
      .selectByValue(selector.Customer.years_select, '1991')
  }

  saveCustomer() {
    return this.client
      .waitForExist(selector.Customer.save_button, 90000)
      .click(selector.Customer.save_button)
      .pause(1000)
  }

  goToCustomersAddress() {
    return this.client
      .waitForExist(selector.Customer.customer_menu, 90000)
      .moveToObject(selector.Customer.customer_menu)
      .waitForExist(selector.Addresses.addresses_menu, 90000)
      .click(selector.Addresses.addresses_menu)
      .pause(2000)
  }

  newAddress() {
    return this.client
      .waitForExist(selector.Addresses.new_address_button, 90000)
      .click(selector.Addresses.new_address_button)
  }

  addCustomerEmail() {
    return this.client
      .waitForExist(selector.Addresses.email_input, 90000)
      .setValue(selector.Addresses.email_input, 'demo' + global.date_time + '@prestashop.com')
  }

  addIdNumber() {
    return this.client
      .waitForExist(selector.Addresses.id_number_input, 90000)
      .setValue(selector.Addresses.id_number_input, '0123456789')
  }

  addAliasAddress() {
    return this.client
      .waitForExist(selector.Addresses.address_alias_input, 90000)
      .setValue(selector.Addresses.address_alias_input, 'Ma super addresse')
  }

  addFirstName() {
    return this.client
      .waitForExist(selector.Addresses.first_name_input, 90000)
      .setValue(selector.Addresses.first_name_input, 'demo')
  }

  addLastName() {
    return this.client
      .waitForExist(selector.Addresses.last_name_input, 90000)
      .setValue(selector.Addresses.last_name_input, 'demo')
  }

  addCompanyName() {
    return this.client
      .waitForExist(selector.Addresses.company, 90000)
      .setValue(selector.Addresses.company, 'Presta')
  }

  addTVANumber() {
    return this.client
      .waitForExist(selector.Addresses.VAT_number_input, 90000)
      .setValue(selector.Addresses.VAT_number_input, '0123456789')
  }

  addAddress() {
    return this.client
      .waitForExist(selector.Addresses.address_input, 90000)
      .setValue(selector.Addresses.address_input, "12 rue d'amsterdam")
  }

  addSecondAddress() {
    return this.client
      .waitForExist(selector.Addresses.address_second_input, 90000)
      .setValue(selector.Addresses.address_second_input, "RDC")
  }

  addZIPCode() {
    return this.client
      .waitForExist(selector.Addresses.zip_code_input, 90000)
      .setValue(selector.Addresses.zip_code_input, "75009")
  }

  addCity() {
    return this.client
      .waitForExist(selector.Addresses.city_input, 90000)
      .setValue(selector.Addresses.city_input, "Paris")
  }

  addCountry() {
    return this.client
      .waitForExist(selector.Addresses.country_input, 90000)
      .selectByValue(selector.Addresses.country_input, "8")
  }

  addHomePhone() {
    return this.client
      .waitForExist(selector.Addresses.phone_input, 90000)
      .setValue(selector.Addresses.phone_input, "0123456789")
  }

  addOther() {
    return this.client
      .waitForExist(selector.Addresses.other_input, 90000)
      .setValue(selector.Addresses.other_input, "azerty")
  }

  saveAddress() {
    return this.client
      .waitForExist(selector.Addresses.save_button, 90000)
      .click(selector.Addresses.save_button)
  }
}

module.exports = CreateCustomer;
