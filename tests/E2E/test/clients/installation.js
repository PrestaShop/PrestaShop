var CommonClient = require('../../../E2E/test/clients/common_client');

class Installation extends CommonClient {

  setNameInput(selector, data) {
    return this.client
      .waitForVisible(selector, 90000)
      .pause(2000)
      .setValue(selector, data)
  }

  goToTheNextPage(selector){
    return this.client
      .pause(2000)
      .waitForVisibleAndClick(selector, 90000)
  }

}

module.exports = Installation;
