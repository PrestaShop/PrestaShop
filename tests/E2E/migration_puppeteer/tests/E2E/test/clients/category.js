var CommonClient = require('./common_client');
let path = require('path');
global.checkCategoryName = [];

class Category extends CommonClient {

  async checkImage(selector) {
    await page.waitForSelector(selector);
    const exist = await this.isExisting(selector);
    expect(exist).to.be.true;
  }

  async clickOnAction(actionSelector, groupActionSelector = '', action = 'edit', alert = false) {
    if (action === 'delete') {
      if (alert) {
        await this.waitForExistAndClick(groupActionSelector);
        if(!global.alertAccept)  await this.alertAccept('accept');
        await this.waitForExistAndClick(actionSelector);

      } else {
        await this.waitForExistAndClick(groupActionSelector);
        await this.waitForExistAndClick(actionSelector);
      }
    } else {
      this.waitForExistAndClick(actionSelector);
    }
  }

  async getCategoriesName(categories_list, i) {
    const name = await page.evaluate((selector,i) => {return document.querySelectorAll(selector)[i].textContent;},categories_list+ ' a',i);
    checkCategoryName[i] = name;
  }

  async checkCategory(selector, category_name) {
    await page.waitForSelector(selector);
    expect(checkCategoryName).to.be.an('array').that.does.include(category_name);
  }

  async checkCategoryInsideParent(parentSelector, categorySelector) {
    await this.moveToObject(parentSelector);
    await this.isVisible(categorySelector);
    expect(global.isVisible).to.be.true;
    await this.waitForExistAndClick(categorySelector);
  }

  async checkBreadcrumbInFo(selector, parentCategory, categoryName) {
    let count = await page.evaluate((selector) => {return document.querySelector(selector).getElementsByTagName("ol")[0].children.length;},selector);
    let breadcrumb = '';
    for (let i = 0; i < count; i++) {
        breadcrumb += await page.evaluate((selector) => {return document.querySelector(selector).getElementsByTagName("ol")[0].children[i].innerText + '/ ';},selector);
    }
    expect(breadcrumb.value).to.contains("Home", parentCategory, categoryName);
  }
}

module.exports = Category;
