// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import common tests
import {installHummingbird, uninstallHummingbird} from '@commonTests/BO/design/hummingbird';

// Import FO pages
import homePage from '@pages/FO/hummingbird/home';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_FO_hummingbird_homePage_checkSlider';

describe('FO - Home Page : Check slider', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Pre-condition : Install Hummingbird
  installHummingbird(`${baseContext}_preTest`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Check slider', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openShopFO', baseContext);

      await homePage.goTo(page, global.FO.URL);

      const result = await homePage.isHomePage(page);
      expect(result).to.equal(true);
    });

    it('should click in right arrow of the slider', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnRightSlideArrow', baseContext);

      let isVisible = await homePage.isSliderVisible(page, 1);
      expect(isVisible).to.equal(true);

      await homePage.clickOnLeftOrRightArrow(page, 'next');

      isVisible = await homePage.isSliderVisible(page, 2);
      expect(isVisible).to.equal(true);
    });

    it('should click in left arrow of the slider', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnLeftSlideArrow', baseContext);

      let isVisible = await homePage.isSliderVisible(page, 2);
      expect(isVisible).to.equal(true);

      await homePage.clickOnLeftOrRightArrow(page, 'prev');

      isVisible = await homePage.isSliderVisible(page, 1);
      expect(isVisible).to.equal(true);
    });

    it('should check the slider URL', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSliderURL', baseContext);

      const currentURL = await homePage.getSliderURL(page);
      expect(currentURL).to.contains('www.prestashop-project.org');
    });
  });

  // Post-condition : Uninstall Hummingbird
  uninstallHummingbird(`${baseContext}_postTest`);
});
