// Import utils
import testContext from '@utils/testContext';

// Import pages
// Import BO pages
import linkWidgetsPage from '@pages/BO/design/linkWidgets';
import addLinkWidgetPage from '@pages/BO/design/linkWidgets/add';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  dataHooks,
  dataLinkWidgets,
  foClassicHomePage,
  type LinkWidgetPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_design_linkWidget_createAndCheckFooterLinkWidget';

/*
Create link widget
Check existence in FO
Delete link widget created
 */
describe('BO - Design - Link Widget : Create footer link widget and check it in FO', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfLinkWidgetInFooter: number = 0;

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  it('should go to \'Design > Link Widget\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLinkWidgetPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.designParentLink,
      boDashboardPage.linkWidgetLink,
    );
    await linkWidgetsPage.closeSfToolBar(page);

    const pageTitle = await linkWidgetsPage.getPageTitle(page);
    expect(pageTitle).to.contains(linkWidgetsPage.pageTitle);
  });

  it('should get link widget number', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'getLinkWidgetNumber', baseContext);

    numberOfLinkWidgetInFooter = await linkWidgetsPage.getNumberOfElementInGrid(page, dataHooks.displayFooter.name);
    expect(numberOfLinkWidgetInFooter).to.be.above(0);
  });

  describe('Create link widget', async () => {
    it('should go to add new link widget page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewLinkWidgetPage', baseContext);

      await linkWidgetsPage.goToNewLinkWidgetPage(page);

      const pageTitle = await addLinkWidgetPage.getPageTitle(page);
      expect(pageTitle).to.contains(addLinkWidgetPage.pageTitle);
    });

    it('should create link widget', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createFooterLInkWidget', baseContext);

      const textResult = await addLinkWidgetPage.addLinkWidget(page, dataLinkWidgets.demo_1);
      expect(textResult).to.equal(linkWidgetsPage.successfulCreationMessage);

      const numberOfLinkWidget = await linkWidgetsPage.getNumberOfElementInGrid(page, dataHooks.displayFooter.name);
      expect(numberOfLinkWidget).to.equal(numberOfLinkWidgetInFooter + 1);
    });
  });

  describe('Go to FO and check existence of new link Widget', async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop', baseContext);

      // View shop
      page = await linkWidgetsPage.viewMyShop(page);
      // Change FO language
      await foClassicHomePage.changeLanguage(page, 'en');

      const pageTitle = await foClassicHomePage.getPageTitle(page);
      expect(pageTitle).to.contains(foClassicHomePage.pageTitle);
    });

    it('should check link widget in the footer of home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkLinkWidgetInFO', baseContext);

      const title = await foClassicHomePage.getFooterLinksBlockTitle(page, numberOfLinkWidgetInFooter + 1);
      expect(title).to.contains(dataLinkWidgets.demo_1.name);

      const linksTextContent = await foClassicHomePage.getFooterLinksTextContent(page, numberOfLinkWidgetInFooter + 1);
      await Promise.all([
        expect(linksTextContent).to.include.members(dataLinkWidgets.demo_1.contentPages),
        expect(linksTextContent).to.include.members(dataLinkWidgets.demo_1.productsPages),
        expect(linksTextContent).to.include.members(dataLinkWidgets.demo_1.staticPages),
        expect(linksTextContent).to.include.members(dataLinkWidgets.demo_1.customPages.map((el: LinkWidgetPage) => el.name)),
      ]);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO', baseContext);

      // Go back to BO
      page = await foClassicHomePage.closePage(browserContext, page, 0);

      const pageTitle = await linkWidgetsPage.getPageTitle(page);
      expect(pageTitle).to.contains(linkWidgetsPage.pageTitle);
    });
  });

  describe('Delete link widget', async () => {
    it('should delete link widget', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteLinkWidget', baseContext);

      const textResult = await linkWidgetsPage.deleteLinkWidget(
        page,
        dataHooks.displayFooter.name,
        numberOfLinkWidgetInFooter + 1,
      );
      expect(textResult).to.equal(linkWidgetsPage.successfulDeleteMessage);

      const numberOfLinkWidgetAfterDelete = await linkWidgetsPage.getNumberOfElementInGrid(
        page,
        dataHooks.displayFooter.name,
      );
      expect(numberOfLinkWidgetAfterDelete).to.equal(numberOfLinkWidgetInFooter);
    });
  });
});
