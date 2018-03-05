let CommonClient = require('./common_client');

class Customer extends CommonClient {

  EmailSearch(selector, customer_email) {
    if (global.isVisible) {
      return this.client
        .waitAndSetValue(selector, customer_email + date_time + '@prestashop.com')
        .keys('\uE007')
    }else{
      return this.client
        .pause(1000)
    }
  }

  CheckEmailExistence(customer, customer_email) {
    if (global.isVisible) {
      return this.client.getText(customer.email_address_value.replace('%ID', 6)).then(function (text) {
        expect(text).to.be.equal(customer_email + date_time + '@prestashop.com');
      })
    } else {
      return this.client.getText(customer.email_address_value.replace('%ID', 5)).then(function (text) {
        expect(text).to.be.equal(customer_email + date_time + '@prestashop.com');
      })
    }
  }

  searchByAddress(addresses, address) {
    if (isVisible) {
      return this.client
        .waitAndSetValue(addresses.filter_by_address_input, address)
        .keys('\uE007')
        .getText(addresses.address_value.replace('%ID', 5)).then(function (text) {
          expect(text).to.be.equal("12 rue d'amsterdam" + address);
        })
    } else {
      return this.client
        .getText(addresses.address_value.replace('%ID', 4)).then(function (text) {
          expect(text).to.be.equal("12 rue d'amsterdam" + address);
        })
    }
  }
}

module.exports = Customer;