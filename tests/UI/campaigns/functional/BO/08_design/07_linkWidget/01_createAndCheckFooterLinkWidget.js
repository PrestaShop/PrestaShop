require('module-alias/register');

// Import expect from chai
const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const linkWidgetsPage = require('@pages/BO/design/linkWidgets');
const addLinkWidgetPage = require('@pages/BO/design/linkWidgets/add');

// Import FO pages
const foHomePage = require('@pages/FO/home');

// Import data
const {LinkWidgets} = require('@data/demo/linkWidgets');
const {hooks} = require('@data/demo/hooks');

const baseContext = 'functional_BO_design_linkWidget_createAndCheckFooterLinkWidget';

let browserContext;
let page;
let numberOfLinkWidgetInFooter = 0;

/*
Create link widget
Check existence in FO
Delete link widget created
 */
describe('BO - Design - Link Widget : Create footer link widget and check it in FO', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  it('should go to \'Design > Link Widget\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLinkWidgetPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.designParentLink,
      dashboardPage.linkWidgetLink,
    );

    await linkWidgetsPage.closeSfToolBar(page);

    const pageTitle = await linkWidgetsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(linkWidgetsPage.pageTitle);
  });

  it('should get link widget number', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'getLinkWidgetNumber', baseContext);

    numberOfLinkWidgetInFooter = await linkWidgetsPage.getNumberOfElementInGrid(page, hooks.displayFooter.name);
    await expect(numberOfLinkWidgetInFooter).to.be.above(0);
  });

  describe('Create link widget', async () => {
    it('should go to add new link widget page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewLinkWidgetPage', baseContext);

      await linkWidgetsPage.goToNewLinkWidgetPage(page);

      const pageTitle = await addLinkWidgetPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addLinkWidgetPage.pageTitle);
    });

    it('should create link widget', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createFooterLInkWidget', baseContext);

      const textResult = await addLinkWidgetPage.addLinkWidget(page, LinkWidgets.demo_1);
      await expect(textResult).to.equal(linkWidgetsPage.successfulCreationMessage);

      const numberOfLinkWidget = await linkWidgetsPage.getNumberOfElementInGrid(page, hooks.displayFooter.name);
      await expect(numberOfLinkWidget).to.equal(numberOfLinkWidgetInFooter + 1);
    });
  });

  describe('Go to FO and check existence of new link Widget', async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop', baseContext);

      // View shop
      page = await linkWidgetsPage.viewMyShop(page);

      // Change FO language
      await foHomePage.changeLanguage(page, 'en');

      const pageTitle = await foHomePage.getPageTitle(page);
      await expect(pageTitle).to.contains(foHomePage.pageTitle);
    });

    it('should check link widget in the footer of home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkLinkWidgetInFO', baseContext);

      const title = await foHomePage.getFooterLinksBlockTitle(page, numberOfLinkWidgetInFooter + 1);
      await expect(title).to.contains(LinkWidgets.demo_1.name);

      const linksTextContent = await foHomePage.getFooterLinksTextContent(page, numberOfLinkWidgetInFooter + 1);

      await Promise.all([
        expect(linksTextContent).to.include.members(LinkWidgets.demo_1.contentPages),
        expect(linksTextContent).to.include.members(LinkWidgets.demo_1.productsPages),
        expect(linksTextContent).to.include.members(LinkWidgets.demo_1.staticPages),
        expect(linksTextContent).to.include.members(LinkWidgets.demo_1.customPages.map(el => el.name)),
      ]);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO', baseContext);

      // Go back to BO
      page = await foHomePage.closePage(browserContext, page, 0);

      const pageTitle = await linkWidgetsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(linkWidgetsPage.pageTitle);
    });
  });

  describe('Delete link widget', async () => {
    it('should delete link widget', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteLinkWidget', baseContext);

      const textResult = await linkWidgetsPage.deleteLinkWidget(
        page,
        hooks.displayFooter.name,
        numberOfLinkWidgetInFooter + 1,
      );
      await expect(textResult).to.equal(linkWidgetsPage.successfulDeleteMessage);

      const numberOfLinkWidgetAfterDelete = await linkWidgetsPage.getNumberOfElementInGrid(
        page,
        hooks.displayFooter.name,
      );
      await expect(numberOfLinkWidgetAfterDelete).to.equal(numberOfLinkWidgetInFooter);
    });
  });
});
