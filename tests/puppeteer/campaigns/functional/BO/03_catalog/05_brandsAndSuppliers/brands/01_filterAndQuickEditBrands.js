require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
const {demoBrands} = require('@data/demo/brands');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const BrandsPage = require('@pages/BO/catalog/brands');

let browser;
let page;
let numberOfBrands = 0;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    brandsPage: new BrandsPage(page),
  };
};

// Filter And Quick Edit brands
describe('Filter And Quick Edit brands', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });
  // Login into BO and go to brands page
  loginCommon.loginBO();

  // GO to Brands Page
  it('should go to brands page', async function () {
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.catalogParentLink,
      this.pageObjects.boBasePage.brandsAndSuppliersLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.brandsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.brandsPage.pageTitle);
  });

  it('should reset all filters and get Number of brands in BO', async function () {
    numberOfBrands = await this.pageObjects.brandsPage.resetAndGetNumberOfLines('manufacturer');
    await expect(numberOfBrands).to.be.above(0);
  });

  // 1 : Filter brands
  describe('Filter brands', async () => {
    const tests = [
      {args: {filterType: 'input', filterBy: 'id_manufacturer', filterValue: demoBrands.first.id}},
      {args: {filterType: 'input', filterBy: 'name', filterValue: demoBrands.first.name}},
      {args: {filterType: 'select', filterBy: 'active', filterValue: demoBrands.first.enabled}, expected: 'check'},
    ];
    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        if (test.args.filterBy === 'active') {
          await this.pageObjects.brandsPage.filterBrandsEnabled(test.args.filterValue);
        } else {
          await this.pageObjects.brandsPage.filterBrands(
            test.args.filterType,
            test.args.filterBy,
            test.args.filterValue,
          );
        }
        const numberOfBrandsAfterFilter = await this.pageObjects.brandsPage.getNumberOfElementInGrid('manufacturer');
        await expect(numberOfBrandsAfterFilter).to.be.at.most(numberOfBrands);
        for (let i = 1; i <= numberOfBrandsAfterFilter; i++) {
          const textColumn = await this.pageObjects.brandsPage.getTextColumnFromTableBrands(i, test.args.filterBy);
          if (test.expected !== undefined) {
            await expect(textColumn).to.contains(test.expected);
          } else {
            await expect(textColumn).to.contains(test.args.filterValue);
          }
        }
      });

      it('should reset all filters', async function () {
        const numberOfBrandsAfterReset = await this.pageObjects.brandsPage.resetAndGetNumberOfLines('manufacturer');
        await expect(numberOfBrandsAfterReset).to.equal(numberOfBrands);
      });
    });
  });
  // 2 : Edit brands in list
  describe('Quick Edit brands', async () => {
    it('should filter by brand name', async function () {
      await this.pageObjects.brandsPage.filterBrands('input', 'name', demoBrands.first.name);
      const numberOfBrandsAfterFilter = await this.pageObjects.brandsPage.getNumberOfElementInGrid('manufacturer');
      await expect(numberOfBrandsAfterFilter).to.be.at.most(numberOfBrands);
      const textColumn = await this.pageObjects.brandsPage.getTextColumnFromTableBrands(1, 'name');
      await expect(textColumn).to.contains(demoBrands.first.name);
    });

    const tests = [
      {args: {action: 'disable', enabledValue: false}},
      {args: {action: 'enable', enabledValue: true}},
    ];

    tests.forEach((test) => {
      it(`should ${test.args.action} first brand`, async function () {
        const isActionPerformed = await this.pageObjects.brandsPage.updateEnabledValue(1, test.args.enabledValue);
        if (isActionPerformed) {
          const resultMessage = await this.pageObjects.brandsPage.getTextContent(
            this.pageObjects.brandsPage.alertSuccessBlockParagraph,
          );
          await expect(resultMessage).to.contains(this.pageObjects.brandsPage.successfulUpdateStatusMessage);
        }
        const isStatusChanged = await this.pageObjects.brandsPage.getToggleColumnValue(1);
        await expect(isStatusChanged).to.be.equal(test.args.enabledValue);
      });
    });

    it('should reset all filters', async function () {
      const numberOfBrandsAfterReset = await this.pageObjects.brandsPage.resetAndGetNumberOfLines('manufacturer');
      await expect(numberOfBrandsAfterReset).to.equal(numberOfBrands);
    });
  });
});
