require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
const {DefaultFrTax} = require('@data/demo/tax');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const TaxesPage = require('@pages/BO/international/taxes');
// Test context imports
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_international_localization_taxes_filterAndQuickEditTaxes';

let browser;
let page;
let numberOfTaxes = 0;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    taxesPage: new TaxesPage(page),
  };
};

// Filter And Quick Edit taxes
describe('Filter And Quick Edit taxes', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });
  // Login into BO and go to taxes page
  loginCommon.loginBO();

  it('should go to Taxes page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToTaxesPage', baseContext);
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.internationalParentLink,
      this.pageObjects.boBasePage.taxesLink,
    );
    const pageTitle = await this.pageObjects.taxesPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.taxesPage.pageTitle);
  });

  it('should reset all filters and get Number of Taxes in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);
    numberOfTaxes = await this.pageObjects.taxesPage.resetAndGetNumberOfLines();
    await expect(numberOfTaxes).to.be.above(0);
  });
  // 1 : Filter Taxes
  describe('Filter Taxes', async () => {
    const tests = [
      {
        args: {
          testIdentifier: 'filterId', filterType: 'input', filterBy: 'id_tax', filterValue: DefaultFrTax.id.toString(),
        },
      },
      {
        args: {
          testIdentifier: 'filterName', filterType: 'input', filterBy: 'name', filterValue: DefaultFrTax.name,
        },
      },
      {
        args:
          {
            testIdentifier: 'filterRate',
            filterType: 'input',
            filterBy: 'rate',
            filterValue: DefaultFrTax.rate.toString(),
          },
      },
      {
        args:
          {
            testIdentifier: 'filterActive', filterType: 'select', filterBy: 'active', filterValue: DefaultFrTax.enabled,
          },
        expected: 'check',
      },
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);
        await this.pageObjects.taxesPage.filterTaxes(test.args.filterType, test.args.filterBy, test.args.filterValue);
        const numberOfTaxesAfterFilter = await this.pageObjects.taxesPage.getNumberOfElementInGrid();
        await expect(numberOfTaxesAfterFilter).to.be.at.most(numberOfTaxes);
        for (let i = 1; i <= numberOfTaxesAfterFilter; i++) {
          const textColumn = await this.pageObjects.taxesPage.getTextColumnFromTableTaxes(i, test.args.filterBy);
          if (test.expected !== undefined) {
            await expect(textColumn).to.contains(test.expected);
          } else {
            await expect(textColumn).to.contains(test.args.filterValue);
          }
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);
        const numberOfTaxesAfterReset = await this.pageObjects.taxesPage.resetAndGetNumberOfLines();
        await expect(numberOfTaxesAfterReset).to.equal(numberOfTaxes);
      });
    });
  });
  // 2 : Edit taxes in list
  describe('Quick Edit Taxes', async () => {
    it('should filter by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForQuickEdit', baseContext);
      await this.pageObjects.taxesPage.filterTaxes('input', 'name', DefaultFrTax.name);
      const numberOfTaxesAfterFilter = await this.pageObjects.taxesPage.getNumberOfElementInGrid();
      await expect(numberOfTaxesAfterFilter).to.be.at.most(numberOfTaxes);
      const textColumn = await this.pageObjects.taxesPage.getTextColumnFromTableTaxes(1, 'name');
      await expect(textColumn).to.contains(DefaultFrTax.name);
    });

    const tests = [
      {args: {action: 'disable', column: 'active', enabledValue: false}},
      {args: {action: 'enable', column: 'active', enabledValue: true}},
    ];
    tests.forEach((test) => {
      it(`should ${test.args.action} first tax`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}Tax`, baseContext);
        const isActionPerformed = await this.pageObjects.taxesPage.updateEnabledValue(
          1,
          test.args.enabledValue,
        );
        if (isActionPerformed) {
          const resultMessage = await this.pageObjects.taxesPage.getTextContent(
            this.pageObjects.taxesPage.alertSuccessBlockParagraph,
          );
          await expect(resultMessage).to.contains(this.pageObjects.taxesPage.successfulUpdateStatusMessage);
        }
        const isStatusChanged = await this.pageObjects.taxesPage.getToggleColumnValue(1, 'active');
        await expect(isStatusChanged).to.be.equal(test.args.enabledValue);
      });
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterQuickEdit', baseContext);
      const numberOfTaxesAfterReset = await this.pageObjects.taxesPage.resetAndGetNumberOfLines();
      await expect(numberOfTaxesAfterReset).to.equal(numberOfTaxes);
    });
  });
});
