var CommonClient = require('./common_client');
const exec = require('child_process').exec;
global.downloadedFileName = "";

class International extends CommonClient {
  showSelect(value, selector) {
    return this.client
      .execute(function (selector) {
        document.querySelector(selector).style = "";
      }, selector)
      .selectByVisibleText(selector, value)
  }

  getNavigatorLanguage() {
    return this.client
      .execute(function () {
        return (document.documentElement.lang);
      })
  }

  clickOnAction(actionSelector, groupActionSelector = '', action = 'edit') {
    if (action === 'delete') {
      return this.client
        .waitForExistAndClick(groupActionSelector)
        .waitForExistAndClick(actionSelector)
        .alertAccept();
    }
    else {
      if (action === 'edit') {
        return this.client
          .pause(2000)
          .waitForExistAndClick(actionSelector)
      }
      else {
        return this.client
          .pause(2000)
          .waitForExistAndClick(groupActionSelector)
          .waitForExistAndClick(actionSelector)
      }
    }
  }

  clearAddressFormat(selector, value) {
    return this.client
      .execute(function (element, value) {
        let addressFormatValue = document.getElementById(element).textContent;
        let editedAddressFormat = addressFormatValue.replace(addressFormatValue.substring(0, addressFormatValue.indexOf(value)), '');
        document.getElementById(element).value = editedAddressFormat;
      }, selector, value)
  }

  getCallPrefixField(element_list, i, sorted = false) {
    return this.client
      .getText(element_list.replace("%ID", i + 1)).then(function (name) {
        if (sorted) {
          if (name === '-') {
            elementsSortedTable[i] = '0';
          } else {
            elementsSortedTable[i] = name.normalize('NFKD').replace(/[+]/g, '').toLowerCase();
          }
        }
        else {
          if (name === '-') {
            elementsTable[i] = '0';
          } else {
            elementsTable[i] = name.normalize('NFKD').replace(/[+]/g, '').toLowerCase();
          }
        }
      });
  }

  getFileName(href) {
    global.downloadedFileName = href.split('/')[href.split('/').length - 1];
    return this.client.pause(100000)
  }

  unzipFile(downloadsFolderPath, filename) {
    exec(' gunzip ' + downloadsFolderPath + '/' + filename,
      (error) => {
        if (error !== null) {
          console.log(`exec error: ${error}`);
        }
      });
    return this.client
      .pause(3000)
      .refresh();
  }

  moveFile(downloadsFolderPath, filename, destinationFolder) {
    exec(' mv ' + downloadsFolderPath + filename.split('.')[0] + '.' + filename.split('.')[1] + ' ' + destinationFolder,
      (error) => {
        if (error !== null) {
          console.log(`exec error: ${error}`);
        }
      });
    return this.client
      .pause(3000)
      .refresh();
  }

  deleteFile(filename, destinationFolder) {
    exec(' rm ' + destinationFolder + '/' + filename.split('.')[0] + '.' + filename.split('.')[1],
      (error) => {
        if (error !== null) {
          console.log(`exec error: ${error}`);
        }
      });
    return this.client
      .pause(3000)
      .refresh();
  }
}

module.exports = International;
