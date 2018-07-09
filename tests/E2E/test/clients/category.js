var CommonClient = require('./common_client');

global.checkCategoryName = [];

class Category extends CommonClient {

  checkImage(selector) {
    return this.client
      .waitForExist(selector)
      .pause(2000)
      .then(() => this.client.isExisting(selector))
      .then((text) => expect(text).to.be.equal(true));

  }

  clickOnAction(actionSelector, groupActionSelector = '', action = 'edit', alert = false) {
    if (action === 'delete') {
      if (alert) {
        return this.client
          .waitForExistAndClick(groupActionSelector)
          .waitForVisibleAndClick(actionSelector)
          .alertAccept();
      } else {
        return this.client
          .waitForExistAndClick(groupActionSelector)
          .waitForVisibleAndClick(actionSelector);
      }
    } else {
      return this.client
        .pause(2000)
        .waitForExistAndClick(actionSelector);
    }
  }

  getCategoriesName(categories_list, i) {
    return this.client.getText(categories_list + '/li[' + i + ']/a').then(function (name) {
      checkCategoryName[i] = name;
    });
  }

  checkCategory(selector, category_name) {
    return this.client
      .waitForExist(selector)
      .then(() => {
        expect(checkCategoryName).to.be.an('array').that.does.include(category_name);
      });
  }

  getCategoriesPageNumber(selector) {
    return this.client
      .execute(function (selector) {
        let count = document.getElementById(selector).getElementsByTagName("tbody")[0].children.length;
        return count;
      }, selector)
      .then((count) => {
        global.categoriesPageNumber = count.value;
      });
  }
}

module.exports = Category;
