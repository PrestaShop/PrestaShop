const CommonClient = require('./common_client');

class Employee extends CommonClient {

  checkEmployeeLanguage(selector, textToCheckWith, pause = 0) {
    return this.client
      .pause(pause)
      .waitForExist(selector, 9000)
      .then(() => this.client.getText(selector))
      .then((text) => expect(text.toLowerCase()).to.contain(textToCheckWith.toLowerCase()));
  }
}

module.exports = Employee;
