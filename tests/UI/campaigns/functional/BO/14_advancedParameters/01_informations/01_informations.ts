// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boDashboardPage,
  boInformationPage,
  boLoginPage,
  type BrowserContext,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_advancedParameters_informations_informations';

describe('BO - Advanced Parameters: Informations', async () => {
  let browserContext: BrowserContext;
  let page: Page;

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

  it('should go to \'Advanced Parameters > Informations\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToInformationsPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.advancedParametersLink,
      boDashboardPage.informationLink,
    );

    const pageTitle = await boInformationPage.getPageTitle(page);
    expect(pageTitle).to.contains(boInformationPage.pageTitle);
  });

  it('should check blocks', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkBlocks', baseContext);

    const isBlockConfigurationInformationVisible = await boInformationPage.isBlockConfigurationInformationVisible(page);
    expect(isBlockConfigurationInformationVisible).to.equal(true);

    const isBlockServerInformationVisible = await boInformationPage.isBlockServerInformationVisible(page);
    expect(isBlockServerInformationVisible).to.equal(true);

    const isBlockDatabaseInformationVisible = await boInformationPage.isBlockDatabaseInformationVisible(page);
    expect(isBlockDatabaseInformationVisible).to.equal(true);

    const isBlockStoreInformationVisible = await boInformationPage.isBlockStoreInformationVisible(page);
    expect(isBlockStoreInformationVisible).to.equal(true);

    const isBlockMailConfigurationVisible = await boInformationPage.isBlockMailConfigurationVisible(page);
    expect(isBlockMailConfigurationVisible).to.equal(true);

    const isBlockYourInformationVisible = await boInformationPage.isBlockYourInformationVisible(page);
    expect(isBlockYourInformationVisible).to.equal(true);

    const isBlockCheckYourConfigurationVisible = await boInformationPage.isBlockCheckYourConfigurationVisible(page);
    expect(isBlockCheckYourConfigurationVisible).to.equal(true);
  });

  it('should check there are no overrides', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkNoOverrides', baseContext);

    const isBlockListOverridesVisible = await boInformationPage.isBlockListOverridesVisible(page);
    expect(isBlockListOverridesVisible).to.equal(true);

    const hasOverrides = await boInformationPage.hasOverrides(page);
    expect(hasOverrides).to.equal(false);
  });

  it('should check there are no changed files', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkNoChangedFiles', baseContext);

    const isBlockListChangedFilesVisible = await boInformationPage.isBlockListChangedFilesVisible(page);
    expect(isBlockListChangedFilesVisible).to.equal(true);

    const hasChangedFiles = await boInformationPage.hasChangedFiles(page);
    expect(hasChangedFiles).to.equal(false);
  });

  it('should open the help side bar and check the document language', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'openHelpSidebar', baseContext);

    const isHelpSidebarVisible = await boInformationPage.openHelpSideBar(page);
    expect(isHelpSidebarVisible).to.eq(true);

    const documentURL = await boInformationPage.getHelpDocumentURL(page);
    expect(documentURL).to.contains('country=en');
  });

  it('should close the help side bar', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'closeHelpSidebar', baseContext);

    const isHelpSidebarClosed = await boInformationPage.closeHelpSideBar(page);
    expect(isHelpSidebarClosed).to.eq(true);
  });
});
