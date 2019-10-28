require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
const brandsCommon = require('@commonTests/BO/BOBase');
const {demoBrands} = require('@data/demo/brands');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const BrandsPage = require('@pages/BO/brands');

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
  brandsCommon.goToBrandsPage();

  it('should reset all filters and get Number of brands in BO', async function () {
    numberOfBrands = await this.pageObjects.brandsPage.resetFilters('manufacturer');
    await expect(numberOfBrands).to.be.above(0);
  });

  // 1 : Filter brands
  describe('Filter brands', async () => {
    it('should filter by Id', async function () {
      await this.pageObjects.brandsPage.filterBrands('input', 'id_manufacturer', demoBrands.first.id);
      const numberOfBrandsAfterFilter = await this.pageObjects.brandsPage.getNumberFromText(
        this.pageObjects.brandsPage.gridHeaderTitle.replace('%TABLE', 'manufacturer'),
      );
      await expect(numberOfBrandsAfterFilter).to.be.at.most(numberOfBrands);
      const textColumn = await this.pageObjects.brandsPage.getTextContent(
        this.pageObjects.brandsPage.tableColumn
          .replace('%TABLE', 'manufacturer')
          .replace('%ROW', 1)
          .replace('%COLUMN', 'id_manufacturer'),
      );
      await expect(textColumn).to.contains(demoBrands.first.id);
    });

    it('should reset all filters', async function () {
      const numberOfBrandsAfterReset = await this.pageObjects.brandsPage.resetFilters('manufacturer');
      await expect(numberOfBrandsAfterReset).to.equal(numberOfBrands);
    });

    it('should filter by brand name', async function () {
      await this.pageObjects.brandsPage.filterBrands('input', 'name', demoBrands.first.name);
      const numberOfBrandsAfterFilter = await this.pageObjects.brandsPage.getNumberFromText(
        this.pageObjects.brandsPage.gridHeaderTitle.replace('%TABLE', 'manufacturer'),
      );
      await expect(numberOfBrandsAfterFilter).to.be.at.most(numberOfBrands);
      const textColumn = await this.pageObjects.brandsPage.getTextContent(
        this.pageObjects.brandsPage.tableColumn
          .replace('%TABLE', 'manufacturer')
          .replace('%ROW', 1)
          .replace('%COLUMN', 'name'),
      );
      await expect(textColumn).to.contains(demoBrands.first.name);
    });

    it('should reset all filters', async function () {
      const numberOfBrandsAfterReset = await this.pageObjects.brandsPage.resetFilters('manufacturer');
      await expect(numberOfBrandsAfterReset).to.equal(numberOfBrands);
    });

    it('should filter by Enabled \'Yes\'', async function () {
      await this.pageObjects.brandsPage.filterBrands(
        'select',
        'active',
        demoBrands.first.enabled ? 'Yes' : 'No',
      );
      const numberOfBrandsAfterFilter = await this.pageObjects.brandsPage.getNumberFromText(
        this.pageObjects.brandsPage.gridHeaderTitle.replace('%TABLE', 'manufacturer'),
      );
      await expect(numberOfBrandsAfterFilter).to.be.at.most(numberOfBrands);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfBrandsAfterFilter; i++) {
        const textColumn = await this.pageObjects.brandsPage.getTextContent(
          this.pageObjects.brandsPage.tableColumn
            .replace('%TABLE', 'manufacturer')
            .replace('%ROW', 1)
            .replace('%COLUMN', 'active'),
        );
        await expect(textColumn).to.contains('check');
      }
      /* eslint-enable no-await-in-loop */
    });

    it('should reset all filters', async function () {
      const numberOfBrandsAfterReset = await this.pageObjects.brandsPage.resetFilters('manufacturer');
      await expect(numberOfBrandsAfterReset).to.equal(numberOfBrands);
    });
  });
  // 2 : Edit brands in list
  describe('Quick Edit brands', async () => {
    // Steps
    it('should filter by brand name', async function () {
      await this.pageObjects.brandsPage.filterBrands('input', 'name', demoBrands.first.name);
      const numberOfBrandsAfterFilter = await this.pageObjects.brandsPage.getNumberFromText(
        this.pageObjects.brandsPage.gridHeaderTitle.replace('%TABLE', 'manufacturer'),
      );
      await expect(numberOfBrandsAfterFilter).to.be.at.most(numberOfBrands);
      const textColumn = await this.pageObjects.brandsPage.getTextContent(
        this.pageObjects.brandsPage.tableColumn
          .replace('%TABLE', 'manufacturer')
          .replace('%ROW', 1)
          .replace('%COLUMN', 'name'),
      );
      await expect(textColumn).to.contains(demoBrands.first.name);
    });

    it('should disable first brand', async function () {
      const isActionPerformed = await this.pageObjects.brandsPage.updateEnabledValue(
        '1',
        false,
      );
      if (isActionPerformed) {
        const resultMessage = await this.pageObjects.brandsPage.getTextContent(
          this.pageObjects.brandsPage.alertSuccessBlockParagraph,
        );
        await expect(resultMessage).to.contains(this.pageObjects.brandsPage.successfulUpdateStatusMessage);
      }
      const isStatusChanged = await this.pageObjects.brandsPage.elementVisible(
        this.pageObjects.brandsPage.brandsEnableColumnNotValidIcon.replace('%ROW', 1),
        100,
      );
      await expect(isStatusChanged).to.be.true;
    });

    it('should enable first brand', async function () {
      const isActionPerformed = await this.pageObjects.brandsPage.updateEnabledValue('1', true);
      if (isActionPerformed) {
        const resultMessage = await this.pageObjects.brandsPage.getTextContent(
          this.pageObjects.brandsPage.alertSuccessBlockParagraph,
        );
        await expect(resultMessage).to.contains(this.pageObjects.brandsPage.successfulUpdateStatusMessage);
      }
      const isStatusChanged = await this.pageObjects.brandsPage.elementVisible(
        this.pageObjects.brandsPage.brandsEnableColumnValidIcon.replace('%ROW', 1),
        100,
      );
      await expect(isStatusChanged).to.be.true;
    });

    it('should reset all filters', async function () {
      const numberOfBrandsAfterReset = await this.pageObjects.brandsPage.resetFilters('manufacturer');
      await expect(numberOfBrandsAfterReset).to.equal(numberOfBrands);
    });
  });
});
