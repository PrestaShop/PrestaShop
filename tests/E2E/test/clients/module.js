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
global.moduleInfo = [];
global.moduleSort = [];

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

  getModuleAttr(selector, attr, index) {
    return this.client
      .getAttribute(selector.replace('%I', index + 1), attr)
      .then((name) => {
        moduleSort[index] = name.toLowerCase();
        moduleInfo[index] = name.toLowerCase();
      })
  }

  checkSortByName(length) {
    return this.client
      .pause(1000)
      .then(() => {
        this.client
          .waitUntil(function () {
            expect(moduleInfo.sort()).to.deep.equal(moduleSort)
          }, 1000 * length)
      });
  }

  checkSortByIncPrice(length) {
    return this.client
      .pause(1000)
      .then(() => {
        this.client
          .waitUntil(function () {
            expect(moduleInfo.sort(function (a, b) {
              return a - b;
            })).to.deep.equal(moduleSort)
          }, 1000 * length)
      });
  }

  checkSortDesc(length) {
    return this.client
      .pause(1000)
      .then(() => {
        this.client
          .waitUntil(function () {
            expect(moduleInfo.sort(function (a, b) {
              return a - b;
            }).reverse()).to.deep.equal(moduleSort)
          }, 1000 * length)
      });
  }
}

module.exports = Module;
