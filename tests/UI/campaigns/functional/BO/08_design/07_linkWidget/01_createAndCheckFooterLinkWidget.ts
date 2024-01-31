// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import linkWidgetsPage from '@pages/BO/design/linkWidgets';
import addLinkWidgetPage from '@pages/BO/design/linkWidgets/add';
// Import FO pages
import {homePage as foHomePage} from '@pages/FO/classic/home';

// Import data
import LinkWidgets from '@data/demo/linkWidgets';
import Hooks from '@data/demo/hooks';
import {LinkWidgetPage} from '@data/types/linkWidget';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

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
    expect(pageTitle).to.contains(linkWidgetsPage.pageTitle);
  });

  it('should get link widget number', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'getLinkWidgetNumber', baseContext);

    numberOfLinkWidgetInFooter = await linkWidgetsPage.getNumberOfElementInGrid(page, Hooks.displayFooter.name);
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

      const textResult = await addLinkWidgetPage.addLinkWidget(page, LinkWidgets.demo_1);
      expect(textResult).to.equal(linkWidgetsPage.successfulCreationMessage);

      const numberOfLinkWidget = await linkWidgetsPage.getNumberOfElementInGrid(page, Hooks.displayFooter.name);
      expect(numberOfLinkWidget).to.equal(numberOfLinkWidgetInFooter + 1);
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
      expect(pageTitle).to.contains(foHomePage.pageTitle);
    });

    it('should check link widget in the footer of home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkLinkWidgetInFO', baseContext);

      const title = await foHomePage.getFooterLinksBlockTitle(page, numberOfLinkWidgetInFooter + 1);
      expect(title).to.contains(LinkWidgets.demo_1.name);

      const linksTextContent = await foHomePage.getFooterLinksTextContent(page, numberOfLinkWidgetInFooter + 1);
      await Promise.all([
        expect(linksTextContent).to.include.members(LinkWidgets.demo_1.contentPages),
        expect(linksTextContent).to.include.members(LinkWidgets.demo_1.productsPages),
        expect(linksTextContent).to.include.members(LinkWidgets.demo_1.staticPages),
        expect(linksTextContent).to.include.members(LinkWidgets.demo_1.customPages.map((el: LinkWidgetPage) => el.name)),
      ]);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO', baseContext);

      // Go back to BO
      page = await foHomePage.closePage(browserContext, page, 0);

      const pageTitle = await linkWidgetsPage.getPageTitle(page);
      expect(pageTitle).to.contains(linkWidgetsPage.pageTitle);
    });
  });

  describe('Delete link widget', async () => {
    it('should delete link widget', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteLinkWidget', baseContext);

      const textResult = await linkWidgetsPage.deleteLinkWidget(
        page,
        Hooks.displayFooter.name,
        numberOfLinkWidgetInFooter + 1,
      );
      expect(textResult).to.equal(linkWidgetsPage.successfulDeleteMessage);

      const numberOfLinkWidgetAfterDelete = await linkWidgetsPage.getNumberOfElementInGrid(
        page,
        Hooks.displayFooter.name,
      );
      expect(numberOfLinkWidgetAfterDelete).to.equal(numberOfLinkWidgetInFooter);
    });
  });
});
