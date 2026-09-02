import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boImageSettingsPage,
  boLoginPage,
  type BrowserContext,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_design_imageSettings_imageGenerationOptions';

/*
  Enable Feature flag of image
 */
describe('BO - Design - Image Settings : Image Generation options', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  describe('Image Generation options', async () => {
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

    it('should go to \'Design > Image Settings\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToImageSettingsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.designParentLink,
        boDashboardPage.imageSettingsLink,
      );
      await boImageSettingsPage.closeSfToolBar(page);

      const pageTitle = await boImageSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boImageSettingsPage.pageTitle);
    });

    it('should check image generation options', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkImageGenerationOptions', baseContext);

      // JPEG/PNG should be checked
      const jpegChecked = await boImageSettingsPage.isImageFormatToGenerateChecked(page, 'jpg');
      expect(jpegChecked).to.eq(true);

      // JPEG/PNG should be greyed
      // You can't uncheck the JPEG/PNG format
      const jpegDisabled = await boImageSettingsPage.isImageFormatToGenerateDisabled(page, 'jpg');
      expect(jpegDisabled).to.eq(true);

      // >= PHP 8.1 : The checkbox of AVIF should be enabled
      // <  PHP 8.1 : The checkbox of AVIF should be disabled
      const avifDisabled = await boImageSettingsPage.isImageFormatToGenerateDisabled(page, 'avif');
      expect(avifDisabled).to.eq(true);
    });
  });
});
