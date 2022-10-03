require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');

// Importing pages
// FO pages
const homePage = require('@pages/FO/home');

// Import test context
const testContext = require('@utils/testContext');

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

    const currentURL = await homePage.clickOnSlider(page, 2);
    await expect(currentURL)
      .to.contains('https://www.prestashop.com/en')
      .and.to.contains('homeslider&utm_campaign=back-office-EN&utm_content=download');
  });
});
