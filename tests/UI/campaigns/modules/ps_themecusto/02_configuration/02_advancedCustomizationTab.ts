// Import utils
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import themeAndLogoPage from '@pages/BO/design/themeAndLogo/themeAndLogo';
import advancedCustomizationPage from '@pages/BO/design/themeAndLogo/advancedCustomization';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'modules_ps_themecusto_configuration_advancedCustomizationTab';

describe('Theme Customization module - Advanced Customization tab ', async () => {
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

  describe('Advanced Customization tab', async () => {
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

    it('should go to \'Advanced Customization\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAdvancedCustomizationPage', baseContext);

      await themeAndLogoPage.goToSubTabAdvancedCustomization(page);

      const pageTitle = await advancedCustomizationPage.getPageTitle(page);
      expect(pageTitle).to.contains(advancedCustomizationPage.pageTitle);
    });

    it('should download theme', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'downloadTheme', baseContext);

      const filePath = await advancedCustomizationPage.downloadTheme(page);

      const exist = await files.doesFileExist(filePath);
      expect(exist, 'Theme was not downloaded').to.eq(true);

      const fileType = await files.getFileType(filePath as string);
      expect(fileType).to.be.equal('zip');
    });

    it('should click on the link "How to use parents/child themes"', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickHowToUseParentsChildThemesLink', baseContext);

      page = await advancedCustomizationPage.clickHowToUseParentsChildThemesLink(page);
      expect(page.url()).to.equal(
        'https://devdocs.prestashop-project.org/1.7/themes/reference/template-inheritance/parent-child-feature/',
      );
    });

    it('should click on the button "Upload child theme"', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnUploadChildThemeButton', baseContext);

      page = await advancedCustomizationPage.closePage(browserContext, page, 0);

      const modalTitle = await advancedCustomizationPage.clickOnUploadChildThemeButton(page);
      expect(modalTitle).to.contains('Drop your child theme archive here or select file');
    });
  });
});
