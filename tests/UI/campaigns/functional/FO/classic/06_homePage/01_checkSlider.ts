// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import FO pages
import {homePage} from '@pages/FO/home';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_FO_classic_homePage_checkSlider';

describe('FO - Home Page : Check slider', async () => {
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

  it('should open the shop page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'openShopFO', baseContext);

    await homePage.goTo(page, global.FO.URL);

    const result = await homePage.isHomePage(page);
    await expect(result).to.be.true;
  });

  it('should click in right arrow of the slider', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'clickOnRightSlideArrow', baseContext);

    let isVisible = await homePage.isSliderVisible(page, 1);
    await expect(isVisible).to.be.true;

    await homePage.clickOnLeftOrRightArrow(page, 'right');

    isVisible = await homePage.isSliderVisible(page, 2);
    await expect(isVisible).to.be.true;
  });

  it('should click in left arrow of the slider', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'clickOnLeftSlideArrow', baseContext);

    let isVisible = await homePage.isSliderVisible(page, 2);
    await expect(isVisible).to.be.true;

    await homePage.clickOnLeftOrRightArrow(page, 'left');

    isVisible = await homePage.isSliderVisible(page, 1);
    await expect(isVisible).to.be.true;
  });

  it('should check the slider URL', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkSliderURL', baseContext);

    const currentURL = await homePage.getSliderURL(page);
    await expect(currentURL).to.contains('www.prestashop-project.org');
  });
});
