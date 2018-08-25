const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const commonProductScenarios = require('../../common_scenarios/product');
const commonFileScenarios = require('../../common_scenarios/file');

let fileData = [{
  filename: 'Ps Picture',
  description: 'Picture of prestashop',
  file: 'prestashop.png'
}, {
  filename: 'Ps Category',
  description: 'Picture of category',
  file: 'category_image.png'
}, {
  filename: 'PS Developer Guide',
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
    filename: 'PS Developer Guide'
  }
}, {
  name: 'PrB',
  quantity: "50",
  price: '5',
  image_name: 'image_test.jpg',
  reference: 'Attached product with file',
  options: {
    filename: 'Ps Category'
  }
}];

scenario('Create, sort, filter, delete and check "Files" in the Back Office', () => {
  scenario('Open the browser and connect to the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should log in successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');
  for(let i = 0; i < fileData.length; i++) {
    commonFileScenarios.createFile(fileData[i].filename, fileData[i].description, fileData[i].file);
    commonFileScenarios.checkFile(fileData[i].filename, fileData[i].description);
  }
  for(let m = 0; m < productData.length; m++) {
    commonProductScenarios.createProduct(AddProductPage, productData[m]);
  }
  commonFileScenarios.sortFile('id', 2);
  commonFileScenarios.sortFile('name', 3);
  commonFileScenarios.sortFile('size', 5);
  commonFileScenarios.sortFile('associated', 6);
  commonFileScenarios.filterFile('1', 'associated', 6, true);
  commonFileScenarios.filterFile('Ps Category', 'name', 3, true);
  commonFileScenarios.sortFile('associated', 6, true);
  commonFileScenarios.filterFile('0', 'associated', 6);
  for(let k = 0; k < fileData.length; k++) {
    commonFileScenarios.deleteFile(fileData[k].filename);
  }
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'common_client');
}, 'common_client', true);