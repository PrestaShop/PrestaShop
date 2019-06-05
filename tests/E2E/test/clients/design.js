var CommonClient = require('./common_client');

class Design extends CommonClient {

    getCategoryID(selector, pos) {
        if (global.isVisible) {
            return this.client
                .getText(selector.replace('%ID', pos))
                .then((text) => global.categoryID = text);
        }
        else {
            return this.client
                .getText(selector.replace('%ID', pos - 1))
                .then((text) => global.categoryID = text);
        }
    }

    showSelect(value, selector = '#hook_module_form > div > div:nth-child(2) > div > select') {
        return this.client
            .execute(function (selector) {
                document.querySelector(selector).style = "";
            }, selector)
            .selectByVisibleText(selector, value)
    }

    clickOnAction(actionSelector, groupActionSelector = '') {
      return this.client
        .pause(2000)
        .waitForExistAndClick(groupActionSelector)
        .waitForExistAndClick(actionSelector)
    }
}

module.exports = Design;
