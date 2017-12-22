var CommonClient = require('./common_client');
var {selector} = require('../globals.webdriverio.js');

global.categoryNameEntry = 'category' + new Date().getTime();

class Category extends CommonClient {

  goToCategoryList() {
    return this.client
      .waitForExist(selector.CatalogPage.menu_button, 90000)
      .moveToObject(selector.CatalogPage.menu_button)
      .waitForExist(selector.CategorySubMenu.submenu, 90000)
      .click(selector.CategorySubMenu.submenu)
  }

  createCategory() {
    return this.client
      .waitForExist(selector.CategorySubMenu.new_category_button, 90000)
      .click(selector.CategorySubMenu.new_category_button)
  }

  addCategoryName() {
    return this.client
      .waitForExist(selector.CategorySubMenu.name_input, 90000)
      .setValue(selector.CategorySubMenu.name_input, global.categoryNameEntry)
  }

  addCategoryImage() {
    return this.client
      .execute(function () {
        document.getElementById("image").style = "";
      })
      .chooseFile(selector.CategorySubMenu.picture, global.categoryImage)
  }

  addCategoryThumb() {
    return this.client
      .execute(function () {
        document.getElementById("image").style = "";
      })
      .chooseFile(selector.CategorySubMenu.thumb_picture, global.categoryThumb)
  }

  addCategoryTitle() {
    return this.client
      .waitForExist(selector.CategorySubMenu.title, 90000)
      .setValue(selector.CategorySubMenu.title, 'test category')
  }

  addCategoryMetaDescription() {
    return this.client
      .waitForExist(selector.CategorySubMenu.meta_description, 90000)
      .setValue(selector.CategorySubMenu.meta_description, 'this is the meta description')
  }

  addCategoryMetakeyswords() {
    return this.client
      .waitForExist(selector.CategorySubMenu.keyswords, 90000)
      .setValue(selector.CategorySubMenu.keyswords, 'keyswords')
  }

  addCategorySimplifyUrl() {
    return this.client
      .waitForExist(selector.CategorySubMenu.simplify_URL_input, 90000)
      .setValue(selector.CategorySubMenu.simplify_URL_input, global.categoryNameEntry)
  }

  SaveCategory() {
    return this.client
      .waitForExist(selector.CategorySubMenu.save_button, 90000)
      .click(selector.CategorySubMenu.save_button)
  }

  goToCategoryBO() {
    return this.client
      .waitForExist(selector.CatalogPage.menu_button, 90000)
      .moveToObject(selector.CatalogPage.menu_button)
      .waitForExist(selector.CategorySubMenu.submenu, 90000)
      .click(selector.CategorySubMenu.submenu)
  }

  searchCategoryBO() {
    return this.client
      .waitForExist(selector.CategorySubMenu.name_search_input, 90000)
      .setValue(selector.CategorySubMenu.name_search_input, global.categoryNameEntry)
      .waitForExist(selector.CategorySubMenu.search_button, 90000)
      .click(selector.CategorySubMenu.search_button)
  }

  checkCategoryImage() {
    return this.client
      .waitForExist(selector.CategorySubMenu.update_button, 90000)
      .click(selector.CategorySubMenu.update_button)
      .pause(2000)
      .then(() => this.client.isExisting(selector.CategorySubMenu.image_link))
      .then((text) => expect(text).to.be.equal(true));

  }

  checkCategoryImageThumb() {
    return this.client
      .waitForExist(selector.CategorySubMenu.thumb_link, 90000)
      .then(() => this.client.isExisting(selector.CategorySubMenu.thumb_link))
      .then((text) => expect(text).to.be.equal(true));
  }

  checkCategoryTitle() {
    return this.client
      .waitForExist(selector.CategorySubMenu.title, 90000)
      .then(() => this.client.getAttribute(selector.CategorySubMenu.title, "value"))
      .then((text) => expect(text).to.be.equal("test category"));
  }

  checkCategoryMetaDescription() {
    return this.client
      .waitForExist(selector.CategorySubMenu.meta_description, 90000)
      .then(() => this.client.getAttribute(selector.CategorySubMenu.meta_description, "value"))
      .then((text) => expect(text).to.be.equal("this is the meta description"));
  }

  checkCategorySimplifyURL() {
    return this.client
      .waitForExist(selector.CategorySubMenu.simplify_URL_input, 90000)
      .then(() => this.client.getAttribute(selector.CategorySubMenu.simplify_URL_input, "value"))
      .then((text) => expect(text).to.be.equal(global.categoryNameEntry));
  }

  openProductList() {
    return this.client
      .waitForExist(selector.AccessPageFO.product_list_button, 90000)
      .click(selector.AccessPageFO.product_list_button)
  }

  checkCategoryExistenceFO() {
    return this.client
      .waitForExist(selector.SearchProductPage.second_category_name, 90000)
      .then(() => this.client.getText(selector.SearchProductPage.second_category_name))
      .then((text) => expect(text).to.be.equal(global.categoryNameEntry));
  }

  updateCategory() {
    global.categoryNameEntry = global.categoryNameEntry + 'update';
    return this.client
      .moveToObject(selector.CategorySubMenu.update_button, 90000)
      .click(selector.CategorySubMenu.update_button)
      .waitForExist(selector.CategorySubMenu.name_input, 90000)
      .setValue(selector.CategorySubMenu.name_input, global.categoryNameEntry)
      .waitForExist(selector.CategorySubMenu.simplify_URL_input, 90000)
      .setValue(selector.CategorySubMenu.simplify_URL_input, global.categoryNameEntry)
      .waitForExist(selector.CategorySubMenu.save_button, 90000)
      .click(selector.CategorySubMenu.save_button)
  }

  deleteCategory() {
    return this.client
      .waitForExist(selector.CategorySubMenu.action_button, 90000)
      .click(selector.CategorySubMenu.action_button)
      .waitForExist(selector.CategorySubMenu.delete_button, 90000)
      .click(selector.CategorySubMenu.delete_button)
      .waitForExist(selector.CategorySubMenu.second_delete_button, 90000)
      .click(selector.CategorySubMenu.second_delete_button)
  }

  deleteCategoryWithActionGroup() {
    return this.client
      .waitForExist(selector.CategorySubMenu.select_category, 90000)
      .click(selector.CategorySubMenu.select_category)
      .waitForExist(selector.CategorySubMenu.action_group_button, 90000)
      .click(selector.CategorySubMenu.action_group_button)
      .waitForExist(selector.CategorySubMenu.delete_action_group_button, 90000)
      .click(selector.CategorySubMenu.delete_action_group_button)
      .alertAccept()
      .waitForExist(selector.CategorySubMenu.second_delete_button, 90000)
      .click(selector.CategorySubMenu.second_delete_button)
      .pause(5000)
  }

}

module.exports = Category;
