var CommonClient = require('./common_client');

global.fileLists = [];
global.fileSortedLists = [];

class File extends CommonClient {

  searchByValue(searchInput, searchButton, value) {
    if(global.isVisible) {
      return this.client
        .waitAndSetValue(searchInput, value)
        .waitForExistAndClick(searchButton);
    }
  }

  resetButton(selector, filtredTable) {
    if(global.isVisible && !filtredTable) {
      return this.client
        .waitForExistAndClick(selector);
    }
  }

  getFilesNumber(selector, pause = 0) {
    if(global.isVisible) {
      return this.client
        .pause(pause)
        .then(() => {
          global.filesNumber = 0;
        });
    } else {
      return this.client
        .pause(pause)
        .execute(function (selector) {
          let count = document.getElementById(selector).getElementsByTagName("tbody")[0].children.length;
          return count;
        }, selector)
        .then((count) => {
          global.filesNumber = count.value;
        });
    }
  }

  getFileInformations(selector, index, lowerCase = true, pause = 0, timeout = 90000) {
    return this.client
      .pause(pause)
      .waitForExist(selector, timeout)
      .getText(selector)
      .then((name) => {
        if(lowerCase) {
          global.fileLists[index] = name.toLowerCase();
          global.fileSortedLists[index] = name.toLowerCase();
        } else {
          global.fileLists[index] = name;
        }
      });
  }

  checkSortFile() {
    return this.client
      .pause(1000)
      .then(() => {
        this.client
          .waitUntil(function () {
            expect(fileLists.sort()).to.deep.equal(fileSortedLists);
          }, 1000 * global.tab['filesNumber']);
      });
  }

  checkFilterFile(searchValue) {
    return this.client
      .pause(1000)
      .then(() => {
        for (let k = 0; k < global.filesNumber; k++) {
          expect(global.fileLists[k]).to.contain(searchValue);
        }
      });
  }
}

module.exports = File;