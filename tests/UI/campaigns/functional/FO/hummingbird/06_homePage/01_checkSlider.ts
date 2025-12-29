// Import utils
import testContext from '@utils/testContext';

// Import common tests
import {enableTheme, disableTheme} from '@commonTests/BO/design/hummingbird';

import {expect} from 'chai';
import {
  type BrowserContext,
  foHummingbirdHomePage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_hummingbird_homePage_checkSlider';

describe('FO - Home Page : Check slider', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Pre-condition : Install Hummingbird
  enableTheme('hummingbird', `${baseContext}_preTest`);

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Check slider', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openShopFO', baseContext);

      await foHummingbirdHomePage.goTo(page, global.FO.URL);

      const result = await foHummingbirdHomePage.isHomePage(page);
      expect(result).to.equal(true);
    });

    it('should click in right arrow of the slider', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnRightSlideArrow', baseContext);

      let isVisible = await foHummingbirdHomePage.isSliderVisible(page, 1);
      expect(isVisible).to.equal(true);

      await foHummingbirdHomePage.clickOnLeftOrRightArrow(page, 'next');

      isVisible = await foHummingbirdHomePage.isSliderVisible(page, 2);
      expect(isVisible).to.equal(true);
    });

    it('should click in left arrow of the slider', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnLeftSlideArrow', baseContext);

      let isVisible = await foHummingbirdHomePage.isSliderVisible(page, 2);
      expect(isVisible).to.equal(true);

      await foHummingbirdHomePage.clickOnLeftOrRightArrow(page, 'prev');

      isVisible = await foHummingbirdHomePage.isSliderVisible(page, 1);
      expect(isVisible).to.equal(true);
    });

    it('should check the slider URL', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSliderURL', baseContext);

      const currentURL = await foHummingbirdHomePage.getSliderURL(page);
      expect(currentURL).to.contains('www.prestashop-project.org');
    });
  });

  // Post-condition : Uninstall Hummingbird
  disableTheme('hummingbird', `${baseContext}_postTest`);
});
