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
    staticPages: ['Contact us', 'My account'],
    customPages: [{name: 'Home in footer', url: global.FO.URL}],
  });
  const secondUpdateLinkBlockData = new FakerLinkWidget({
    name: 'Products',
    hook: dataHooks.displayFooterBefore,
    contentPages: ['Delivery'],
    productsPages: ['Prices drop'],
    categoriesPages: ['Accessories', 'Art'],
    staticPages: ['Contact us', 'My account'],
    customPages: [{name: 'Home in footer', url: global.FO.URL}, {name: 'Home in footer 2', url: global.FO.URL}],
  });

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Create link block with hook \'DisplayFooter\'', async () => {
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
      expect(title).to.contains(linkBlockData.name);

      const linksTextContent = await foClassicHomePage.getFooterLinksTextContent(page, numberOfLinkWidgetInFooter + 1);
      await Promise.all([
        expect(linksTextContent).to.include.members(linkBlockData.contentPages),
        expect(linksTextContent).to.include.members(linkBlockData.productsPages),
        expect(linksTextContent).to.include.members(linkBlockData.staticPages),
        expect(linksTextContent).to.include.members(linkBlockData.customPages.map((el: LinkWidgetPage) => el.name)),
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

  describe('Update link block to hook \'DisplayFooterBefore\'', async () => {
    it('should click on edit block product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToUpdatePage', baseContext);

      await boDesignLinkListPage.goToEditBlock(page, dataHooks.displayFooter.name, 3);

      const pageTitle = await boDesignLinkListCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boDesignLinkListCreatePage.pageTitle);
    });

    it('should update link block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateBlockProduct', baseContext);

      const textResult = await boDesignLinkListCreatePage.addLinkWidget(page, updateLinkBlockData, 2);
      expect(textResult).to.contains(boDesignLinkListPage.successfulUpdateMessage);

      const numberOfLinkWidget = await boDesignLinkListPage.getNumberOfElementInGrid(page, dataHooks.displayFooterBefore.name);
      expect(numberOfLinkWidget).to.equal(1);
    });
  });

  describe('Go to FO and check existence of updated link Block just before the footer', async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop', baseContext);

      // View shop
      page = await boDesignLinkListPage.viewMyShop(page);
      // Change FO language
      await foClassicHomePage.changeLanguage(page, 'en');

      const pageTitle = await foClassicHomePage.getPageTitle(page);
      expect(pageTitle).to.contains(foClassicHomePage.pageTitle);
    });

    it('should check link block before the footer of home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkLinBlockInFO', baseContext);

      const linksTitle = await foClassicHomePage.getFooterLinksBlockTitle(page, 1);
      await expect(linksTitle).to.equal(updateLinkBlockData.name);

      const linksTextContent = await foClassicHomePage.getFooterLinksTextContent(page, 1);
      await Promise.all([
        expect(linksTextContent).to.include.members(updateLinkBlockData.contentPages),
        expect(linksTextContent).to.include.members(updateLinkBlockData.productsPages),
        expect(linksTextContent).to.include.members(updateLinkBlockData.categoriesPages),
        expect(linksTextContent).to.include.members(updateLinkBlockData.staticPages),
        expect(linksTextContent).to.include.members(updateLinkBlockData.customPages.map((el: LinkWidgetPage) => el.name)),
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

      await boDesignLinkListPage.goToEditBlock(page, dataHooks.displayFooterBefore.name, 1);

      const pageTitle = await boDesignLinkListCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boDesignLinkListCreatePage.pageTitle);
    });

    it('should update link block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateBlockProduct', baseContext);

      await boDesignLinkListCreatePage.addCustomPages(page, secondUpdateLinkBlockData.customPages, 3);

      const textResult = await boDesignLinkListCreatePage.saveForm(page);
      expect(textResult).to.contains(boDesignLinkListPage.successfulUpdateMessage);

      const numberOfLinkWidget = await boDesignLinkListPage.getNumberOfElementInGrid(page, dataHooks.displayFooterBefore.name);
      expect(numberOfLinkWidget).to.equal(1);
    });
  });

  describe('Delete updated link block and check the FO', async () => {
    it('should delete link block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteLinkBlock', baseContext);

      const textResult = await boDesignLinkListPage.deleteLinkWidget(page, dataHooks.displayFooterBefore.name, 1);
      expect(textResult).to.equal(boDesignLinkListPage.successfulDeleteMessage);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop', baseContext);

      // View shop
      page = await boDesignLinkListPage.viewMyShop(page);
      // Change FO language
      await foClassicHomePage.changeLanguage(page, 'en');

      const pageTitle = await foClassicHomePage.getPageTitle(page);
      expect(pageTitle).to.contains(foClassicHomePage.pageTitle);
    });

    it('should check the first link block in the footer of home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkLinBlockInFO', baseContext);

      const linksTitle = await foClassicHomePage.getFooterLinksBlockTitle(page, 1);
      await expect(linksTitle).to.not.equal(updateLinkBlockData.name);
    });
  });
});
