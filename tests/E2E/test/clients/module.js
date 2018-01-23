var CommonClient = require('./common_client');

global.moduleObject = {
  "data-name": '',
  "data-tech-name": '',
  "data-author": '',
  "data-description": '',
  "data-child-categories": '',
  "data-categories": '',
  "data-type": ''
};
global.moduleLists = {};

class Module extends CommonClient {

  getModuleAttributes(selector, attr, index) {
    return this.client
      .getAttribute(selector.replace('%I', index), attr)
      .then((name) => {
        global.moduleLists[index - 1][attr] = name.toLowerCase();
      })
  }

  checkModuleData(number) {
    return this.client
      .pause(12000)
      .then(() => {
        for (let k = 0; k < number-1; k++) {
          var exist = false;
          for (var attr in moduleLists[k]) {
            if (moduleLists[k][attr].includes('contact') || moduleLists[k][attr].includes('form')) {
              exist = true;
            }
          }
          expect(exist).to.be.true;
        }
      })
  }

}

module.exports = Module;