require('module-alias/register');

const {expect} = require('chai');

const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const TaxesPage = require('@pages/BO/international/taxes');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_international_taxes_pagination';

let browserContext;
let page;

let numberOfTaxes = 0;

// Init objects needed
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    taxesPage: new TaxesPage(page),
  };
};

describe('Taxes pagination', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    this.pageObjects = await init();
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // Login into BO
  loginCommon.loginBO();

  it('should go to taxes page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToTaxesPage', baseContext);

    await this.pageObjects.dashboardPage.goToSubMenu(
      this.pageObjects.dashboardPage.internationalParentLink,
      this.pageObjects.dashboardPage.taxesLink,
    );

    await this.pageObjects.taxesPage.closeSfToolBar();

    const pageTitle = await this.pageObjects.taxesPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.taxesPage.pageTitle);
  });

  it('should reset all filters and get number of taxes in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfTaxes = await this.pageObjects.taxesPage.resetAndGetNumberOfLines();
    await expect(numberOfTaxes).to.be.at.least(11);

    const paginationLabelText = await this.pageObjects.taxesPage.getPaginationLabel();
    await expect(paginationLabelText).to.contains('(page 1 / 1)');
  });

  describe('Pagination next and previous', async () => {
    it('should change the item number to 10 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo10', baseContext);

      const paginationNumber = await this.pageObjects.taxesPage.selectPaginationLimit('10');
      expect(paginationNumber).to.contains('(page 1 / 4)');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await this.pageObjects.taxesPage.paginationNext();
      await expect(paginationNumber).to.contains('(page 2 / 4)');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await this.pageObjects.taxesPage.paginationPrevious();
      await expect(paginationNumber).to.contains('(page 1 / 4)');
    });

    it('should change the item number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo50', baseContext);

      const paginationNumber = await this.pageObjects.taxesPage.selectPaginationLimit('50');
      await expect(paginationNumber).to.contains('(page 1 / 1)');
    });
  });
});
