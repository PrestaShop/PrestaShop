require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const linkWidgetsPage = require('@pages/BO/design/linkWidgets');
const addLinkWidgetPage = require('@pages/BO/design/linkWidgets/add');
const foHomePage = require('@pages/FO/home');

// Import data
const {LinkWidgets} = require('@data/demo/linkWidgets');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_design_linkWidget_createAndCheckFooterLInkWidget';


let browserContext;
let page;
let numberOfLinkWidgetInFooter = 0;

/*
Create link widget
Check existence in FO
Delete link widget created
 */
describe('Create footer link widget and check it in FO', async () => {
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

  it('should go to link Widget page', async function () {
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

      numberOfLinkWidgetInFooter = await linkWidgetsPage.getNumberOfElementInGrid(page, 35);
      await expect(numberOfLinkWidgetInFooter).to.be.above(0);
    });
  });

  describe('Go to FO and check existence of link Widget created', async () => {
    it('should go to FO and check link widget in home page footer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkLinkWidgetInFO', baseContext);

      // View shop
      page = await linkWidgetsPage.viewMyShop(page);

      // Change FO language
      await foHomePage.changeLanguage(page, 'en');
      const title = await foHomePage.getFooterLinksBlockTitle(page, numberOfLinkWidgetInFooter);
      await expect(title).to.contains(LinkWidgets.demo_1.name);

      const linksTextContent = await foHomePage.getFooterLinksTextContent(page, numberOfLinkWidgetInFooter);

      await Promise.all([
        expect(linksTextContent).to.include.members(LinkWidgets.demo_1.contentPages),
        expect(linksTextContent).to.include.members(LinkWidgets.demo_1.productsPages),
        expect(linksTextContent).to.include.members(LinkWidgets.demo_1.staticPages),
        expect(linksTextContent).to.include.members(LinkWidgets.demo_1.customPages.map(el => el.name)),
      ]);

      // Go back to BO
      page = await foHomePage.closePage(browserContext, page, 0);
    });
  });

  describe('Delete link widget created', async () => {
    it('should delete link widget created', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteLinkWidget', baseContext);

      const textResult = await linkWidgetsPage.deleteLinkWidget(page, 35, numberOfLinkWidgetInFooter);
      await expect(textResult).to.equal(linkWidgetsPage.successfulDeleteMessage);

      const numberOfLinkWidgetAfterDelete = await linkWidgetsPage.getNumberOfElementInGrid(page, 35);
      await expect(numberOfLinkWidgetAfterDelete).to.equal(numberOfLinkWidgetInFooter - 1);
    });
  });
});
