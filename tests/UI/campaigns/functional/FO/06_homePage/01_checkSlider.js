// Import utils
import helper from '@utils/helpers';

// Import test context
import testContext from '@utils/testContext';

require('module-alias/register');

const {expect} = require('chai');

// Importing pages
// FO pages
const homePage = require('@pages/FO/home');

const baseContext = 'functional_FO_homePage_checkSlider';

let browserContext;
let page;

describe('FO - Home Page : Check slider', async () => {
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

  it('should click on the slider and check the URL', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'clickOnSlider', baseContext);

    const currentURL = await homePage.getSliderURL(page);
    await expect(currentURL)
      .to.contains('http://www.prestashop.com/')
      .and.to.contains('homeslider&utm_campaign=back-office-EN&utm_content=download');
  });
});
