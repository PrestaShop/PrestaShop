// Import utils
import testContext from '@utils/testContext';

// Import pages

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  boDashboardPage,
  boLoginPage,
  boDesignLinkListPage,
  boDesignLinkListCreatePage,
  dataHooks,
  dataLinkWidgets,
  FakerLinkWidget,
  foClassicHomePage,
  type LinkWidgetPage,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_design_linkList_CRUDLinkBlock';

/*
Create link block
Check existence in FO
Delete link block
 */
describe('BO - Design - Link block : CRUD link block', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfLinkWidgetInFooter: number = 0;
  const linkBlockData: FakerLinkWidget = new FakerLinkWidget({
    name: 'Footer test block',
    frName: 'Test block dans le footer',
    hook: dataHooks.displayFooter,
    contentPages: ['Delivery'],
    productsPages: ['New products'],
    staticPages: ['Contact us'],
    customPages: [{name: 'Home in footer', url: global.FO.URL}],
  });
  const updateLinkBlockData: FakerLinkWidget = new FakerLinkWidget({
    name: 'Products section',
    hook: dataHooks.displayFooterBefore,
    contentPages: ['Delivery'],
    productsPages: ['Prices drop'],
    categoriesPages: ['Accessories', 'Art'],
    staticPages: ['Contact us', 'My account', 'Custom content'],
    customPages: [{name: 'Home in footer', url: global.FO.URL}],
  });

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

  it('should go to \'Design > Link block\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLinkWidgetPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.designParentLink,
      boDashboardPage.linkWidgetLink,
    );
    await boDesignLinkListPage.closeSfToolBar(page);

    const pageTitle = await boDesignLinkListPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDesignLinkListPage.pageTitle);
  });

  it('should get link block number', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'getLinkBlockNumber', baseContext);

    numberOfLinkWidgetInFooter = await boDesignLinkListPage.getNumberOfElementInGrid(page, dataHooks.displayFooter.name);
    expect(numberOfLinkWidgetInFooter).to.be.above(0);
  });

  describe('Create link block', async () => {
    it('should go to add new link block page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewLinkBlockPage', baseContext);

      await boDesignLinkListPage.goToNewLinkWidgetPage(page);

      const pageTitle = await boDesignLinkListCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boDesignLinkListCreatePage.pageTitle);
    });

    it('should create link block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createFooterLinkBlock', baseContext);

      const textResult = await boDesignLinkListCreatePage.addLinkWidget(page, linkBlockData);
      expect(textResult).to.equal(boDesignLinkListPage.successfulCreationMessage);

      const numberOfLinkWidget = await boDesignLinkListPage.getNumberOfElementInGrid(page, dataHooks.displayFooter.name);
      expect(numberOfLinkWidget).to.equal(numberOfLinkWidgetInFooter + 1);
    });
  });

  describe('Go to FO and check existence of new link Block', async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop', baseContext);

      // View shop
      page = await boDesignLinkListPage.viewMyShop(page);
      // Change FO language
      await foClassicHomePage.changeLanguage(page, 'en');

      const pageTitle = await foClassicHomePage.getPageTitle(page);
      expect(pageTitle).to.contains(foClassicHomePage.pageTitle);
    });

    it('should check link block in the footer of home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkLinBlockInFO', baseContext);

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

      const pageTitle = await boDesignLinkListPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDesignLinkListPage.pageTitle);
    });
  });

  describe('Update link block', async () => {
    it('should click on edit block product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToUpdatePage', baseContext);

      await boDesignLinkListPage.goToEditBlock(page, dataHooks.displayFooter.name, 1);

      const pageTitle = await boDesignLinkListCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boDesignLinkListCreatePage.pageTitle);
    });

    it('should update link block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateBlockProduct', baseContext);

      const textResult = await boDesignLinkListCreatePage.addLinkWidget(page, updateLinkBlockData);
      expect(textResult).to.equal(boDesignLinkListPage.successfulCreationMessage);

      const numberOfLinkWidget = await boDesignLinkListPage.getNumberOfElementInGrid(page, dataHooks.displayFooterBefore.name);
      expect(numberOfLinkWidget).to.equal(1);
    });
  });

  describe('Delete link block', async () => {
    it('should delete link block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteLinkBlock', baseContext);

      const textResult = await boDesignLinkListPage.deleteLinkWidget(
        page,
        dataHooks.displayFooter.name,
        numberOfLinkWidgetInFooter + 1,
      );
      expect(textResult).to.equal(boDesignLinkListPage.successfulDeleteMessage);

      const numberOfLinkWidgetAfterDelete = await boDesignLinkListPage.getNumberOfElementInGrid(
        page,
        dataHooks.displayFooter.name,
      );
      expect(numberOfLinkWidgetAfterDelete).to.equal(numberOfLinkWidgetInFooter);
    });
  });
});
