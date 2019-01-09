const {Menu} = require('../../selectors/BO/menu.js');
const {CatalogPage} = require('../../selectors/BO/catalogpage/index');
const {Brands} = require('../../selectors/BO/catalogpage/Manufacturers/brands');
let promise = Promise.resolve();

/**** Example of brand data ****
 * let brandData = {
 *  name: 'name of brand',
 *  shortDescription: 'short description of brand',
 *  description: 'description of the brand',
 *  picture: 'brand picture file',
 *  metaTitle: 'meta title of the brand',
 *  metaDescription: 'meta description of the brand',
 *  metaKeywords: {
 *    1: 'first key',
 *    2: 'second key'
 *  },
 * };
 */

module.exports = {
  createBrand: function (brandData) {
    scenario('Create a new "Brand"', client => {
      test('should go to "Brands & Suppliers" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.manufacturers_submenu));
      test('should click on "Add new brand" button', () => client.waitForExistAndClick(Brands.new_brand_button));
      test('should set the "Name" input', () => client.waitAndSetValue(Brands.name_input, brandData.name + date_time));
      test('should set the "Short Description" input', () => client.setEditorText(Brands.short_description_input, brandData.shortDescription));
      test('should set the "Description" input', () => client.setEditorText(Brands.description_input, brandData.description));
      test('should upload "Picture" to the brand', () => client.uploadPicture(brandData.picture, Brands.image_input, "logo"));
      test('should set the "Meta title" input', () => client.waitAndSetValue(Brands.meta_title_input, brandData.metaTitle));
      test('should set the "Meta description" input', () => client.waitAndSetValue(Brands.meta_description_input, brandData.metaDescription));
      Object.keys(brandData.metaKeywords).forEach(function (key) {
        test('should set the "Meta keywords" input', () => {
          return promise
            .then(() => client.waitAndSetValue(Brands.meta_keywords_input, brandData.metaKeywords[key]))
            .then(() => client.keys('Enter'));
        });
      });
      test('should click on "Activate" button', () => client.waitForExistAndClick(Brands.active_button));
      test('should click on "Save" button', () => client.waitForExistAndClick(Brands.save_button));
      test('should verify the appearance of the green validation', () => client.checkTextValue(CatalogPage.success_panel, '×\nSuccessful creation.'));
    }, 'common_client');
  }
};
