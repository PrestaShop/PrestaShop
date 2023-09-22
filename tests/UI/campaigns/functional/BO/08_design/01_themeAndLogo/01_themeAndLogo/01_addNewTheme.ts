// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';
import files from '@utils/files';

// Import common tests
import loginCommon from '@commonTests/BO/loginBO';

// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import themeAndLogoPage from '@pages/BO/design/themeAndLogo/themeAndLogo';
import themeImportPage from '@pages/BO/design/themeAndLogo/themeAndLogo/import';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

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

    await Promise.all([
      files.deleteFile('theme.zip'),
      files.deleteFile('../../themes/theme.zip'),
      files.deleteFile('../../admin-dev/hummingbird.zip'),
    ]);
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
  });

  it('should go to \'Add new theme\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewTheme1', baseContext);

    await themeAndLogoPage.goToNewThemePage(page);

    const pageTitle = await themeImportPage.getPageTitle(page);
    expect(pageTitle).to.contains(themeImportPage.pageTitle);
  });

  describe('Import from your computer', async () => {
    it('should download theme from the web', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'downloadTheme', baseContext);

      await files.downloadFile(urlTheme, 'theme.zip');

      const found = await files.doesFileExist('theme.zip');
      expect(found).to.eq(true);
    });

    it('should import theme from your computer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'importThemeFromComputer', baseContext);

      await themeImportPage.importFromYourComputer(page, 'theme.zip');

      const pageTitle = await themeAndLogoPage.getPageTitle(page);
      expect(pageTitle).to.contains(themeAndLogoPage.pageTitle);

      const numThemes = await themeAndLogoPage.getNumberOfThemes(page);
      expect(numThemes).to.eq(2);
    });

    it('should remove the theme Hummingbird', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'removeThemeHummingbird1', baseContext);

      const result = await themeAndLogoPage.removeTheme(page, 'hummingbird');
      expect(result).to.eq(themeAndLogoPage.successfulDeleteMessage);

      const numThemes = await themeAndLogoPage.getNumberOfThemes(page);
      expect(numThemes).to.eq(1);
    });
  });

  describe('Import from the web', async () => {
    it('should go to \'Add new theme\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewTheme2', baseContext);

      await themeAndLogoPage.goToNewThemePage(page);

      const pageTitle = await themeImportPage.getPageTitle(page);
      expect(pageTitle).to.contains(themeImportPage.pageTitle);
    });

    it('should import from the web the Hummingbird theme', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'importThemeFromWeb', baseContext);

      await themeImportPage.importFromWeb(page, urlTheme);

      const pageTitle = await themeAndLogoPage.getPageTitle(page);
      expect(pageTitle).to.contains(themeAndLogoPage.pageTitle);

      const numThemes = await themeAndLogoPage.getNumberOfThemes(page);
      expect(numThemes).to.eq(2);
    });

    it('should remove the theme Hummingbird', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'removeThemeHummingbird2', baseContext);

      const result = await themeAndLogoPage.removeTheme(page, 'hummingbird');
      expect(result).to.eq(themeAndLogoPage.successfulDeleteMessage);

      const numThemes = await themeAndLogoPage.getNumberOfThemes(page);
      expect(numThemes).to.eq(1);
    });
  });

  describe('Import from FTP', async () => {
    it('should go to \'Add new theme\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewTheme3', baseContext);

      await files.downloadFile(urlTheme, '../../themes/theme.zip');

      await themeAndLogoPage.goToNewThemePage(page);

      const pageTitle = await themeImportPage.getPageTitle(page);
      expect(pageTitle).to.contains(themeImportPage.pageTitle);
    });

    it('should import from FTP', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'importFromFTP', baseContext);

      await themeImportPage.importFromFTP(page, 'theme.zip');

      const pageTitle = await themeAndLogoPage.getPageTitle(page);
      expect(pageTitle).to.contains(themeAndLogoPage.pageTitle);

      const numThemes = await themeAndLogoPage.getNumberOfThemes(page);
      expect(numThemes).to.eq(2);
    });

    it('should remove the theme Hummingbird', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'removeThemeHummingbird3', baseContext);

      const result = await themeAndLogoPage.removeTheme(page, 'hummingbird');
      expect(result).to.eq(themeAndLogoPage.successfulDeleteMessage);

      const numThemes = await themeAndLogoPage.getNumberOfThemes(page);
      expect(numThemes).to.eq(1);
    });
  });
});
