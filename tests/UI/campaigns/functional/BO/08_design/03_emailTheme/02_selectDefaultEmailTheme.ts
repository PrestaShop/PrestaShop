// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boDashboardPage,
  boDesignEmailThemesPage,
  boLoginPage,
  type BrowserContext,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_design_emailTheme_selectDefaultEmailTheme';

describe('BO - Design - Email Theme : Select default email theme', async () => {
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

  it('should go to \'Design > Email Theme\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEmailThemePage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.designParentLink,
      boDashboardPage.emailThemeLink,
    );
    await boDesignEmailThemesPage.closeSfToolBar(page);

    const pageTitle = await boDesignEmailThemesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDesignEmailThemesPage.pageTitle);
  });

  ['classic', 'modern'].forEach((emailTheme: string) => {
    it(`should select '${emailTheme}' as default email theme`, async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        `${emailTheme}AsDefaultEmailTheme`,
        baseContext,
      );

      const textMessage = await boDesignEmailThemesPage.selectDefaultEmailTheme(page, emailTheme);
      expect(textMessage).to.contains(boDesignEmailThemesPage.emailThemeConfigurationSuccessfulMessage);
    });
  });
});
