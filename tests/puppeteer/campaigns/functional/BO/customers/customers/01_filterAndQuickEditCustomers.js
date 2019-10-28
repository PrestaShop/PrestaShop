require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
const {DefaultAccount} = require('@data/demo/customer');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const CustomersPage = require('@pages/BO/customers');

let browser;
let page;
let numberOfCustomers = 0;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    customersPage: new CustomersPage(page),
  };
};

// Filter And Quick Edit Customers
describe('Filter And Quick Edit Customers', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });
  // Login into BO and go to customers page
  loginCommon.loginBO();
  it('should go to Customers page', async function () {
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.customersParentLink,
      this.pageObjects.boBasePage.customersLink,
    );
    const pageTitle = await this.pageObjects.customersPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.customersPage.pageTitle);
  });
  it('should reset all filters and get Number of customers in BO', async function () {
    if (await this.pageObjects.customersPage.elementVisible(this.pageObjects.customersPage.filterResetButton, 2000)) {
      await this.pageObjects.customersPage.resetFilter();
    }
    numberOfCustomers = await this.pageObjects.customersPage.getNumberFromText(
      this.pageObjects.customersPage.customerGridTitle,
    );
    await expect(numberOfCustomers).to.be.above(0);
  });
  // 1 : Filter Customers with all inputs and selects in grid table
  describe('Filter Customers', async () => {
    it('should filter by Id \'2\'', async function () {
      await this.pageObjects.customersPage.filterCustomers('input', 'id_customer', DefaultAccount.id);
      const numberOfCustomersAfterFilter = await this.pageObjects.customersPage.getNumberFromText(
        this.pageObjects.customersPage.customerGridTitle,
      );
      await expect(numberOfCustomersAfterFilter).to.be.at.most(numberOfCustomers);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfCustomersAfterFilter; i++) {
        const textColumn = await this.pageObjects.customersPage.getTextContent(
          this.pageObjects.customersPage.customersListTableColumn.replace('%ROW', i).replace('%COLUMN', 'id_customer'),
        );
        await expect(textColumn).to.contains(DefaultAccount.id);
      }
      /* eslint-enable no-await-in-loop */
    });
    it('should reset all filters', async function () {
      await this.pageObjects.customersPage.resetFilter();
      const numberOfCustomersAfterReset = await this.pageObjects.customersPage.getNumberFromText(
        this.pageObjects.customersPage.customerGridTitle,
      );
      await expect(numberOfCustomersAfterReset).to.equal(numberOfCustomers);
    });
    it('should filter by Social title \'Mr.\'', async function () {
      await this.pageObjects.customersPage.filterCustomers(
        'select',
        'social_title',
        DefaultAccount.socialTitle,
      );
      const numberOfCustomersAfterFilter = await this.pageObjects.customersPage.getNumberFromText(
        this.pageObjects.customersPage.customerGridTitle,
      );
      await expect(numberOfCustomersAfterFilter).to.be.at.most(numberOfCustomers);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfCustomersAfterFilter; i++) {
        const textColumn = await this.pageObjects.customersPage.getTextContent(
          this.pageObjects.customersPage.customersListTableColumn.replace('%ROW', i).replace('%COLUMN', 'social_title'),
        );
        await expect(textColumn).to.contains(DefaultAccount.socialTitle);
      }
      /* eslint-enable no-await-in-loop */
    });
    it('should reset all filters', async function () {
      await this.pageObjects.customersPage.resetFilter();
      const numberOfCustomersAfterReset = await this.pageObjects.customersPage.getNumberFromText(
        this.pageObjects.customersPage.customerGridTitle,
      );
      await expect(numberOfCustomersAfterReset).to.equal(numberOfCustomers);
    });
    it('should filter by First name \'John\'', async function () {
      await this.pageObjects.customersPage.filterCustomers(
        'input',
        'firstname',
        DefaultAccount.firstName,
      );
      const numberOfCustomersAfterFilter = await this.pageObjects.customersPage.getNumberFromText(
        this.pageObjects.customersPage.customerGridTitle,
      );
      await expect(numberOfCustomersAfterFilter).to.be.at.most(numberOfCustomers);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfCustomersAfterFilter; i++) {
        const textColumn = await this.pageObjects.customersPage.getTextContent(
          this.pageObjects.customersPage.customersListTableColumn.replace('%ROW', i).replace('%COLUMN', 'firstname'),
        );
        await expect(textColumn).to.contains(DefaultAccount.firstName);
      }
      /* eslint-enable no-await-in-loop */
    });
    it('should reset all filters', async function () {
      await this.pageObjects.customersPage.resetFilter();
      const numberOfCustomersAfterReset = await this.pageObjects.customersPage.getNumberFromText(
        this.pageObjects.customersPage.customerGridTitle,
      );
      await expect(numberOfCustomersAfterReset).to.equal(numberOfCustomers);
    });
    it('should filter by Last name \'DOE\'', async function () {
      await this.pageObjects.customersPage.filterCustomers(
        'input',
        'lastname',
        DefaultAccount.lastName,
      );
      const numberOfCustomersAfterFilter = await this.pageObjects.customersPage.getNumberFromText(
        this.pageObjects.customersPage.customerGridTitle,
      );
      await expect(numberOfCustomersAfterFilter).to.be.at.most(numberOfCustomers);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfCustomersAfterFilter; i++) {
        const textColumn = await this.pageObjects.customersPage.getTextContent(
          this.pageObjects.customersPage.customersListTableColumn.replace('%ROW', i).replace('%COLUMN', 'lastname'),
        );
        await expect(textColumn).to.contains(DefaultAccount.lastName);
      }
      /* eslint-enable no-await-in-loop */
    });
    it('should reset all filters', async function () {
      await this.pageObjects.customersPage.resetFilter();
      const numberOfCustomersAfterReset = await this.pageObjects.customersPage.getNumberFromText(
        this.pageObjects.customersPage.customerGridTitle,
      );
      await expect(numberOfCustomersAfterReset).to.equal(numberOfCustomers);
    });
    it('should filter by Email \'pub@prestashop.com\'', async function () {
      await this.pageObjects.customersPage.filterCustomers(
        'input',
        'email',
        DefaultAccount.email,
      );
      const numberOfCustomersAfterFilter = await this.pageObjects.customersPage.getNumberFromText(
        this.pageObjects.customersPage.customerGridTitle,
      );
      await expect(numberOfCustomersAfterFilter).to.be.at.most(numberOfCustomers);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfCustomersAfterFilter; i++) {
        const textColumn = await this.pageObjects.customersPage.getTextContent(
          this.pageObjects.customersPage.customersListTableColumn.replace('%ROW', i).replace('%COLUMN', 'email'),
        );
        await expect(textColumn).to.contains(DefaultAccount.email);
      }
      /* eslint-enable no-await-in-loop */
    });
    it('should reset all filters', async function () {
      await this.pageObjects.customersPage.resetFilter();
      const numberOfCustomersAfterReset = await this.pageObjects.customersPage.getNumberFromText(
        this.pageObjects.customersPage.customerGridTitle,
      );
      await expect(numberOfCustomersAfterReset).to.equal(numberOfCustomers);
    });
    it('should filter by Enabled \'Yes\'', async function () {
      await this.pageObjects.customersPage.filterCustomers(
        'select',
        'active',
        DefaultAccount.enabled ? 'Yes' : 'No',
      );
      const numberOfCustomersAfterFilter = await this.pageObjects.customersPage.getNumberFromText(
        this.pageObjects.customersPage.customerGridTitle,
      );
      await expect(numberOfCustomersAfterFilter).to.be.at.most(numberOfCustomers);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfCustomersAfterFilter; i++) {
        const textColumn = await this.pageObjects.customersPage.getTextContent(
          this.pageObjects.customersPage.customersListTableColumn.replace('%ROW', i).replace('%COLUMN', 'active'),
        );
        await expect(textColumn).to.contains('check');
      }
      /* eslint-enable no-await-in-loop */
    });
    it('should reset all filters', async function () {
      await this.pageObjects.customersPage.resetFilter();
      const numberOfCustomersAfterReset = await this.pageObjects.customersPage.getNumberFromText(
        this.pageObjects.customersPage.customerGridTitle);
      await expect(numberOfCustomersAfterReset).to.equal(numberOfCustomers);
    });
    it('should filter by Newsletter \'Yes\'', async function () {
      await this.pageObjects.customersPage.filterCustomers(
        'select',
        'newsletter',
        DefaultAccount.newsletter ? 'Yes' : 'No',
      );
      const numberOfCustomersAfterFilter = await this.pageObjects.customersPage.getNumberFromText(
        this.pageObjects.customersPage.customerGridTitle,
      );
      await expect(numberOfCustomersAfterFilter).to.be.at.most(numberOfCustomers);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfCustomersAfterFilter; i++) {
        const textColumn = await this.pageObjects.customersPage.getTextContent(
          this.pageObjects.customersPage.customersListTableColumn.replace('%ROW', i).replace('%COLUMN', 'newsletter'),
        );
        await expect(textColumn).to.contains('check');
      }
      /* eslint-enable no-await-in-loop */
    });
    it('should reset all filters', async function () {
      await this.pageObjects.customersPage.resetFilter();
      const numberOfCustomersAfterReset = await this.pageObjects.customersPage.getNumberFromText(
        this.pageObjects.customersPage.customerGridTitle,
      );
      await expect(numberOfCustomersAfterReset).to.equal(numberOfCustomers);
    });
    it('should filter by Partner Offers \'YES\'', async function () {
      await this.pageObjects.customersPage.filterCustomers(
        'select',
        'optin',
        DefaultAccount.partnerOffers ? 'Yes' : 'No',
      );
      const numberOfCustomersAfterFilter = await this.pageObjects.customersPage.getNumberFromText(
        this.pageObjects.customersPage.customerGridTitle,
      );
      await expect(numberOfCustomersAfterFilter).to.be.at.most(numberOfCustomers);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfCustomersAfterFilter; i++) {
        const textColumn = await this.pageObjects.customersPage.getTextContent(
          this.pageObjects.customersPage.customersListTableColumn.replace('%ROW', i).replace('%COLUMN', 'optin'),
        );
        await expect(textColumn).to.contains('check');
      }
      /* eslint-enable no-await-in-loop */
    });
    it('should reset all filters', async function () {
      await this.pageObjects.customersPage.resetFilter();
      const numberOfCustomersAfterReset = await this.pageObjects.customersPage.getNumberFromText(
        this.pageObjects.customersPage.customerGridTitle,
      );
      await expect(numberOfCustomersAfterReset).to.equal(numberOfCustomers);
    });
  });
  // 2 : Editing customers from grid table
  describe('Quick Edit Customers', async () => {
    // Steps
    it('should filter by Email \'pub@prestashop.com\'', async function () {
      await this.pageObjects.customersPage.filterCustomers(
        'input',
        'email',
        DefaultAccount.email,
      );
      const numberOfCustomersAfterFilter = await this.pageObjects.customersPage.getNumberFromText(
        this.pageObjects.customersPage.customerGridTitle,
      );
      await expect(numberOfCustomersAfterFilter).to.be.at.above(0);
    });
    it('should disable first Customer', async function () {
      const isActionPerformed = await this.pageObjects.customersPage.updateToggleColumnValue(
        '1',
        'active',
        false,
      );
      if (isActionPerformed) {
        const resultMessage = await this.pageObjects.customersPage.getTextContent(
          this.pageObjects.customersPage.alertSuccessBlockParagraph,
        );
        await expect(resultMessage).to.contains(this.pageObjects.customersPage.successfulUpdateStatusMessage);
      }
      const isStatusChanged = await this.pageObjects.customersPage.elementVisible(
        this.pageObjects.customersPage.customersListColumnNotValidIcon
          .replace('%ROW', 1).replace('%COLUMN', 'active'),
        100,
      );
      await expect(isStatusChanged).to.be.true;
    });
    it('should enable first Customer', async function () {
      const isActionPerformed = await this.pageObjects.customersPage.updateToggleColumnValue(
        '1',
        'active',
        true,
      );
      if (isActionPerformed) {
        const resultMessage = await this.pageObjects.customersPage.getTextContent(
          this.pageObjects.customersPage.alertSuccessBlockParagraph,
        );
        await expect(resultMessage).to.contains(this.pageObjects.customersPage.successfulUpdateStatusMessage);
      }
      const isStatusChanged = await this.pageObjects.customersPage.elementVisible(
        this.pageObjects.customersPage.customersListColumnValidIcon
          .replace('%ROW', 1).replace('%COLUMN', 'active'),
        100,
      );
      await expect(isStatusChanged).to.be.true;
    });
    it('should Change Newsletter to "No" for first Customer', async function () {
      const isActionPerformed = await this.pageObjects.customersPage.updateToggleColumnValue(
        '1',
        'newsletter',
        false,
      );
      if (isActionPerformed) {
        const resultMessage = await this.pageObjects.customersPage.getTextContent(
          this.pageObjects.customersPage.alertSuccessBlockParagraph,
        );
        await expect(resultMessage).to.contains(this.pageObjects.customersPage.successfulUpdateStatusMessage);
      }
      const isStatusChanged = await this.pageObjects.customersPage.elementVisible(
        this.pageObjects.customersPage.customersListColumnNotValidIcon
          .replace('%ROW', 1).replace('%COLUMN', 'newsletter'),
        100,
      );
      await expect(isStatusChanged).to.be.true;
    });
    it('should Change Newsletter to "Yes" for first Customer', async function () {
      const isActionPerformed = await this.pageObjects.customersPage.updateToggleColumnValue(
        '1',
        'newsletter',
        true,
      );
      if (isActionPerformed) {
        const resultMessage = await this.pageObjects.customersPage.getTextContent(
          this.pageObjects.customersPage.alertSuccessBlockParagraph,
        );
        await expect(resultMessage).to.contains(this.pageObjects.customersPage.successfulUpdateStatusMessage);
      }
      const isStatusChanged = await this.pageObjects.customersPage.elementVisible(
        this.pageObjects.customersPage.customersListColumnValidIcon.replace('%ROW', 1)
          .replace('%COLUMN', 'newsletter'),
        100,
      );
      await expect(isStatusChanged).to.be.true;
    });
    it('should Change Partner offers to "No" for first Customer', async function () {
      const isActionPerformed = await this.pageObjects.customersPage.updateToggleColumnValue(
        '1',
        'optin',
        false,
      );
      if (isActionPerformed) {
        const resultMessage = await this.pageObjects.customersPage.getTextContent(
          this.pageObjects.customersPage.alertSuccessBlockParagraph,
        );
        await expect(resultMessage).to.contains(this.pageObjects.customersPage.successfulUpdateStatusMessage);
      }
      const isStatusChanged = await this.pageObjects.customersPage.elementVisible(
        this.pageObjects.customersPage.customersListColumnNotValidIcon.replace('%ROW', 1)
          .replace('%COLUMN', 'optin'),
        100,
      );
      await expect(isStatusChanged).to.be.true;
    });
    it('should Change Partner offers to "Yes" for first Customer', async function () {
      const isActionPerformed = await this.pageObjects.customersPage.updateToggleColumnValue(
        '1',
        'optin',
        true,
      );
      if (isActionPerformed) {
        const resultMessage = await this.pageObjects.customersPage.getTextContent(
          this.pageObjects.customersPage.alertSuccessBlockParagraph,
        );
        await expect(resultMessage).to.contains(this.pageObjects.customersPage.successfulUpdateStatusMessage);
      }
      const isStatusChanged = await this.pageObjects.customersPage.elementVisible(
        this.pageObjects.customersPage.customersListColumnValidIcon.replace('%ROW', 1)
          .replace('%COLUMN', 'optin'),
        100,
      );
      await expect(isStatusChanged).to.be.true;
    });
  });
});
