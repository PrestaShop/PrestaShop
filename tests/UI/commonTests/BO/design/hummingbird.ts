import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  boThemeAndLogoPage,
  type BrowserContext,
  type Page,
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
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Design > Theme & Logo\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToThemeAndLogoPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.designParentLink,
        boDashboardPage.themeAndLogoParentLink,
      );
      await boThemeAndLogoPage.closeSfToolBar(page);

      const pageTitle = await boThemeAndLogoPage.getPageTitle(page);
      expect(pageTitle).to.contains(boThemeAndLogoPage.pageTitle);

      const numThemes = await boThemeAndLogoPage.getNumberOfThemes(page);
      expect(numThemes).to.eq(2);
    });

    it('should enable the theme Hummingbird', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableThemeHummingbird', baseContext);

      const result = await boThemeAndLogoPage.enableTheme(page, 'hummingbird');
      expect(result).to.eq(boThemeAndLogoPage.successfulUpdateMessage);
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
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Design > Theme & Logo\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToThemeAndLogoPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.designParentLink,
        boDashboardPage.themeAndLogoParentLink,
      );
      await boThemeAndLogoPage.closeSfToolBar(page);

      const pageTitle = await boThemeAndLogoPage.getPageTitle(page);
      expect(pageTitle).to.contains(boThemeAndLogoPage.pageTitle);

      const numThemes = await boThemeAndLogoPage.getNumberOfThemes(page);
      expect(numThemes).to.eq(2);
    });

    it('should enable the theme Classic', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableThemeClassic', baseContext);

      const result = await boThemeAndLogoPage.enableTheme(page, 'classic');
      expect(result).to.eq(boThemeAndLogoPage.successfulUpdateMessage);
    });
  });
}

export {
  enableHummingbird,
  disableHummingbird,
};
