let CommonClient = require('./common_client');

class Customer extends CommonClient {

  async searchByAddress(addresses, address) {
    if (isVisible) {
      await this.fillInputText(addresses.filter_by_address_input,address);
      await this.keys('Enter');
      await page.waitForNavigation({waitUntil:'networkidle0'});
      let text = await this.getText(addresses.address_value.replace('%ID', 5));
      expect(text).to.be.equal(address);
    } else {
      let text = await this.getText(addresses.address_value.replace('%ID', 4));
      await expect(text).to.be.equal(address);
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

  getAddressNumberInVar(selector, globalVar) {
    return this.client
      .execute(function (selector) {
        return (document.querySelectorAll(selector).length);
      }, selector)
      .then((variable) => global.tab[globalVar] = variable.value);
  }

  async deleteAddresses(selector) {
    for (let j = 1; j <= (parseInt(tab['address_number'])); j++) {
      await this.client.waitForExistAndClick(selector)
    }
    return await this.client.pause(1000)
  }

  async checkCustomerDetail(selector, textToCheckWith) {
    await page.waitFor(2000);
    await page.waitFor(selector);
    let value = await page.evaluate((selector) => {
      let elem = document.querySelector(selector);
      return elem.value;
    }, selector);
    expect(value).to.be.equal(textToCheckWith);
  }

}

module.exports = Customer;
