import loginCommon from '@commonTests/BO/loginBO';

import themeAndLogoPage from '@pages/BO/design/themeAndLogo/themeAndLogo';

import testContext from '@utils/testContext';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  boDashboardPage,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

function enableHummingbird(baseContext: string = 'commonTests-enableHummingbird'): void {
  describe('Enable Hummingbird theme', async () => {
    let browserContext: BrowserContext;
    let page: Page;

    // before and after functions
    before(async function () {
      browserContext = await utilsPlaywright.createBrowserContext(this.browser);
      page = await utilsPlaywright.newTab(browserContext);
    });

    after(async () => {
      await utilsPlaywright.closeBrowserContext(browserContext);
    });

    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Design > Theme & Logo\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToThemeAndLogoPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.designParentLink,
        boDashboardPage.themeAndLogoParentLink,
      );
      await themeAndLogoPage.closeSfToolBar(page);

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

function disableHummingbird(baseContext: string = 'commonTests-disableHummingbird'): void {
  describe('Disable Hummingbird theme', async () => {
    let browserContext: BrowserContext;
    let page: Page;

    // before and after functions
    before(async function () {
      browserContext = await utilsPlaywright.createBrowserContext(this.browser);
      page = await utilsPlaywright.newTab(browserContext);
    });

    after(async () => {
      await utilsPlaywright.closeBrowserContext(browserContext);
    });

    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Design > Theme & Logo\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToThemeAndLogoPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.designParentLink,
        boDashboardPage.themeAndLogoParentLink,
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
  });
}

export {
  enableHummingbird,
  disableHummingbird,
};
