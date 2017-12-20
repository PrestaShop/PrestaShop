var CommonClient = require('./common_client');

global.tab = [] ;

class Module extends CommonClient {

  searchModule(selector, moduleName) {
    return this.client
      .waitAndSetValue(selector.modules_search_input, moduleName)
      .waitForExistAndClick(selector.modules_search_button)
  }

  goInstalledModule(selector){
    return this.client
      .pause(1000)
      .waitForExistAndClick(selector)
  }

  waitForVisibleAndCheckText(selector, text){
    return this.client
      .waitForVisible(selector.success_install_message, 90000)
      .then(() => this.client.getText(selector.installed_message))
      .then((variable) => expect(text).to.equal("Module installed!"));
  }


}

module.exports = Module;
