const {getClient} = require('../common.webdriverio.js');
const {selector} = require('../globals.webdriverio.js');
const PrestashopClient = require('./prestashop_client');

global.categoryName = 'category' + new Date().getTime();

class Category extends PrestashopClient {

  goToCategoryList() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.menu_button, 90000)
      .moveToObject(selector.BO.CatalogPage.menu_button)
      .waitForExist(selector.BO.CatalogPage.CategorySubmenu.submenu, 90000)
      .click(selector.BO.CatalogPage.CategorySubmenu.submenu)
  }

  createCategory() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.CategorySubmenu.new_category_button, 90000)
      .click(selector.BO.CatalogPage.CategorySubmenu.new_category_button)
  }

  addCategoryName() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.CategorySubmenu.name_input, 90000)
      .setValue(selector.BO.CatalogPage.CategorySubmenu.name_input, global.categoryName)
  }

  addCategoryImage() {
    return this.client
      .execute(function () {
        document.getElementById("image").style = "";
      })
      .chooseFile(selector.BO.CatalogPage.CategorySubmenu.picture, global.categoryImage)
  }

  addCategoryThumb() {
    return this.client
      .execute(function () {
        document.getElementById("image").style = "";
      })
      .chooseFile(selector.BO.CatalogPage.CategorySubmenu.thumb_picture, global.categoryThumb)
  }

  addCategoryTitle() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.CategorySubmenu.title, 90000)
      .setValue(selector.BO.CatalogPage.CategorySubmenu.title, 'test category')
  }

  addCategoryMetaDescription() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.CategorySubmenu.meta_description, 90000)
      .setValue(selector.BO.CatalogPage.CategorySubmenu.meta_description, 'this is the meta description')
  }

  addCategoryMetakeyswords() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.CategorySubmenu.keyswords, 90000)
      .setValue(selector.BO.CatalogPage.CategorySubmenu.keyswords, 'keyswords')
  }

  addCategorySimplifyUrl() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.CategorySubmenu.simplify_URL_input, 90000)
      .setValue(selector.BO.CatalogPage.CategorySubmenu.simplify_URL_input, global.categoryName)
  }

  addCategorySave() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.CategorySubmenu.save_button, 90000)
      .click(selector.BO.CatalogPage.CategorySubmenu.save_button)
  }

  goToCategoryBO() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.menu_button, 90000)
      .moveToObject(selector.BO.CatalogPage.menu_button)
      .waitForExist(selector.BO.CatalogPage.CategorySubmenu.submenu, 90000)
      .click(selector.BO.CatalogPage.CategorySubmenu.submenu)
  }

  searchCategoryBO() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.CategorySubmenu.name_search_input, 90000)
      .setValue(selector.BO.CatalogPage.CategorySubmenu.name_search_input, global.categoryName)
      .waitForExist(selector.BO.CatalogPage.CategorySubmenu.search_button, 90000)
      .click(selector.BO.CatalogPage.CategorySubmenu.search_button)
      .getText(selector.BO.CatalogPage.CategorySubmenu.search_result).then(function (text) {
        text = text.indexOf(global.categoryName);
        if (text === -1) {
          done(new Error('we could not find the category in the list of category'));
        }
      })
  }

  checkCategoryImage() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.CategorySubmenu.update_button, 90000)
      .click(selector.BO.CatalogPage.CategorySubmenu.update_button)
      .isExisting(selector.BO.CatalogPage.CategorySubmenu.image_link).then(function (text) {
        if (!text) {
          done(new Error('we could not find the image'));
        }
      })
  }

  checkCategoryImageThumb() {
    return this.client
      .isExisting(selector.BO.CatalogPage.CategorySubmenu.thumb_link).then(function (text) {
        if (!text) {
          done(new Error('we could not find the thumb image'));
        }
      })
  }

  checkCategoryTitle() {
    return this.client
      .then(() => this.client.getAttribute(selector.BO.CatalogPage.CategorySubmenu.title, "value"))
      .then((text) => expect(text).to.be.equal("test category"));
  }

  checkCategoryMetaDescription() {
    return this.client
      .then(() => this.client.getAttribute(selector.BO.CatalogPage.CategorySubmenu.meta_description, "value"))
      .then((text) => expect(text).to.be.equal("this is the meta description"));
  }

  checkCategorySimplifyURL() {
    return this.client
      .then(() => this.client.getAttribute(selector.BO.CatalogPage.CategorySubmenu.simplify_URL_input, "value"))
      .then((text) => expect(text).to.be.equal(global.categoryName));
  }

  openProductList() {
    return this.client
      .waitForExist(selector.FO.AccessPage.product_list_button, 90000)
      .click(selector.FO.AccessPage.product_list_button)
  }

  checkCategoryExistenceFO() {
    return this.client
      .then(() => this.client.getText('//*[@id="left-column"]/div[1]/ul/li[2]/ul/li[' + 2 + ']/a'))
      .then((text) => expect(text).to.be.equal(global.categoryName));
  }

  updateCategory() {
    global.categoryName = global.categoryName + 'update';
    return this.client
      .moveToObject(selector.BO.CatalogPage.CategorySubmenu.update_button, 90000)
      .click(selector.BO.CatalogPage.CategorySubmenu.update_button)
      .waitForExist(selector.BO.CatalogPage.CategorySubmenu.name_input, 90000)
      .setValue(selector.BO.CatalogPage.CategorySubmenu.name_input, global.categoryName)
      .waitForExist(selector.BO.CatalogPage.CategorySubmenu.simplify_URL_input, 90000)
      .setValue(selector.BO.CatalogPage.CategorySubmenu.simplify_URL_input, global.categoryName)
      .waitForExist(selector.BO.CatalogPage.CategorySubmenu.save_button, 90000)
      .click(selector.BO.CatalogPage.CategorySubmenu.save_button)
  }

  deleteCategory() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.CategorySubmenu.action_button, 90000)
      .click(selector.BO.CatalogPage.CategorySubmenu.action_button)
      .waitForExist(selector.BO.CatalogPage.CategorySubmenu.delete_button, 90000)
      .click(selector.BO.CatalogPage.CategorySubmenu.delete_button)
      .waitForExist(selector.BO.CatalogPage.CategorySubmenu.second_delete_button, 90000)
      .click(selector.BO.CatalogPage.CategorySubmenu.second_delete_button)
  }

  deleteCategoryWithActiongroup() {
    return this.client
      .waitForExist(selector.BO.CatalogPage.CategorySubmenu.select_category, 90000)
      .click(selector.BO.CatalogPage.CategorySubmenu.select_category)
      .waitForExist(selector.BO.CatalogPage.CategorySubmenu.action_group_button, 90000)
      .click(selector.BO.CatalogPage.CategorySubmenu.action_group_button)
      .waitForExist(selector.BO.CatalogPage.CategorySubmenu.delete_action_group_button, 90000)
      .click(selector.BO.CatalogPage.CategorySubmenu.delete_action_group_button)
      .alertAccept()
      .waitForExist(selector.BO.CatalogPage.CategorySubmenu.second_delete_button, 90000)
      .click(selector.BO.CatalogPage.CategorySubmenu.second_delete_button)
  }

}

module.exports = Category;
