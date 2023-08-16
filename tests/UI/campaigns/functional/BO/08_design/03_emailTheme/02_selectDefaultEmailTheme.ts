// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import emailThemesPage from '@pages/BO/design/emailThemes';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_design_emailTheme_selectDefaultEmailTheme';

describe('BO - Design - Email Theme : Select default email theme', async () => {
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

  it('should go to \'Design > Email Theme\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEmailThemePage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.designParentLink,
      dashboardPage.emailThemeLink,
    );
    await emailThemesPage.closeSfToolBar(page);

    const pageTitle = await emailThemesPage.getPageTitle(page);
    expect(pageTitle).to.contains(emailThemesPage.pageTitle);
  });

  ['classic', 'modern'].forEach((emailTheme: string) => {
    it(`should select '${emailTheme}' as default email theme`, async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        `${emailTheme}AsDefaultEmailTheme`,
        baseContext,
      );

      const textMessage = await emailThemesPage.selectDefaultEmailTheme(page, emailTheme);
      expect(textMessage).to.contains(emailThemesPage.emailThemeConfigurationSuccessfulMessage);
    });
  });
});
