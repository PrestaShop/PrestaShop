const {AccessPageBO} = require('../../../selectors/BO/access_page');
const common_scenarios = require('./file');

let fileData = [{
  filename: 'Ps Picture',
  description: 'Picture of prestashop',
  file: 'prestashop.png'
}, {
  filename: 'Ps Category',
  description: 'Picture of category',
  file: 'category_image.png'
}], fileEditedData = {
  filename: 'PS Developer Guide',
  description: 'The technical documentation of prestashop',
  file: 'prestashop_developer_guide.pdf'
};

scenario('Create, edit, delete and check "Files" in the Back Office', () => {
  scenario('Open the browser and connect to the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should log in successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');
  common_scenarios.createFile(fileData[0].filename, fileData[0].description, fileData[0].file);
  common_scenarios.createFile(fileData[1].filename, fileData[1].description, fileData[1].file);
  common_scenarios.checkFile(fileData[0].filename, fileData[0].description);
  common_scenarios.checkFile(fileData[1].filename, fileData[1].description);
  common_scenarios.editFile(fileData[1].filename, fileEditedData.filename, fileEditedData.description, fileEditedData.file);
  common_scenarios.checkFile(fileEditedData.filename, fileEditedData.description);
  common_scenarios.deleteFile(fileData[0].filename);
  common_scenarios.deleteFile(fileEditedData.filename);
  common_scenarios.createFile(fileEditedData.filename, fileEditedData.description, fileEditedData.file);
  common_scenarios.deleteFileWithBulkAction(fileEditedData.filename);
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'common_client');
}, 'common_client', true);