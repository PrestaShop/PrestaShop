// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';
import files from '@utils/files';

// Import common tests
import loginCommon from '@commonTests/BO/loginBO';

// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import themeAndLogoPage from '@pages/BO/design/themeAndLogo/themeAndLogo';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_design_themeAndLogo_themeAndLogo_exportCurrentTheme';

describe('BO - Design - Theme & Logo : Export current theme', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);

    await files.deleteFile('../../themes/classic.zip');
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

  it('should click on export current theme button', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'exportCurrentTheme', baseContext);

    const successMessage = await themeAndLogoPage.exportCurrentTheme(page);
    expect(successMessage).to.contains(themeAndLogoPage.successExportMessage);
  });

  it('should check that the theme is exported successfully', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkTheme', baseContext);

    const found = await files.doesFileExist('../../themes/classic.zip');
    expect(found).to.equal(true);
  });
});
