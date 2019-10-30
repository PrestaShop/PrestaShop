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
const TaxesPage = require('@pages/BO/taxes');

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
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.internationalParentLink,
      this.pageObjects.boBasePage.taxesLink,
    );
    const pageTitle = await this.pageObjects.taxesPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.taxesPage.pageTitle);
  });

  it('should reset all filters and get Number of Taxes in BO', async function () {
    numberOfTaxes = await this.pageObjects.taxesPage.resetAndGetNumberOfLines();
    await expect(numberOfTaxes).to.be.above(0);
  });
  // 1 : Filter Taxes
  describe('Filter Taxes', async () => {
    it('should filter by Id \'1\'', async function () {
      await this.pageObjects.taxesPage.filterTaxes('input', 'id_tax', DefaultFrTax.id);
      const numberOfTaxesAfterFilter = await this.pageObjects.taxesPage.getNumberFromText(
        this.pageObjects.taxesPage.gridHeaderTitle,
      );
      await expect(numberOfTaxesAfterFilter).to.be.at.most(numberOfTaxes);
      const textColumn = await this.pageObjects.taxesPage.getTextContent(
        this.pageObjects.taxesPage.taxesGridColumn.replace('%ROW', 1).replace('%COLUMN', 'id_tax'),
      );
      await expect(textColumn).to.contains(DefaultFrTax.id);
    });

    it('should reset all filters', async function () {
      const numberOfTaxesAfterReset = await this.pageObjects.taxesPage.resetAndGetNumberOfLines();
      await expect(numberOfTaxesAfterReset).to.equal(numberOfTaxes);
    });

    it('should filter by tax name', async function () {
      await this.pageObjects.taxesPage.filterTaxes('input', 'name', DefaultFrTax.name);
      const numberOfTaxesAfterFilter = await this.pageObjects.taxesPage.getNumberFromText(
        this.pageObjects.taxesPage.gridHeaderTitle,
      );
      await expect(numberOfTaxesAfterFilter).to.be.at.most(numberOfTaxes);
      const textColumn = await this.pageObjects.taxesPage.getTextContent(
        this.pageObjects.taxesPage.taxesGridColumn.replace('%ROW', 1).replace('%COLUMN', 'name'),
      );
      await expect(textColumn).to.contains(DefaultFrTax.name);
    });

    it('should reset all filters', async function () {
      const numberOfTaxesAfterReset = await this.pageObjects.taxesPage.resetAndGetNumberOfLines();
      await expect(numberOfTaxesAfterReset).to.equal(numberOfTaxes);
    });

    it('should filter by Rate', async function () {
      await this.pageObjects.taxesPage.filterTaxes('input', 'rate', DefaultFrTax.rate);
      const numberOfTaxesAfterFilter = await this.pageObjects.taxesPage.getNumberFromText(
        this.pageObjects.taxesPage.gridHeaderTitle,
      );
      await expect(numberOfTaxesAfterFilter).to.be.at.most(numberOfTaxes);
      const textColumn = await this.pageObjects.taxesPage.getTextContent(
        this.pageObjects.taxesPage.taxesGridColumn.replace('%ROW', 1).replace('%COLUMN', 'rate'),
      );
      await expect(textColumn).to.contains(DefaultFrTax.rate);
    });

    it('should reset all filters', async function () {
      const numberOfTaxesAfterReset = await this.pageObjects.taxesPage.resetAndGetNumberOfLines();
      await expect(numberOfTaxesAfterReset).to.equal(numberOfTaxes);
    });

    it('should filter by Enabled \'Yes\'', async function () {
      await this.pageObjects.taxesPage.filterTaxes(
        'select',
        'active',
        DefaultFrTax.enabled,
      );
      const numberOfTaxesAfterFilter = await this.pageObjects.taxesPage.getNumberFromText(
        this.pageObjects.taxesPage.gridHeaderTitle,
      );
      await expect(numberOfTaxesAfterFilter).to.be.at.most(numberOfTaxes);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfTaxesAfterFilter; i++) {
        const textColumn = await this.pageObjects.taxesPage.getTextContent(
          this.pageObjects.taxesPage.taxesGridColumn.replace('%ROW', i).replace('%COLUMN', 'active'),
        );
        await expect(textColumn).to.contains('check');
      }
      /* eslint-enable no-await-in-loop */
    });

    it('should reset all filters', async function () {
      const numberOfTaxesAfterReset = await this.pageObjects.taxesPage.resetAndGetNumberOfLines();
      await expect(numberOfTaxesAfterReset).to.equal(numberOfTaxes);
    });
  });
  // 2 : Edit taxes in list
  describe('Quick Edit Taxes', async () => {
    // Steps
    it('should filter by name', async function () {
      await this.pageObjects.taxesPage.filterTaxes('input', 'name', DefaultFrTax.name);
      const numberOfTaxesAfterFilter = await this.pageObjects.taxesPage.getNumberFromText(
        this.pageObjects.taxesPage.gridHeaderTitle,
      );
      await expect(numberOfTaxesAfterFilter).to.be.at.most(numberOfTaxes);
      const textColumn = await this.pageObjects.taxesPage.getTextContent(
        this.pageObjects.taxesPage.taxesGridColumn.replace('%ROW', 1).replace('%COLUMN', 'name'),
      );
      await expect(textColumn).to.contains(DefaultFrTax.name);
    });

    it('should disable first tax', async function () {
      const isActionPerformed = await this.pageObjects.taxesPage.updateEnabledValue(
        '1',
        false,
      );
      if (isActionPerformed) {
        const resultMessage = await this.pageObjects.taxesPage.getTextContent(
          this.pageObjects.taxesPage.alertSuccessBlockParagraph,
        );
        await expect(resultMessage).to.contains(this.pageObjects.taxesPage.successfulUpdateStatusMessage);
      }
      const isStatusChanged = await this.pageObjects.taxesPage.elementVisible(
        this.pageObjects.taxesPage.toggleColumnNotValidIcon.replace('%ROW', 1)
          .replace('%COLUMN', 'active'),
        100,
      );
      await expect(isStatusChanged).to.be.true;
    });

    it('should enable first tax', async function () {
      const isActionPerformed = await this.pageObjects.taxesPage.updateEnabledValue(
        '1',
        true,
      );
      if (isActionPerformed) {
        const resultMessage = await this.pageObjects.taxesPage.getTextContent(
          this.pageObjects.taxesPage.alertSuccessBlockParagraph,
        );
        await expect(resultMessage).to.contains(this.pageObjects.taxesPage.successfulUpdateStatusMessage);
      }
      const isStatusChanged = await this.pageObjects.taxesPage.elementVisible(
        this.pageObjects.taxesPage.toggleColumnValidIcon.replace('%ROW', 1)
          .replace('%COLUMN', 'active'),
        100,
      );
      await expect(isStatusChanged).to.be.true;
    });

    it('should reset all filters', async function () {
      const numberOfTaxesAfterReset = await this.pageObjects.taxesPage.resetAndGetNumberOfLines();
      await expect(numberOfTaxesAfterReset).to.equal(numberOfTaxes);
    });
  });
});
