import testContext from '@utils/testContext';
import {setupSmtpConfigTest, resetSmtpConfigTest} from '@commonTests/BO/advancedParameters/smtp';
import {expect} from 'chai';

import {
  boDashboardPage,
  boEmployeesPage,
  boEmployeesCreatePage,
  boLoginPage,
  type BrowserContext,
  FakerEmployee,
  type MailDev,
  type MailDevEmail,
  type Page,
  utilsMail,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_login_passwordReminder';

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
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfEmployees: number = 0;
  let newMail: MailDevEmail;
  let mailListener: MailDev;

  const resetPasswordMailSubject: string = 'Your new password';
  const createEmployeeData: FakerEmployee = new FakerEmployee({
    defaultPage: 'Products',
    language: 'English (English)',
    permissionProfile: 'Salesman',
  });

  // Pre-Condition : Setup config SMTP
  setupSmtpConfigTest(baseContext);

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    // Start listening to maildev server
    mailListener = utilsMail.createMailListener();
    utilsMail.startListener(mailListener);

    // Handle every new email
    mailListener.on('new', (email: MailDevEmail) => {
      newMail = email;
    });
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);

    // Stop listening to maildev server
    utilsMail.stopListener(mailListener);
  });

  describe('Go to BO and create a new employee', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

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

    it('should reset all filters and get number of employees', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

      numberOfEmployees = await boEmployeesPage.resetAndGetNumberOfLines(page);
      expect(numberOfEmployees).to.be.above(0);
    });

    it('should go to add new employee page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewEmployeePage', baseContext);

      await boEmployeesPage.goToAddNewEmployeePage(page);

      const pageTitle = await boEmployeesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boEmployeesCreatePage.pageTitleCreate);
    });

    it('should create employee and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createEmployee', baseContext);

      const textResult = await boEmployeesCreatePage.createEditEmployee(page, createEmployeeData);
      expect(textResult).to.equal(boEmployeesPage.successfulCreationMessage);
    });

    it('should logout from BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'logoutBO', baseContext);

      await boDashboardPage.logoutBO(page);

      const pageTitle = await boLoginPage.getPageTitle(page);
      expect(pageTitle).to.contains(boLoginPage.pageTitle);
    });
  });

  describe('Go to BO login page and use password reminder link', async () => {
    it('should go to BO login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToBOLoginPage', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);

      const pageTitle = await boLoginPage.getPageTitle(page);
      expect(pageTitle).to.contains(boLoginPage.pageTitle);
    });

    it('should send reset password mail', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sendResetPasswordMailAndCheckSuccess', baseContext);

      await boLoginPage.sendResetPasswordLink(page, createEmployeeData.email);

      const successTextContent = await boLoginPage.getResetPasswordSuccessMessage(page);
      expect(successTextContent).to.contains(boLoginPage.resetPasswordSuccessText);
    });

    it('should check if reset password mail is in mailbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkIfResetPasswordMailIsInMailbox', baseContext);

      expect(newMail.subject).to.contains(resetPasswordMailSubject);
    });
  });

  describe('Go to BO and delete previously created employee', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Advanced Parameters > Team\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEmployeesPageToDelete', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.advancedParametersLink,
        boDashboardPage.teamLink,
      );

      const pageTitle = await boEmployeesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boEmployeesPage.pageTitle);
    });

    it('should filter list of employees by email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterEmployeesToDelete', baseContext);

      await boEmployeesPage.filterEmployees(
        page,
        'input',
        'email',
        createEmployeeData.email,
      );

      const textEmail = await boEmployeesPage.getTextColumnFromTable(page, 1, 'email');
      expect(textEmail).to.contains(createEmployeeData.email);
    });

    it('should delete employee', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteEmployee', baseContext);

      const textResult = await boEmployeesPage.deleteEmployee(page, 1);
      expect(textResult).to.equal(boEmployeesPage.successfulDeleteMessage);
    });

    it('should reset filter and check the number of employees', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfEmployeesAfterDelete = await boEmployeesPage.resetAndGetNumberOfLines(page);
      expect(numberOfEmployeesAfterDelete).to.be.equal(numberOfEmployees);
    });
  });

  // Post-Condition : Reset SMTP config
  resetSmtpConfigTest(baseContext);
});
