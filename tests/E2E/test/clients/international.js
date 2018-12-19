var CommonClient = require('./common_client');

class International extends CommonClient {
  showSelect(value, selector) {
    return this.client
      .execute(function (selector) {
        document.querySelector(selector).style = "";
      }, selector)
      .selectByVisibleText(selector, value)
  }

  checkLanguage() {
    return this.client
      .execute(function () {
        return (navigator.language);
      })
  }
}

module.exports = International;
