import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  boThemeAndLogoPage,
  type BrowserContext,
  type Page,
  utilsCore,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

function enableTheme(theme: string, baseContext: string = 'commonTests-enableTheme'): void {
  describe(`Enable ${utilsCore.capitalize(theme)} theme`, async () => {
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

    it(`should enable the theme ${utilsCore.capitalize(theme)}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableTheme', baseContext);

      const activeTheme = await boThemeAndLogoPage.getActiveTheme(page);

      if (activeTheme !== theme) {
        const result = await boThemeAndLogoPage.enableTheme(page, theme);
        expect(result).to.eq(boThemeAndLogoPage.successfulUpdateMessage);
      }
    });
  });
}

function disableTheme(theme: string, baseContext: string = 'commonTests-disableTheme'): void {
  describe(`Disable ${utilsCore.capitalize(theme)} theme`, async () => {
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

    it(`should toggle the theme ${utilsCore.capitalize(theme)}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableTheme', baseContext);

      const activeTheme = await boThemeAndLogoPage.getActiveTheme(page);

      if (activeTheme === theme) {
        const result = await boThemeAndLogoPage.enableTheme(page, theme === 'hummingbird' ? 'classic' : 'hummingbird');
        expect(result).to.eq(boThemeAndLogoPage.successfulUpdateMessage);
      }
    });
  });
}

export {
  enableTheme,
  disableTheme,
};
