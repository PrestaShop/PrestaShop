const {AccessPageBO} = require('../../../selectors/BO/access_page');
const commonFileScenarios = require('../../common_scenarios/file');
const welcomeScenarios = require('../../common_scenarios/welcome');
let fileData = {
    filename: 'Ps Picture',
    description: 'Picture of prestashop',
    file: 'prestashop.png'
  },
  fileEditedData = {
    filename: 'PS Developer Guide',
    description: 'The technical documentation of prestashop',
    file: 'prestashop_developer_guide.pdf'
  };

scenario('Create, edit, view, delete and check "Files" in the Back Office', () => {
  scenario('Open the browser and connect to the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should log in successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');
  welcomeScenarios.findAndCloseWelcomeModal();
  commonFileScenarios.createFile(fileData.filename, fileData.description, fileData.file);
  commonFileScenarios.checkFile(fileData.filename, fileData.description);
  commonFileScenarios.editFile(fileData.filename, fileEditedData.filename, fileEditedData.description, fileEditedData.file);
  commonFileScenarios.checkFile(fileEditedData.filename, fileEditedData.description);
  commonFileScenarios.viewFile(global.downloadsFolderPath, fileEditedData.filename, fileEditedData.file);
  commonFileScenarios.deleteFile(fileEditedData.filename);
  commonFileScenarios.createFile(fileEditedData.filename, fileEditedData.description, fileEditedData.file);
  commonFileScenarios.createFile(fileEditedData.filename, fileEditedData.description, fileEditedData.file);
  commonFileScenarios.deleteFileWithBulkAction(fileEditedData.filename);
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'common_client');
}, 'common_client', true);
