// Import utils
import files from '@utils/files';
import helper from '@utils/helpers';
import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

// Import test context
import testContext from '@utils/testContext';

// Import BO commons tests
import {createEmployeeTest, deleteEmployeeTest} from '@commonTests/BO/advancedParameters/employee';
import setPermissions from '@commonTests/BO/advancedParameters/setPermissions';
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import loginPage from '@pages/BO/login';
import dashboardPage from '@pages/BO/dashboard';
import productsPage from '@pages/BO/catalog/products';
import creditSlipsPage from '@pages/BO/orders/creditSlips';
import employeesPage from '@pages/BO/advancedParameters/team';
import myProfilePage from '@pages/BO/advancedParameters/team/myProfile';

// Import data
import EmployeeData from '@data/faker/employee';
import type {EmployeePermission} from '@data/types/employee';

const baseContext: string = 'functional_BO_header_myProfile';

describe('BO - Header : My profile', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const employeeData = new EmployeeData({
    defaultPage: 'Products',
    language: 'English (English)',
    permissionProfile: 'Salesman',
  });
  const permissionProfileData: EmployeePermission[] = [
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
      await loginPage.successLogin(page, employeeData.email, employeeData.password);

      const pageTitle = await productsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should go to \'Your profile\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMyProfilePage', baseContext);

      await dashboardPage.goToMyProfile(page);
      await myProfilePage.closeSfToolBar(page);

      const pageTitle = await myProfilePage.getPageTitle(page);
      await expect(pageTitle).to.contains(myProfilePage.pageTitleEdit(employeeData.lastName, employeeData.firstName));
    });
  });

  describe('Edit the profile', async () => {
    it('should update firstname and lastname with invalid values and check error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkInvalidFirstNameAndLastName', baseContext);

      employeeData.firstName = 'Hello222';
      employeeData.lastName = 'World333';

      await myProfilePage.updateEditEmployee(page, employeeData.password, employeeData);

      const textResult = await myProfilePage.getAlertError(page);
      await expect(textResult).to.equal(myProfilePage.errorInvalidFirstNameMessage);

      const lastNameResult = await myProfilePage.getInputValue(page, 'lastname');
      await expect(lastNameResult).to.equal(employeeData.lastName);

      const firstNameResult = await myProfilePage.getInputValue(page, 'firstname');
      await expect(firstNameResult).to.equal(employeeData.firstName);
    });

    it('should update with valid firstname and invalid lastname and check error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkValidFirstNameAndInvalidLastName', baseContext);

      employeeData.firstName = 'Hello man';

      await myProfilePage.updateEditEmployee(page, employeeData.password, employeeData);

      const textResult = await myProfilePage.getAlertError(page);
      await expect(textResult).to.equal(myProfilePage.errorInvalidLastNameMessage);

      const lastNameResult = await myProfilePage.getInputValue(page, 'lastname');
      await expect(lastNameResult).to.equal(employeeData.lastName);

      const firstNameResult = await myProfilePage.getInputValue(page, 'firstname');
      await expect(firstNameResult).to.equal(employeeData.firstName);
    });

    it('should update firstname and lastname with valid values', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkValidFirstNameAndLastName', baseContext);

      employeeData.firstName = 'Hello';
      employeeData.lastName = 'World';

      await myProfilePage.updateEditEmployee(page, employeeData.password, employeeData);

      const textResult = await myProfilePage.getAlertSuccess(page);
      await expect(textResult).to.equal(myProfilePage.successfulUpdateMessage);

      const lastNameResult = await myProfilePage.getInputValue(page, 'lastname');
      await expect(lastNameResult).to.equal(employeeData.lastName);

      const firstNameResult = await myProfilePage.getInputValue(page, 'firstname');
      await expect(firstNameResult).to.equal(employeeData.firstName);

      const pageTitle = await myProfilePage.getPageTitle(page);
      await expect(pageTitle).to.contains(myProfilePage.pageTitleEdit(employeeData.lastName, employeeData.firstName));
    });

    it('should upload an invalid format image and check error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkInvalidFormatImage', baseContext);
      await files.createSVGFile('.', 'image.svg');

      employeeData.avatarFile = 'image.svg';

      await myProfilePage.updateEditEmployee(page, employeeData.password, employeeData);

      const textResult = await myProfilePage.getAlertError(page);
      await expect(textResult).to.contains(myProfilePage.errorInvalidFormatImageMessage);

      // Delete created file
      await files.deleteFile('image.svg');
    });

    it('should upload a valid format image', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkValidFormatImage', baseContext);
      await files.generateImage('image.jpg');

      employeeData.avatarFile = 'image.jpg';

      await myProfilePage.updateEditEmployee(page, employeeData.password, employeeData);

      const textResult = await myProfilePage.getAlertSuccess(page);
      await expect(textResult).to.equal(myProfilePage.successfulUpdateMessage);

      // Delete created file
      await files.deleteFile('image.jpg');
      // Reset value
      employeeData.avatarFile = null;
    });

    it('should enable Gravatar', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkGravatar', baseContext);
      employeeData.enableGravatar = true;

      await myProfilePage.updateEditEmployee(page, employeeData.password, employeeData);

      const textResult = await myProfilePage.getAlertSuccess(page);
      await expect(textResult).to.equal(myProfilePage.successfulUpdateMessage);

      const isChecked = await myProfilePage.isGravatarEnabled(page);
      await expect(isChecked).to.be.true;

      const avatarURL = await myProfilePage.getCurrentEmployeeAvatar(page);
      await expect(avatarURL).to.contains('https://www.gravatar.com/avatar/');

      // Reset value
      employeeData.enableGravatar = false;
    });

    it('should update all others fields', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateAllOthersFields', baseContext);
      employeeData.email = 'demo1@prestashop.com';
      employeeData.password = 'prestashop_demo';
      employeeData.language = 'English (English)';
      employeeData.defaultPage = 'Credit Slips';

      await myProfilePage.updateEditEmployee(page, employeeData.password, employeeData);

      const textResult = await myProfilePage.getAlertSuccess(page);
      await expect(textResult).to.equal(myProfilePage.successfulUpdateMessage);
    });

    it('should logout from BO', async function () {
      await loginCommon.logoutBO(this, page);
    });

    it('should check the password and the default page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPasswordAndDefaultPageAndLanguage', baseContext);

      await loginPage.goTo(page, global.BO.URL);
      await loginPage.successLogin(page, employeeData.email, employeeData.password);

      const pageTitle = await creditSlipsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(creditSlipsPage.pageTitle);
    });

    it('should reset the language', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetLanguage', baseContext);

      await dashboardPage.goToMyProfile(page);

      employeeData.language = 'English (English)';

      await myProfilePage.updateEditEmployee(page, employeeData.password, employeeData);

      const textResult = await myProfilePage.getAlertSuccess(page);
      await expect(textResult).to.equal(myProfilePage.successfulUpdateMessage);
    });
  });

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
