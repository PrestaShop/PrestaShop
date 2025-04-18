import {expect} from 'chai';
import testContext from '@utils/testContext';

// Import BO commons tests
import {createEmployeeTest, deleteEmployeeTest} from '@commonTests/BO/advancedParameters/employee';
import setPermissions from '@commonTests/BO/advancedParameters/setPermissions';

import {
  boCreditSlipsPage,
  boDashboardPage,
  boEmployeesPage,
  boLoginPage,
  boMyProfilePage,
  boProductsPage,
  type BrowserContext,
  type EmployeePermission,
  FakerEmployee,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_header_myProfile';

describe('BO - Header : My profile', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const employeeData: FakerEmployee = new FakerEmployee({
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
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Go to employee page', async () => {
    it('should login by new employee account', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginWithNewEmployee', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, employeeData.email, employeeData.password);

      const pageTitle = await boProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsPage.pageTitle);
    });

    it('should go to \'Your profile\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMyProfilePage', baseContext);

      await boDashboardPage.goToMyProfile(page);
      await boMyProfilePage.closeSfToolBar(page);

      const pageTitle = await boMyProfilePage.getPageTitle(page);
      expect(pageTitle).to.contains(boMyProfilePage.pageTitleEdit(employeeData.lastName, employeeData.firstName));
    });
  });

  describe('Edit the profile', async () => {
    it('should update firstname and lastname with invalid values and check error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkInvalidFirstNameAndLastName', baseContext);

      employeeData.firstName = 'Hello222';
      employeeData.lastName = 'World333';

      await boMyProfilePage.updateEditEmployee(page, employeeData.password, employeeData);

      const textResult = await boMyProfilePage.getAlertError(page);
      expect(textResult).to.equal(boMyProfilePage.errorInvalidFirstNameMessage);

      const lastNameResult = await boMyProfilePage.getInputValue(page, 'lastname');
      expect(lastNameResult).to.equal(employeeData.lastName);

      const firstNameResult = await boMyProfilePage.getInputValue(page, 'firstname');
      expect(firstNameResult).to.equal(employeeData.firstName);
    });

    it('should update with valid firstname and invalid lastname and check error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkValidFirstNameAndInvalidLastName', baseContext);

      employeeData.firstName = 'Hello man';

      await boMyProfilePage.updateEditEmployee(page, employeeData.password, employeeData);

      const textResult = await boMyProfilePage.getAlertError(page);
      expect(textResult).to.equal(boMyProfilePage.errorInvalidLastNameMessage);

      const lastNameResult = await boMyProfilePage.getInputValue(page, 'lastname');
      expect(lastNameResult).to.equal(employeeData.lastName);

      const firstNameResult = await boMyProfilePage.getInputValue(page, 'firstname');
      expect(firstNameResult).to.equal(employeeData.firstName);
    });

    it('should update firstname and lastname with valid values', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkValidFirstNameAndLastName', baseContext);

      employeeData.firstName = 'Hello';
      employeeData.lastName = 'World';

      await boMyProfilePage.updateEditEmployee(page, employeeData.password, employeeData);

      const textResult = await boMyProfilePage.getAlertSuccess(page);
      expect(textResult).to.equal(boMyProfilePage.successfulUpdateMessage);

      const lastNameResult = await boMyProfilePage.getInputValue(page, 'lastname');
      expect(lastNameResult).to.equal(employeeData.lastName);

      const firstNameResult = await boMyProfilePage.getInputValue(page, 'firstname');
      expect(firstNameResult).to.equal(employeeData.firstName);

      const pageTitle = await boMyProfilePage.getPageTitle(page);
      expect(pageTitle).to.contains(boMyProfilePage.pageTitleEdit(employeeData.lastName, employeeData.firstName));
    });

    it('should upload an invalid format image and check error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkInvalidFormatImage', baseContext);
      await utilsFile.createSVGFile('.', 'image.svg');

      employeeData.avatarFile = 'image.svg';

      await boMyProfilePage.updateEditEmployee(page, employeeData.password, employeeData);

      const textResult = await boMyProfilePage.getAlertError(page);
      expect(textResult).to.contains(boMyProfilePage.errorInvalidFormatImageMessage);

      // Delete created file
      await utilsFile.deleteFile('image.svg');
    });

    it('should upload a valid format image', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkValidFormatImage', baseContext);
      await utilsFile.generateImage('image.jpg');

      employeeData.avatarFile = 'image.jpg';

      await boMyProfilePage.updateEditEmployee(page, employeeData.password, employeeData);

      const textResult = await boMyProfilePage.getAlertSuccess(page);
      expect(textResult).to.equal(boMyProfilePage.successfulUpdateMessage);

      // Delete created file
      await utilsFile.deleteFile('image.jpg');
      // Reset value
      employeeData.avatarFile = null;
    });

    it('should enable Gravatar', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkGravatar', baseContext);
      employeeData.enableGravatar = true;

      await boMyProfilePage.updateEditEmployee(page, employeeData.password, employeeData);

      const textResult = await boMyProfilePage.getAlertSuccess(page);
      expect(textResult).to.equal(boMyProfilePage.successfulUpdateMessage);

      const isChecked = await boMyProfilePage.isGravatarEnabled(page);
      expect(isChecked).to.eq(true);

      const avatarURL = await boMyProfilePage.getCurrentEmployeeAvatar(page);
      expect(avatarURL).to.contains('https://www.gravatar.com/avatar/');

      // Reset value
      employeeData.enableGravatar = false;
    });

    it('should update all others fields', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateAllOthersFields', baseContext);
      employeeData.email = 'demo1@prestashop.com';
      employeeData.password = 'prestashop_demo';
      employeeData.language = 'English (English)';
      employeeData.defaultPage = 'Credit Slips';

      await boMyProfilePage.updateEditEmployee(page, employeeData.password, employeeData);

      const textResult = await boMyProfilePage.getAlertSuccess(page);
      expect(textResult).to.equal(boMyProfilePage.successfulUpdateMessage);
    });

    it('should logout from BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'logoutBO', baseContext);

      await boDashboardPage.logoutBO(page);

      const pageTitle = await boLoginPage.getPageTitle(page);
      expect(pageTitle).to.contains(boLoginPage.pageTitle);
    });

    it('should check the password and the default page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPasswordAndDefaultPageAndLanguage', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, employeeData.email, employeeData.password);

      const pageTitle = await boCreditSlipsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCreditSlipsPage.pageTitle);
    });

    it('should reset the language', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetLanguage', baseContext);

      await boDashboardPage.goToMyProfile(page);

      employeeData.language = 'English (English)';

      await boMyProfilePage.updateEditEmployee(page, employeeData.password, employeeData);

      const textResult = await boMyProfilePage.getAlertSuccess(page);
      expect(textResult).to.equal(boMyProfilePage.successfulUpdateMessage);
    });
  });

  describe('Delete the account and check error', async () => {
    it('should go to \'Advanced Parameters > Team\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToTeamPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.advancedParametersLink,
        boDashboardPage.teamLink,
      );

      const pageTitle = await boEmployeesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boEmployeesPage.pageTitle);
    });

    it('should filter list by email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterEmployeesToDelete', baseContext);

      await boEmployeesPage.filterEmployees(page, 'input', 'email', employeeData.email);

      const textEmail = await boEmployeesPage.getTextColumnFromTable(page, 1, 'email');
      expect(textEmail).to.contains(employeeData.email);
    });

    it('should delete employee and check error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteEmployee', baseContext);

      const textResult = await boEmployeesPage.deleteEmployeeAndFail(page, 1);
      expect(textResult).to.equal(boEmployeesPage.errorDeleteOwnAccountMessage);
    });
  });

  // Post-condition: Delete employee
  deleteEmployeeTest(employeeData, `${baseContext}_postTest_1`);
});
