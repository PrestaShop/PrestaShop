require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const files = require('@utils/files');

// Import BO commons tests
const {createEmployeeTest, deleteEmployeeTest} = require('@commonTests/BO/advancedParameters/createDeleteEmployee');
const {setPermissions} = require('@commonTests/BO/advancedParameters/setPermissions');

// Import pages
const loginPage = require('@pages/BO/login/index');
const dashboardPage = require('@pages/BO/dashboard');
const productsPage = require('@pages/BO/catalog/products/index');
const employeesPage = require('@pages/BO/advancedParameters/team/index');
const myProfilePage = require('@pages/BO/advancedParameters/team/myProfile');

// Import data
const EmployeeFaker = require('@data/faker/employee');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_header_myProfile';

const employeeData = new EmployeeFaker({
  defaultPage: 'Products',
  language: 'English (English)',
  permissionProfile: 'Salesman',
});
const permissionProfileData = [
  {
    className: 'AdminEmployees',
    accesses: [
      'all',
    ],
  },
  {
    className: 'AdminParentEmployees',
    accesses: [
      'all',
    ],
  },
];

let browserContext;
let page;

describe('BO - My profile', async () => {
  // Pre-condition: Create new employee
  createEmployeeTest(employeeData, `${baseContext}_preTest_1`);

  // Pre-condition: Set all access to Team > Employee page
  setPermissions(employeeData.permissionProfile, permissionProfileData, `${baseContext}_preTest_2`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Go to employee page', async () => {
    it('should login by new employee account', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginWithNewEmployee', baseContext);

      await loginPage.goTo(page, global.BO.URL);
      await loginPage.login(page, employeeData.email, employeeData.password);

      const pageTitle = await productsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should go to \'Your profile\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage1', baseContext);

      await dashboardPage.goToMyProfile(page);

      await myProfilePage.closeSfToolBar(page);

      const pageTitle = await myProfilePage.getPageTitle(page);
      await expect(pageTitle).to.contains(myProfilePage.pageTitleEdit);
    });
  });

  describe('Edit the profile', async () => {
    it('should update firstname and lastname with invalid values and check error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateProfile', baseContext);

      employeeData.firstName = 'Hello222';
      employeeData.lastName = 'World333';

      await myProfilePage.updateEditEmployee(page, employeeData.password, employeeData);

      const textResult = await myProfilePage.getAlertError(page);
      await expect(textResult).to.equal(myProfilePage.errorInvalidFirstNameMessage);

      const lastNameResult = await myProfilePage.getInputValue(page, myProfilePage.lastNameInput);
      await expect(lastNameResult).to.equal(employeeData.lastName);

      const firstNameResult = await myProfilePage.getInputValue(page, myProfilePage.firstNameInput);
      await expect(firstNameResult).to.equal(employeeData.firstName);
    });

    it('should update firstname with valid value and lastname with invalid value and check error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateProfile', baseContext);

      employeeData.firstName = 'Hello man';

      await myProfilePage.updateEditEmployee(page, employeeData.password, employeeData);

      const textResult = await myProfilePage.getAlertError(page);
      await expect(textResult).to.equal(myProfilePage.errorInvalidLastNameMessage);

      const lastNameResult = await myProfilePage.getInputValue(page, myProfilePage.lastNameInput);
      await expect(lastNameResult).to.equal(employeeData.lastName);

      const firstNameResult = await myProfilePage.getInputValue(page, myProfilePage.firstNameInput);
      await expect(firstNameResult).to.equal(employeeData.firstName);
    });

    it('should update firstname and lastname with valid values', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateProfile', baseContext);

      employeeData.firstName = 'Hello';
      employeeData.lastName = 'World';

      await myProfilePage.updateEditEmployee(page, employeeData.password, employeeData);

      const textResult = await myProfilePage.getAlertSuccess(page);
      await expect(textResult).to.equal(myProfilePage.successfulUpdateMessage);

      const lastNameResult = await myProfilePage.getInputValue(page, myProfilePage.lastNameInput);
      await expect(lastNameResult).to.equal(employeeData.lastName);

      const firstNameResult = await myProfilePage.getInputValue(page, myProfilePage.firstNameInput);
      await expect(firstNameResult).to.equal(employeeData.firstName);

      const pageTitle = await myProfilePage.getPageTitle(page);
      await expect(pageTitle).to.contains(`${myProfilePage.pageTitleEdit} ${employeeData.lastName} ${
        employeeData.firstName}`);
    });

    it('should upload an invalid format image and check error message', async function () {
      await files.createSVGFile('.', 'image.svg');

      employeeData.avatarFile = 'image.svg';

      await myProfilePage.updateEditEmployee(page, employeeData.password, employeeData);

      const textResult = await myProfilePage.getAlertError(page);
      await expect(textResult).to.equal(myProfilePage.errorInvalidFormatImageMessage);

      await files.deleteFile('image.svg');
    });

    it('should upload a valid format image', async function () {
      await files.generateImage('image.jpg');

      employeeData.avatarFile = 'image.jpg';

      await myProfilePage.updateEditEmployee(page, employeeData.password, employeeData);

      const textResult = await myProfilePage.getAlertSuccess(page);
      await expect(textResult).to.equal(myProfilePage.successfulUpdateMessage);

      await files.deleteFile('image.jpg');
      employeeData.avatarFile = null;
    });
  });

  /*
  describe('Edit the profile with valid values', async() => {
    it('should go to \'Your profile\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMyProfile', baseContext);

      await dashboardPage.goToMyProfile(page);

      const pageTitle = await myProfilePage.getPageTitle(page);
      await expect(pageTitle).to.contains(myProfilePage.pageTitleEdit);
    });

    it('should logout from BO', async function () {
      await loginCommon.logoutBO(this, page);
    });

    it('should check the password and the default page', async function() {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPasswordAndDefaultPage', baseContext);

      await loginPage.goTo(page, global.BO.URL);
      await loginPage.login(page, editEmployeeData.email, editEmployeeData.password);

      const pageTitle = await ordersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should check the firstname', async function() {
      await testContext.addContextItem(this, 'testIdentifier', 'checkFirstname', baseContext);

      const firstname = await ordersPage.getFirstname(page);
      await expect(firstname).to.contains(editEmployeeData.firstName);
    });
  });
  */
  describe('Delete the account and check error', async () => {
    it('should go to \'Advanced Parameters > Team\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToTeamPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.advancedParametersLink,
        dashboardPage.teamLink,
      );

      const pageTitle = await employeesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(employeesPage.pageTitle);
    });

    it('should filter list by email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterEmployeesToDelete', baseContext);

      await employeesPage.filterEmployees(page, 'input', 'email', employeeData.email);

      const textEmail = await employeesPage.getTextColumnFromTable(page, 1, 'email');
      await expect(textEmail).to.contains(employeeData.email);
    });

    it('should delete employee and check error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteEmployee', baseContext);

      const textResult = await employeesPage.deleteEmployeeAndFail(page, 1);
      await expect(textResult).to.equal(employeesPage.errorDeleteOwnAccountMessage);
    });
  });

  // Post-condition: Delete employee
  deleteEmployeeTest(employeeData, `${baseContext}_postTest_1`);
});
