import loginCommon from '@commonTests/BO/loginBO';

import dashboardPage from '@pages/BO/dashboard';
import themeAndLogoPage from '@pages/BO/design/themeAndLogo/themeAndLogo';
import themeImportPage from '@pages/BO/design/themeAndLogo/themeAndLogo/import';

import helper from '@utils/helpers';
import testContext from '@utils/testContext';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import files from '@utils/files';

function installHummingbird(baseContext: string = 'commonTests-installHummingbird'): void {
  describe('Install Hummingbird theme', async () => {
    let browserContext: BrowserContext;
    let page: Page;

    const urlTheme: string = 'https://github.com/PrestaShop/hummingbird/releases/download/v0.1.6/hummingbird.zip';

    // before and after functions
    before(async function () {
      browserContext = await helper.createBrowserContext(this.browser);
      page = await helper.newTab(browserContext);
    });

    after(async () => {
      await helper.closeBrowserContext(browserContext);
      if (await files.doesFileExist('../../admin-dev/hummingbird.zip')) {
        await files.deleteFile('../../admin-dev/hummingbird.zip');
      }
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
      expect(pageTitle).to.contains(themeAndLogoPage.pageTitle);

      const numThemes = await themeAndLogoPage.getNumberOfThemes(page);
      expect(numThemes).to.eq(1);
    });

    it('should go to \'Add new theme\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewTheme', baseContext);

      await themeAndLogoPage.goToNewThemePage(page);

      const pageTitle = await themeImportPage.getPageTitle(page);
      expect(pageTitle).to.contains(themeImportPage.pageTitle);
    });

    it('should import from the web the Hummingbird theme', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'importTheme', baseContext);

      await themeImportPage.importFromWeb(page, urlTheme);

      const pageTitle = await themeAndLogoPage.getPageTitle(page);
      expect(pageTitle).to.contains(themeAndLogoPage.pageTitle);

      const numThemes = await themeAndLogoPage.getNumberOfThemes(page);
      expect(numThemes).to.eq(2);
    });

    it('should enable the theme Hummingbird', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableThemeHummingbird', baseContext);

      const result = await themeAndLogoPage.enableTheme(page, 'hummingbird');
      expect(result).to.eq(themeAndLogoPage.successfulUpdateMessage);
    });
  });
}

function uninstallHummingbird(baseContext: string = 'commonTests-uninstallHummingbird'): void {
  describe('Uninstall Hummingbird theme', async () => {
    let browserContext: BrowserContext;
    let page: Page;

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
      expect(pageTitle).to.contains(themeAndLogoPage.pageTitle);

      const numThemes = await themeAndLogoPage.getNumberOfThemes(page);
      expect(numThemes).to.eq(2);
    });

    it('should enable the theme Classic', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableThemeClassic', baseContext);

      const result = await themeAndLogoPage.enableTheme(page, 'classic');
      expect(result).to.eq(themeAndLogoPage.successfulUpdateMessage);
    });

    it('should remove the theme Hummingbird', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'removeThemeHummingbird', baseContext);

      const result = await themeAndLogoPage.removeTheme(page, 'hummingbird');
      expect(result).to.eq(themeAndLogoPage.successfulDeleteMessage);

      const numThemes = await themeAndLogoPage.getNumberOfThemes(page);
      expect(numThemes).to.eq(1);
    });
  });
}

export {
  installHummingbird,
  uninstallHummingbird,
};
