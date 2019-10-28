require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const StocksPage = require('@pages/BO/stocks');

let browser;
let page;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    stocksPage: new StocksPage(page),
  };
};

// Filter And Quick Edit Stocks
describe('Filter And Quick Edit Stocks', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });
  // Login into BO and go to categories page
  loginCommon.loginBO();

  it('should go to "Catalog>Stocks" page', async function () {
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.productsParentLink,
      this.pageObjects.boBasePage.stocksLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.categoriesPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.stocksPage.pageTitle);
  });

  it('should get number of products in list', async function () {
    numberOfProducts = await this.pageObjects.stocksPage.getNumberOfPRoductsFromList();
    await expect(numberOfProducts).to.be.above(0);
  });


  // 1 : Filter products with name, reference, supplier
  describe('Filter products', async () => {
    it('should filter by name \'mug\'', async function () {
      await this.pageObjects.stocksPage.simpleFilter(
        'mug',
      );
      const numberOfProductsAfterFilter = await this.pageObjects.stocksPage.getNumberOfProductsFromList();
      await expect(numberOfProductsAfterFilter).to.be.at.most(numberOfProducts);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfProductsAfterFilter; i++) {
        const textColumn = await this.pageObjects.stocksPage.getTextContent(
          this.pageObjects.stocksPage.productRowNameColumn.replace('%ROW', i));
        await expect(textColumn).to.contains('mug');
      }
      /* eslint-enable no-await-in-loop */
    });
    it('should reset all filters', async function () {
      await this.pageObjects.categoriesPage.resetFilter();
      const numberOfCategoriesAfterReset = await this.pageObjects.categoriesPage.getNumberFromText(
        this.pageObjects.categoriesPage.categoryGridTitle);
      await expect(numberOfCategoriesAfterReset).to.equal(numberOfCategories);
    });
    it('should filter by Name \'Accessories\'', async function () {
      await this.pageObjects.categoriesPage.filterCategories(
        'input',
        'name',
        Categories.accessories.name,
      );
      const numberOfCategoriesAfterFilter = await this.pageObjects.categoriesPage.getNumberFromText(
        this.pageObjects.categoriesPage.categoryGridTitle);
      await expect(numberOfCategoriesAfterFilter).to.be.at.most(numberOfCategories);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfCategoriesAfterFilter; i++) {
        const textColumn = await this.pageObjects.categoriesPage.getTextContent(
          this.pageObjects.categoriesPage.categoriesListTableColumn.replace('%ROW', i).replace('%COLUMN', 'name'),
        );
        await expect(textColumn).to.contains(Categories.accessories.name);
      }
      /* eslint-enable no-await-in-loop */
    });
    it('should reset all filters', async function () {
      await this.pageObjects.categoriesPage.resetFilter();
      const numberOfCategoriesAfterReset = await this.pageObjects.categoriesPage.getNumberFromText(
        this.pageObjects.categoriesPage.categoryGridTitle);
      await expect(numberOfCategoriesAfterReset).to.equal(numberOfCategories);
    });
    it('should filter by Description', async function () {
      await this.pageObjects.categoriesPage.filterCategories(
        'input',
        'description',
        Categories.accessories.description,
      );
      const numberOfCategoriesAfterFilter = await this.pageObjects.categoriesPage.getNumberFromText(
        this.pageObjects.categoriesPage.categoryGridTitle);
      await expect(numberOfCategoriesAfterFilter).to.be.at.most(numberOfCategories);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfCategoriesAfterFilter; i++) {
        const textColumn = await this.pageObjects.categoriesPage.getTextContent(
          this.pageObjects.categoriesPage.categoriesListTableColumn
            .replace('%ROW', i).replace('%COLUMN', 'description'),
        );
        await expect(textColumn).to.contains(Categories.accessories.description);
      }
      /* eslint-enable no-await-in-loop */
    });
    it('should reset all filters', async function () {
      await this.pageObjects.categoriesPage.resetFilter();
      const numberOfCategoriesAfterReset = await this.pageObjects.categoriesPage.getNumberFromText(
        this.pageObjects.categoriesPage.categoryGridTitle);
      await expect(numberOfCategoriesAfterReset).to.equal(numberOfCategories);
    });
    it('should filter by Position \'3\'', async function () {
      await this.pageObjects.categoriesPage.filterCategories(
        'input',
        'position',
        Categories.art.position,
      );
      const numberOfCategoriesAfterFilter = await this.pageObjects.categoriesPage.getNumberFromText(
        this.pageObjects.categoriesPage.categoryGridTitle);
      await expect(numberOfCategoriesAfterFilter).to.be.at.most(numberOfCategories);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfCategoriesAfterFilter; i++) {
        const textColumn = await this.pageObjects.categoriesPage.getTextContent(
          this.pageObjects.categoriesPage.categoriesListTableColumn.replace('%ROW', i).replace('%COLUMN', 'position'),
        );
        await expect(textColumn).to.contains(Categories.art.position);
      }
      /* eslint-enable no-await-in-loop */
    });
    it('should reset all filters', async function () {
      await this.pageObjects.categoriesPage.resetFilter();
      const numberOfCategoriesAfterReset = await this.pageObjects.categoriesPage.getNumberFromText(
        this.pageObjects.categoriesPage.categoryGridTitle);
      await expect(numberOfCategoriesAfterReset).to.equal(numberOfCategories);
    });
    it('should filter by Displayed \'Yes\'', async function () {
      await this.pageObjects.categoriesPage.filterCategories(
        'select',
        'active',
        Categories.art.displayed,
      );
      const numberOfCategoriesAfterFilter = await this.pageObjects.categoriesPage.getNumberFromText(
        this.pageObjects.categoriesPage.categoryGridTitle);
      await expect(numberOfCategoriesAfterFilter).to.be.at.most(numberOfCategories);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfCategoriesAfterFilter; i++) {
        const textColumn = await this.pageObjects.categoriesPage.getTextContent(
          this.pageObjects.categoriesPage.categoriesListTableColumn.replace('%ROW', i).replace('%COLUMN', 'active'),
        );
        await expect(textColumn).to.contains('check');
      }
      /* eslint-enable no-await-in-loop */
    });
    it('should reset all filters', async function () {
      await this.pageObjects.categoriesPage.resetFilter();
      const numberOfCategoriesAfterReset = await this.pageObjects.categoriesPage.getNumberFromText(
        this.pageObjects.categoriesPage.categoryGridTitle);
      await expect(numberOfCategoriesAfterReset).to.equal(numberOfCategories);
    });
  });
});

