// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import menuTabPage from '@pages/BO/advancedParameters/menuTab';
import dashboardPage from '@pages/BO/dashboard';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'regression_menu_deniedAccessToMenuTab';

describe('Regression : Access to Menu tab is denied with neither left menu and Url', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const pageUrl: string = `${global.BO.URL}index.php?controller=AdminTabs`;

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

  it('should go check that menu tab on left menu is not visible', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'menuTabNotVisible', baseContext);

    const isMenuTabVisible = await dashboardPage.isSubmenuVisible(
      page,
      dashboardPage.advancedParametersLink,
      dashboardPage.menuTabLink,
    );
    await expect(isMenuTabVisible, 'The Menu tab is still visible').to.be.false;
  });

  it('should accessing the page by Url be denied', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkUrlAccessibility', baseContext);

    await dashboardPage.navigateToPageWithInvalidToken(page, pageUrl);

    // Check Title
    const pageTitle = await menuTabPage.getPageTitle(page);
    await expect(pageTitle).to.contains(menuTabPage.pageTitle);

    const alertText = await menuTabPage.getAlertDangerBlockParagraphContent(page);
    await expect(alertText).to.contain(menuTabPage.accessDeniedMessage);
  });
});
