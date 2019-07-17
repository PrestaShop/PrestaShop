/**
 * This script is based on the scenario described in this test link
 * [id="PS-368"][Name="Filters"]
 **/

const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const commonProductScenarios = require('../../common_scenarios/product');
const commonFileScenarios = require('../../common_scenarios/file');
const welcomeScenarios = require('../../common_scenarios/welcome');
const {Files} = require('../../../selectors/BO/catalogpage/files');

let fileData = [{
  filename: 'Ps Picture',
  description: 'Picture of prestashop',
  file: 'prestashop.png'
}, {
  filename: 'Ps Category',
  description: 'Picture of category',
  file: 'category_image.png'
}, {
  filename: 'PS Guide',
  description: 'The technical documentation of prestashop',
  file: 'prestashop_developer_guide.pdf'
}];

let productData = [{
  name: 'PrA',
  quantity: "50",
  price: '5',
  image_name: 'image_test.jpg',
  reference: 'Attached product with file',
  options: {
    filename: ['PS Guide']
  }
}, {
  name: 'PrB',
  quantity: "50",
  price: '5',
  image_name: 'image_test.jpg',
  reference: 'Attached product with file',
  options: {
    filename: ['Ps Category']
  }
}];
/**
 * This script should be moved to the campaign full when this issues will be fixed
 * https://github.com/PrestaShop/PrestaShop/issues/11054 && https://github.com/PrestaShop/PrestaShop/issues/9607
 **/
scenario('Create, sort, filter, delete and check "Files" in the Back Office', () => {
  scenario('Open the browser and connect to the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should log in successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');
  welcomeScenarios.findAndCloseWelcomeModal();
  for (let i = 0; i < fileData.length; i++) {
    commonFileScenarios.createFile(fileData[i].filename, fileData[i].description, fileData[i].file);
    commonFileScenarios.checkFile(fileData[i].filename, fileData[i].description);
  }
  for (let m = 0; m < productData.length; m++) {
    commonProductScenarios.createProduct(AddProductPage, productData[m]);
  }
  commonFileScenarios.sortFile(Files.files_name, 'name', 3);
  commonFileScenarios.sortFile(Files.files_id, 'id', 2, true);
  commonFileScenarios.sortFile(Files.files_size, 'size', 5);
  commonFileScenarios.sortFile(Files.files_associated, 'associated', 6);
  commonFileScenarios.filterFile('Ps Category', 'name', 3, true);
  commonFileScenarios.sortFile(Files.files_id, 'id', 2, false, true);
  commonFileScenarios.filterFile('0', 'associated', 6);
  commonFileScenarios.filterFile('37.74k', 'size', 5);
  for (let k = 0; k < fileData.length; k++) {
    commonFileScenarios.deleteFile(fileData[k].filename);
  }
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'common_client');
}, 'common_client', true);
