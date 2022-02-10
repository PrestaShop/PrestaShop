require('module-alias/register');

// Import utils
const helper = require('@utils/helpers');
const mailHelper = require('@utils/mailHelper');

// Import common tests
const loginCommon = require('@commonTests/BO/loginBO');
const {setupSmtpConfigTest, resetSmtpConfigTest} = require('@commonTests/BO/advancedParameters/configSMTP');

// Import pages
const loginPage = require('@pages/BO/login/index');
const dashboardPage = require('@pages/BO/dashboard');
const employeesPage = require('@pages/BO/advancedParameters/team/index');
const addEmployeePage = require('@pages/BO/advancedParameters/team/add');

// Import data
const EmployeeFaker = require('@data/faker/employee');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_login_passwordReminder';

// Import expect from chai
const {expect} = require('chai');

let browserContext;
let page;
let numberOfEmployees = 0;

let newMail;
const resetPasswordMailSubject = 'Your new password';

// New employee data
const createEmployeeData = new EmployeeFaker({
  defaultPage: 'Products',
  language: 'English (English)',
  permissionProfile: 'Salesman',
});

// mailListener
let mailListener;

/*
Pre-condition
- Setup SMTP parameters
Scenario:
- Create new employee
- Click on 'I forget my password'
- Check if the email is received
- Delete created employee
Post-condition
- Reset SMTP parameters
 */
describe('BO - Login : Password reminder', async () => {
  // Pre-Condition : Setup config SMTP
  setupSmtpConfigTest(baseContext);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    // Start listening to maildev server
    mailListener = mailHelper.createMailListener();
    mailHelper.startListener(mailListener);

    // Handle every new email
    mailListener.on('new', (email) => {
      newMail = email;
    });
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);

    // Stop listening to maildev server
    mailHelper.stopListener(mailListener);
  });

  describe('Go to BO and create a new employee', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

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

    it('should reset all filters and get number of employees', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

      numberOfEmployees = await employeesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfEmployees).to.be.above(0);
    });

    it('should go to add new employee page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewEmployeePage', baseContext);

      await employeesPage.goToAddNewEmployeePage(page);
      const pageTitle = await addEmployeePage.getPageTitle(page);
      await expect(pageTitle).to.contains(addEmployeePage.pageTitleCreate);
    });

    it('should create employee and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createEmployee', baseContext);

      const textResult = await addEmployeePage.createEditEmployee(page, createEmployeeData);
      await expect(textResult).to.equal(employeesPage.successfulCreationMessage);
    });

    it('should logout from BO', async function () {
      await loginCommon.logoutBO(this, page);
    });
  });

  describe('Go to BO login page and use password reminder link', async () => {
    it('should go to BO login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToBOLoginPage', baseContext);
      await loginPage.goTo(page, global.BO.URL);

      const pageTitle = await loginPage.getPageTitle(page);
      await expect(pageTitle).to.contains(loginPage.pageTitle);
    });

    it('should send reset password mail', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sendResetPasswordMailAndCheckSuccess', baseContext);

      await loginPage.sendResetPasswordLink(page, createEmployeeData.email);

      const successTextContent = await loginPage.getResetPasswordSuccessMessage(page);
      await expect(successTextContent).to.contains(loginPage.resetPasswordSuccessText);
    });

    it('should check if reset password mail is in mailbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkIfResetPasswordMailIsInMailbox', baseContext);

      await expect(newMail.subject).to.contains(resetPasswordMailSubject);
    });
  });

  describe('Go to BO and delete previously created employee', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Advanced Parameters > Team\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEmployeesPageToDelete', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.advancedParametersLink,
        dashboardPage.teamLink,
      );

      const pageTitle = await employeesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(employeesPage.pageTitle);
    });

    it('should filter list of employees by email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterEmployeesToDelete', baseContext);

      await employeesPage.filterEmployees(
        page,
        'input',
        'email',
        createEmployeeData.email,
      );

      const textEmail = await employeesPage.getTextColumnFromTable(page, 1, 'email');
      await expect(textEmail).to.contains(createEmployeeData.email);
    });

    it('should delete employee', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteEmployee', baseContext);

      const textResult = await employeesPage.deleteEmployee(page, 1);
      await expect(textResult).to.equal(employeesPage.successfulDeleteMessage);
    });

    it('should reset filter and check the number of employees', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfEmployeesAfterDelete = await employeesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfEmployeesAfterDelete).to.be.equal(numberOfEmployees);
    });
  });

  // Post-Condition : Reset SMTP config
  resetSmtpConfigTest(baseContext);
});
