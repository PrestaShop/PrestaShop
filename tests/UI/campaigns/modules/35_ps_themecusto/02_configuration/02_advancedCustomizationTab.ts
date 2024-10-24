// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  boThemeAdvancedConfigurationPage,
  boThemeAndLogoPage,
  type BrowserContext,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'modules_ps_themecusto_configuration_advancedCustomizationTab';

describe('Theme Customization module - Advanced Customization tab ', async () => {
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

  describe('Advanced Customization tab', async () => {
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
    });

    it('should go to \'Advanced Customization\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAdvancedCustomizationPage', baseContext);

      await boThemeAndLogoPage.goToSubTabAdvancedCustomization(page);

      const pageTitle = await boThemeAdvancedConfigurationPage.getPageTitle(page);
      expect(pageTitle).to.contains(boThemeAdvancedConfigurationPage.pageTitle);
    });

    it('should download theme', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'downloadTheme', baseContext);

      const filePath = await boThemeAdvancedConfigurationPage.downloadTheme(page);

      const exist = await utilsFile.doesFileExist(filePath);
      expect(exist, 'Theme was not downloaded').to.eq(true);

      const fileType = await utilsFile.getFileType(filePath as string);
      expect(fileType).to.be.equal('zip');
    });

    it('should click on the link "How to use parents/child themes"', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickHowToUseParentsChildThemesLink', baseContext);

      page = await boThemeAdvancedConfigurationPage.clickHowToUseParentsChildThemesLink(page);
      expect(page.url()).to.equal(
        'https://devdocs.prestashop-project.org/1.7/themes/reference/template-inheritance/parent-child-feature/',
      );
    });

    it('should click on the button "Upload child theme"', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnUploadChildThemeButton', baseContext);

      page = await boThemeAdvancedConfigurationPage.closePage(browserContext, page, 0);

      const modalTitle = await boThemeAdvancedConfigurationPage.clickOnUploadChildThemeButton(page);
      expect(modalTitle).to.contains('Drop your child theme archive here or select file');
    });
  });
});
