// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import login steps
import loginCommon from '@commonTests/BO/loginBO';
import themeImportPage from '@pages/BO/design/themeAndLogo/themeAndLogo/import';

// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import themeAndLogoPage from '@pages/BO/design/themeAndLogo/themeAndLogo';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import files from "@utils/files";
import Modules from "@data/demo/modules";

const baseContext: string = 'functional_BO_design_themeAndLogo_themeAndLogo_addNewTheme';

describe('BO - Design - Theme & Logo : Add new theme', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const urlTheme: string = 'https://github.com/PrestaShop/hummingbird/releases/download/v0.1.5/hummingbird.zip';

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

  it('should go to \'Design > Theme & Logo\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToThemeAndLogoPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.designParentLink,
      dashboardPage.themeAndLogoParentLink,
    );
    await themeAndLogoPage.closeSfToolBar(page);

    const pageTitle = await themeAndLogoPage.getPageTitle(page);
    await expect(pageTitle).to.contains(themeAndLogoPage.pageTitle);
  });

  it('should go to \'Add new theme\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewTheme', baseContext);

    await themeAndLogoPage.goToNewThemePage(page);

    const pageTitle = await themeImportPage.getPageTitle(page);
    await expect(pageTitle).to.contains(themeImportPage.pageTitle);
  });

  it('should download theme from the web', async function(){
    await testContext.addContextItem(this, 'testIdentifier', 'importTheme', baseContext);

    await files.downloadFile(urlTheme, 'theme.zip');

    const found = await files.doesFileExist('module.zip');
    await expect(found).to.be.true;
  });

  it('should import theme from your computer', asyn function(){
    await testContext.addContextItem(this, 'testIdentifier', 'importThemeFromComputer', baseContext);
  });

  it('should import from the web the Hummingbird theme', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'importThemeFromWeb', baseContext);

    await themeImportPage.importTheme(page, urlTheme);

    const pageTitle = await themeAndLogoPage.getPageTitle(page);
    await expect(pageTitle).to.contains(themeAndLogoPage.pageTitle);

    const numThemes = await themeAndLogoPage.getNumberOfThemes(page);
    await expect(numThemes).to.eq(2);
  });
});
