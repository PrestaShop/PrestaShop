require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const PagesPage = require('@pages/BO/design/pages/pages');
const FOBasePage = require('@pages/FO/FObasePage');
const CMSPage = require('@pages/FO/cms');
const {Pages} = require('@data/demo/CMSpage');

let browser;
let page;
let numberOfPages = 0;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    pagesPage: new PagesPage(page),
  };
};

// Filter And Quick Edit Pages
describe('Filter And Quick Edit Pages', async () => {
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
  it('should go to "Design>Pages" page', async function () {
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.designParentLink,
      this.pageObjects.boBasePage.pagesLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.pagesPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.pagesPage.pageTitle);
  });
  it('should reset all filters and get number of pages in BO', async function () {
    if (await this.pageObjects.pagesPage.elementVisible(this.pageObjects.pagesPage.pagefilterResetButton, 2000)) {
      await this.pageObjects.pagesPage.resetFilter(this.pageObjects.pagesPage.pagefilterResetButton);
    }
    numberOfPages = await this.pageObjects.pagesPage.getNumberFromText(
      this.pageObjects.pagesPage.pagesGridTitle);
    await expect(numberOfPages).to.be.above(0);
  });
  // 1 : Filter Pages with all inputs and selects in grid table
  describe('Filter Pages', async () => {
    it('should filter by Id \'1\'', async function () {
      await this.pageObjects.pagesPage.filterPages(
        'input',
        'id_cms',
        Pages.delivery.id,
      );
      const numberOfPagesAfterFilter = await this.pageObjects.pagesPage.getNumberFromText(
        this.pageObjects.pagesPage.pagesGridTitle);
      if (numberOfPages === 0) {
        await expect(numberOfPagesAfterFilter).to.be.equal(numberOfPages + 1);
      } else await expect(numberOfPagesAfterFilter).to.be.at.most(numberOfPages);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfPagesAfterFilter; i++) {
        const textColumn = await this.pageObjects.pagesPage.getTextContent(
          this.pageObjects.pagesPage.pagesListTableColumn.replace('%ROW', i).replace('%COLUMN', 'id_cms'),
        );
        await expect(textColumn).to.contains(Pages.delivery.id);
      }
      /* eslint-enable no-await-in-loop */
    });
    it('should reset all filters', async function () {
      if (await this.pageObjects.pagesPage.elementVisible(this.pageObjects.pagesPage.pagefilterResetButton, 2000)) {
        await this.pageObjects.pagesPage.resetFilter(this.pageObjects.pagesPage.pagefilterResetButton);
      }
      const numberOfPagesAfterReset = await this.pageObjects.pagesPage.getNumberFromText(
        this.pageObjects.pagesPage.pagesGridTitle);
      await expect(numberOfPagesAfterReset).to.be.equal(numberOfPages);
    });
    it('should filter by URL \'about-us\'', async function () {
      await this.pageObjects.pagesPage.filterPages(
        'input',
        'link_rewrite',
        Pages.aboutUs.url,
      );
      const numberOfPagesAfterFilter = await this.pageObjects.pagesPage.getNumberFromText(
        this.pageObjects.pagesPage.pagesGridTitle);
      if (numberOfPages === 0) {
        await expect(numberOfPagesAfterFilter).to.be.equal(numberOfPages + 1);
      } else await expect(numberOfPagesAfterFilter).to.be.at.most(numberOfPages);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfPagesAfterFilter; i++) {
        const textColumn = await this.pageObjects.pagesPage.getTextContent(
          this.pageObjects.pagesPage.pagesListTableColumn.replace('%ROW', i).replace('%COLUMN', 'link_rewrite'),
        );
        await expect(textColumn).to.contains(Pages.aboutUs.url);
      }
      /* eslint-enable no-await-in-loop */
    });
    it('should reset all filters', async function () {
      if (await this.pageObjects.pagesPage.elementVisible(this.pageObjects.pagesPage.pagefilterResetButton, 2000)) {
        await this.pageObjects.pagesPage.resetFilter(this.pageObjects.pagesPage.pagefilterResetButton);
      }
      const numberOfPagesAfterReset = await this.pageObjects.pagesPage.getNumberFromText(
        this.pageObjects.pagesPage.pagesGridTitle);
      await expect(numberOfPagesAfterReset).to.be.equal(numberOfPages);
    });
    it('should filter by Title \'Terms and conditions of use\'', async function () {
      await this.pageObjects.pagesPage.filterPages(
        'input',
        'meta_title',
        Pages.termsAndCondition.title,
      );
      const numberOfPagesAfterFilter = await this.pageObjects.pagesPage.getNumberFromText(
        this.pageObjects.pagesPage.pagesGridTitle);
      if (numberOfPages === 0) {
        await expect(numberOfPagesAfterFilter).to.be.equal(numberOfPages + 1);
      } else await expect(numberOfPagesAfterFilter).to.be.at.most(numberOfPages);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfPagesAfterFilter; i++) {
        const textColumn = await this.pageObjects.pagesPage.getTextContent(
          this.pageObjects.pagesPage.pagesListTableColumn.replace('%ROW', i).replace('%COLUMN', 'meta_title'),
        );
        await expect(textColumn).to.contains(Pages.termsAndCondition.title);
      }
      /* eslint-enable no-await-in-loop */
    });
    it('should reset all filters', async function () {
      if (await this.pageObjects.pagesPage.elementVisible(this.pageObjects.pagesPage.pagefilterResetButton, 2000)) {
        await this.pageObjects.pagesPage.resetFilter(this.pageObjects.pagesPage.pagefilterResetButton);
      }
      const numberOfPagesAfterReset = await this.pageObjects.pagesPage.getNumberFromText(
        this.pageObjects.pagesPage.pagesGridTitle);
      await expect(numberOfPagesAfterReset).to.be.equal(numberOfPages);
    });
    it('should filter by Position \'5\'', async function () {
      await this.pageObjects.pagesPage.filterPages(
        'input',
        'position',
        Pages.securePayment.position,
      );
      const numberOfPagesAfterFilter = await this.pageObjects.pagesPage.getNumberFromText(
        this.pageObjects.pagesPage.pagesGridTitle);
      if (numberOfPages === 0) {
        await expect(numberOfPagesAfterFilter).to.be.equal(numberOfPages + 1);
      } else await expect(numberOfPagesAfterFilter).to.be.at.most(numberOfPages);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfPagesAfterFilter; i++) {
        const textColumn = await this.pageObjects.pagesPage.getTextContent(
          this.pageObjects.pagesPage.pagesListTableColumn.replace('%ROW', i).replace('%COLUMN', 'position'),
        );
        await expect(textColumn).to.contains(Pages.securePayment.position);
      }
      /* eslint-enable no-await-in-loop */
    });
    it('should reset all filters', async function () {
      if (await this.pageObjects.pagesPage.elementVisible(this.pageObjects.pagesPage.pagefilterResetButton, 2000)) {
        await this.pageObjects.pagesPage.resetFilter(this.pageObjects.pagesPage.pagefilterResetButton);
      }
      const numberOfPagesAfterReset = await this.pageObjects.pagesPage.getNumberFromText(
        this.pageObjects.pagesPage.pagesGridTitle);
      await expect(numberOfPagesAfterReset).to.be.equal(numberOfPages);
    });
    it('should filter by Displayed \'Yes\'', async function () {
      await this.pageObjects.pagesPage.filterPages(
        'input',
        'active',
        Pages.securePayment.displayed,
      );
      const numberOfPagesAfterFilter = await this.pageObjects.pagesPage.getNumberFromText(
        this.pageObjects.pagesPage.pagesGridTitle);
      if (numberOfPages === 0) {
        await expect(numberOfPagesAfterFilter).to.be.equal(numberOfPages + 1);
      } else await expect(numberOfPagesAfterFilter).to.be.at.most(numberOfPages);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfPagesAfterFilter; i++) {
        const textColumn = await this.pageObjects.pagesPage.getTextContent(
          this.pageObjects.pagesPage.pagesListTableColumn.replace('%ROW', i).replace('%COLUMN', 'active'),
        );
        await expect(textColumn).to.contains(Pages.securePayment.position);
      }
      /* eslint-enable no-await-in-loop */
    });
  });
});
