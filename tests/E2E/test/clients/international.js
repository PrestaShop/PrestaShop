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

  checkCheckboxStatus(selector, checkedValue) {
    return this.client
      .pause(2000)
      .execute(function (selector) {
        return (document.querySelector(selector).checked);
      }, selector)
      .then((status) => {
        expect(status.value).to.equal(checkedValue)
      });
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
}

module.exports = International;
