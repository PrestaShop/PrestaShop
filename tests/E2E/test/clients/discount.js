var CommonClient = require('./common_client');

class Discount extends CommonClient {

  searchByName(inputSelector, buttonSelector, name) {
    if (isVisible) {
      return this.client
        .waitAndSetValue(inputSelector, name)
        .waitForExistAndClick(buttonSelector);
    }
  }

  /**
   * This function allows to select a customer
   * @param selectorInput
   * @param selectorOption
   * @param value
   * @returns {*}
   */
  chooseCustomer(selectorInput, selectorOption, value) {
    return this.client
      .waitAndSetValue(selectorInput, value)
      .pause(2000)
      .keys('ArrowDown')
      .waitForVisibleAndClick(selectorOption);
  }

  setPromoCode(selectorInput, selectorButton, value) {
    return this.client
      .waitAndSetValue(selectorInput, tab[value],2000)
      .waitForExistAndClick(selectorButton,2000);
  }

  checkTotalPrice(selector, option = 'percent') {
    return this.client
      .pause(2000)
      .then(() => this.client.getText(selector))
      .then((code) => {
        if(option === 'amount') {
          expect(code.split('€')[1]).to.be.equal(((tab["totalProducts"].split('€')[1] * 0.5) - 24).toPrecision(4).toString());
        } else {
          expect(code.split('€')[1]).to.be.equal(((tab["totalProducts"].split('€')[1] * 0.5) * 0.5).toPrecision(4).toString());
        }
      });
  }
}

module.exports = Discount;