let CommonClient = require('./common_client');

class Customer extends CommonClient {

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

  searchByEmail(customer, customer_email) {
    if (isVisible) {
      return this.client
        .waitAndSetValue(customer.customer_filter_by_email_input, customer_email)
        .keys('\uE007')
        .getText(customer.email_address_value.replace('%ID', 6)).then(function (text) {
          expect(text).to.be.equal(customer_email);
        })
    } else {
      return this.client
        .getText(customer.email_address_value.replace('%ID', 5)).then(function (text) {
          expect(text).to.be.equal(customer_email);
        })
    }
  }

  addressSearch(selector, addressData) {
    if (global.isVisible) {
      return this.client
        .waitAndSetValue(selector, addressData.address + " " + date_time)
        .keys('\uE007')
    } else {
      return this.client
        .pause(1000)
    }
  }

  CheckAddressExistence(addresses, addressData) {
    if (global.isVisible) {
      return this.client.getText(addresses.address_value.replace('%ID', 5)).then(function (text) {
        expect(text).to.be.equal(addressData + " " + date_time);
      })
    } else {
      return this.client.getText(addresses.address_value.replace('%ID', 4)).then(function (text) {
        expect(text).to.be.equal(addressData + " " + date_time);
      })
    }
  }
}

module.exports = Customer;