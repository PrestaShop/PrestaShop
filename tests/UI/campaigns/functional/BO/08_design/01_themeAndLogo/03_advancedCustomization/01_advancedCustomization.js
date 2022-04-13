require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');
const files = require('@utils/files');

// Import login steps
const loginCommon = require('@commonTests/BO/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const themeAndLogoPage = require('@pages/BO/design/themeAndLogo/themeAndLogo');
const advancedCustomizationPage = require('@pages/BO/design/themeAndLogo/advancedCustomization');

const baseContext = 'functional_BO_design_themeAndLogo_advancedCustomization';

let browserContext;
let page;
let filePath;
const renamedFilePath = 'child_classic.zip';

/*
Download a child theme and check the zip is well downloaded
*/
describe('BO - Design - Theme & Logo - Advanced Customization : Download a child theme', async () => {
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

  it('should login go to \'Design > Theme & Logo\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToThemeAndLogoPage', baseContext);

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
    await testContext.addContextItem(this, 'testIdentifier', 'goToAdvancedCustomizationPage', baseContext);

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
});
