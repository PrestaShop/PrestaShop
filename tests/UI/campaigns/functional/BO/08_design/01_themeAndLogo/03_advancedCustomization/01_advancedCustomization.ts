// Import utils
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import login steps
import loginCommon from '@commonTests/BO/loginBO';

// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import advancedCustomizationPage from '@pages/BO/design/themeAndLogo/advancedCustomization';
import themeAndLogoPage from '@pages/BO/design/themeAndLogo/themeAndLogo';
import {moduleManager as moduleManagerPage} from '@pages/BO/modules/moduleManager';
// Import FO pages
import {homePage} from '@pages/FO/home';

// Import data
import Modules from '@data/demo/modules';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_design_themeAndLogo_advancedCustomization_advancedCustomization';

/*
Pre-condition:
- Check if the module Theme Customization is installed and enabled
Scenario:
- Download a child theme and check the theme is well uploaded
- Use the child theme
- Use the classic theme and remove the child theme
- Check the How to use parents/child themes link
 */

describe('BO - Design - Theme & Logo - Advanced Customization', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  // Variable used to create temporary theme file
  let filePath: string|null;

  // Variable used to create child_classic.zip file
  const renamedFilePath: string = 'child_classic.zip';
  // Variable used for the themes folder
  const themesPath: string = 'themes/';

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

  // Pre-condition: Check if the module Theme Customization is installed and enabled
  describe('BO - Modules - Check that the \'Theme Customization\' module is installed and enabled ', async () => {
    it('should go to \'Modules > Module Manager\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToModuleManagerPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.modulesParentLink,
        dashboardPage.moduleManagerLink,
      );
    });

    it(`should search for module ${Modules.themeCustomization.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchForModule', baseContext);

      const isModuleVisible = await moduleManagerPage.searchModule(page, Modules.themeCustomization);
      await expect(isModuleVisible, `The module ${Modules.themeCustomization.name} is not installed`).to.be.true;
    });

    it(`should check the status of the module ${Modules.themeCustomization.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStatusModule', baseContext);

      const isModuleEnabled = await moduleManagerPage.isModuleStatus(page, Modules.themeCustomization.name, 'enable');
      await expect(isModuleEnabled, `The module ${Modules.themeCustomization.name} is disabled`).to.be.true;
    });
  });

  // 1 - Download a child theme and check the theme is well uploaded
  describe('BO - Design - Theme & Logo - Advanced Customization : Download a child theme and upload it', async () => {
    it('should go to \'Design > Theme & Logo\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToThemeAndLogoPage_1', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.designParentLink,
        dashboardPage.themeAndLogoParentLink,
      );
      await themeAndLogoPage.closeSfToolBar(page);

      const pageTitle = await themeAndLogoPage.getPageTitle(page);
      await expect(pageTitle).to.contains(themeAndLogoPage.pageTitle);
    });

    it('should go to \'Advanced Customization\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAdvancedCustomizationPage_1', baseContext);

      await themeAndLogoPage.goToSubTabAdvancedCustomization(page);

      const pageTitle = await advancedCustomizationPage.getPageTitle(page);
      await expect(pageTitle).to.contains(advancedCustomizationPage.pageTitle);
    });

    it('should download theme', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'downloadTheme', baseContext);

      filePath = await advancedCustomizationPage.downloadTheme(page);

      const exist = await files.doesFileExist(filePath);
      await expect(exist, 'Theme was not downloaded').to.be.true;
    });

    it('should click on upload child theme button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnUploadChildTheme', baseContext);

      const modalTitle = await advancedCustomizationPage.clickOnUploadChildThemeButton(page);
      await expect(modalTitle, 'Modal \'Upload child theme\' is not displayed')
        .to.contains('Drop your child theme archive here or select file');
    });

    it('should upload child theme', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'uploadChildTheme', baseContext);

      await files.renameFile(filePath, renamedFilePath);

      const uploadTheme = await advancedCustomizationPage.uploadTheme(page, renamedFilePath);
      await expect(uploadTheme, 'Child theme is not uploaded')
        .to.contains('The child theme has been added successfully.');
    });

    it('should close the modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeModal', baseContext);

      const isModalClosed = await advancedCustomizationPage.closeModal(page);
      await expect(isModalClosed, 'Modal not closed').to.be.true;
    });

    // remove the child_classic.zip from themes folder
    it('should remove the zip file of the child theme from the themes folder', async function () {
      await testContext
        .addContextItem(this, 'testIdentifier', 'removeZipFileChildThemeFromThemesFolder', baseContext);

      const generatedFilePath = await files.getFilePathAutomaticallyGenerated(themesPath, renamedFilePath);
      await files.deleteFile(generatedFilePath);

      const doesFileExist = await files.doesFileExist(generatedFilePath);
      await expect(doesFileExist).to.be.false;
    });

    it('should remove the zip file of the child theme from the temporary path', async function () {
      await testContext
        .addContextItem(this, 'testIdentifier', 'removeChildThemeFromTemporaryPath', baseContext);

      await files.deleteFile(renamedFilePath);

      const doesFileExist = await files.doesFileExist(renamedFilePath);
      await expect(doesFileExist).to.be.false;
    });
  });

  // 2 - Use the child theme
  describe('Use the child theme', async () => {
    it('should go to \'Design > Theme & Logo\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToThemeAndLogoPage_2', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.designParentLink,
        dashboardPage.themeAndLogoParentLink,
      );
      await themeAndLogoPage.closeSfToolBar(page);

      const pageTitle = await themeAndLogoPage.getPageTitle(page);
      await expect(pageTitle).to.contains(themeAndLogoPage.pageTitle);
    });

    it('should use the child theme', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'useChildTheme', baseContext);

      const successResult = await themeAndLogoPage.useTheme(page);
      await expect(successResult).to.be.equal(themeAndLogoPage.successfulUpdateMessage);
    });

    it('should click on view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnViewMyShop', baseContext);

      page = await themeAndLogoPage.viewMyShop(page);
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      await expect(isHomePage, 'Fail to open FO home page').to.be.true;
    });

    it('should close the current page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeCurrentPage', baseContext);

      page = await homePage.closePage(browserContext, page, 0);

      const pageTitle = await themeAndLogoPage.getPageTitle(page);
      await expect(pageTitle).to.contains(themeAndLogoPage.pageTitle);
    });
  });

  // 3 - Use the classic theme and remove the child theme
  describe('Remove the child theme', async () => {
    it('should use the classic theme', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'useClassicTheme', baseContext);

      const successResult = await themeAndLogoPage.useTheme(page);
      await expect(successResult).to.be.equal(themeAndLogoPage.successfulUpdateMessage);
    });

    it('should delete the child theme', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteChildTheme', baseContext);

      const successResult = await themeAndLogoPage.deleteTheme(page);
      await expect(successResult).to.be.equal(themeAndLogoPage.successfulDeleteMessage);
    });
  });

  // 4 - Click on the link How to use parents/child themes
  describe('How to use parents/child themes', async () => {
    it('should go to \'Advanced Customization\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAdvancedCustomizationPage_2', baseContext);

      await themeAndLogoPage.goToSubTabAdvancedCustomization(page);

      const pageTitle = await advancedCustomizationPage.getPageTitle(page);
      await expect(pageTitle).to.contains(advancedCustomizationPage.pageTitle);
    });

    it('should get the How to use parents/child themes link', async function () {
      await testContext
        .addContextItem(this, 'testIdentifier', 'getHowToUseParentsChildThemesLink', baseContext);

      const linkParentChildPage = await advancedCustomizationPage.getHowToUseParentsChildThemesLink(page);
      await expect(linkParentChildPage)
        .to
        .contains('https://devdocs.prestashop.com/1.7/themes/reference/template_inheritance/parent_child_feature/');
    });
  });
});
