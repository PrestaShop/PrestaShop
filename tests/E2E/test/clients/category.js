var CommonClient = require('./common_client');
let path = require('path');
global.checkCategoryName = [];

class Category extends CommonClient {

  checkImage(selector) {
    return this.client
      .waitForExist(selector)
      .pause(2000)
      .then(() => this.client.isExisting(selector))
      .then((text) => expect(text, "The picture is not existing").to.be.equal(true));

  }

  clickOnAction(actionSelector, groupActionSelector = '', action = 'edit', alert = false) {
    if (action === 'delete') {
      if (alert) {
        return this.client
          .waitForExistAndClick(groupActionSelector)
          .waitForExistAndClick(actionSelector)
          .alertAccept()
      } else {
        return this.client
          .waitForExistAndClick(groupActionSelector)
          .waitForExistAndClick(actionSelector)
      }
    } else {
      return this.client
        .pause(2000)
        .waitForExistAndClick(actionSelector)
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
        expect(checkCategoryName).to.be.an('array').that.does.include(category_name)
      });
  }

  checkCategoryInsideParent(parentSelector, categorySelector) {
    return this.client
      .moveToObject(parentSelector)
      .isVisible(categorySelector)
      .then((isVisible) => {
        expect(isVisible).to.be.true;
        if (isVisible) {
          this.client.waitForExistAndClick(categorySelector);
        }
      });
  }

  checkBreadcrumbInFo(selector, parentCategory, categoryName) {
    return this.client
      .execute(function (selector) {
        let count = document.querySelector(selector).getElementsByTagName("ol")[0].children.length;
        let breadcrumb = '';
        for(let i = 0; i < count; i++) {
          breadcrumb += document.querySelector(selector).getElementsByTagName("ol")[0].children[i].innerText + '/ '
        }
        return breadcrumb;
      }, selector)
      .then((breadcrumb) => {
        expect(breadcrumb.value).to.contains("Home", parentCategory, categoryName)
      })
  }
}

module.exports = Category;
