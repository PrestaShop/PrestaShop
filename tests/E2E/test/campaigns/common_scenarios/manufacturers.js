const {Menu} = require('../../selectors/BO/menu.js');
const {CatalogPage} = require('../../selectors/BO/catalogpage/index');
const {Brands} = require('../../selectors/BO/catalogpage/Manufacturers/brands');
const {BrandAddress} = require('../../selectors/BO/catalogpage/Manufacturers/brands_address');
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

/**** Example of brand address data ****
 * let brandAddressData = {
 *  brand: 'name of brand',
 *  lastName: 'last name of the brand address',
 *  firstName: 'first name of the brand address',
 *  address: 'address of the brand address',
 *  secondAddress: 'second address of the brand address',
 *  postalCode: 'postal code of the brand address',
 *  city: 'city of the brand address',
 *  country: 'country of the brand address',
 *  homePhone: 'home phone of the brand address',
 *  mobilePhone: 'mobile phone of the brand address',
 *  other: 'other',
 * };
 */

module.exports = {
  createBrand: function (brandData) {
    scenario('Create a new "Brand"', client => {
      test('should go to "Brands & Suppliers" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.manufacturers_submenu));
      test('should click on "Add new brand" button', () => client.waitForExistAndClick(Brands.new_brand_button));
      test('should set the "Name" input', () => client.waitAndSetValue(Brands.name_input, brandData.name + date_time));
      test('should set the "Short Description" input', () => client.setiFrameContent(Brands.short_description_input, brandData.shortDescription,false));
      test('should set the "Description" input', () => client.setiFrameContent(Brands.description_input, brandData.description,false));
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
      test('should click on "Activate" button', () => client.scrollWaitForExistAndClick(Brands.active_button));
      test('should click on "Save" button', () => client.scrollWaitForExistAndClick(Brands.save_button));
      test('should verify the appearance of the green validation', () => client.checkTextValue(CatalogPage.success_panel, 'Successful creation.'));
    }, 'common_client');
  },
  createBrandAddress: function (brandAddressData) {
    scenario('Create a new "Brand address"', client => {
      test('should click on "Add new brand address" button', () => client.waitForExistAndClick(BrandAddress.new_brand_address_button));
      test('should Choose the brand name', () => client.waitAndSelectByVisibleText(BrandAddress.brand_select, brandAddressData.brand + date_time));
      test('should set the "Last name" input', () => client.waitAndSetValue(BrandAddress.last_name_input, brandAddressData.lastName));
      test('should set the "First name" input', () => client.waitAndSetValue(BrandAddress.first_name_input, brandAddressData.firstName));
      test('should set the "Address" input', () => client.waitAndSetValue(BrandAddress.address_input, brandAddressData.address));
      test('should set the "Second address" input', () => client.waitAndSetValue(BrandAddress.secondary_address, brandAddressData.secondAddress));
      test('should set the "Zip code" input', () => client.waitAndSetValue(BrandAddress.postal_code_input, brandAddressData.postalCode));
      test('should set the "City" input', () => client.waitAndSetValue(BrandAddress.city_input, brandAddressData.city));
      test('should choose the country', () => client.waitAndSelectByVisibleText(BrandAddress.country, brandAddressData.country));
      test('should set the "Home phone" input', () => client.waitAndSetValue(BrandAddress.phone_input, brandAddressData.homePhone));
      test('should set the "Mobile phone" input', () => client.waitAndSetValue(BrandAddress.mobile_phone_input, brandAddressData.mobilePhone));
      test('should set the "Other" input', () => client.waitAndSetValue(BrandAddress.other_input, brandAddressData.other));
      test('should click on "Save" button', () => client.scrollWaitForExistAndClick(BrandAddress.save_button));
      test('should verify the appearance of the green validation', () => client.checkTextValue(CatalogPage.success_panel, 'Successful creation.'));
    }, 'common_client');
  },
};
