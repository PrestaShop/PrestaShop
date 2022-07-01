require('module-alias/register');

const {expect} = require('chai');

const helper = require('@utils/helpers');

// Import FO pages
const homePage = require('@pages/FO/home');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_FO_headerAndFooter_changeLanguage';

let browserContext;
let page;

/*
Go to FO

 */
describe('FO - Header and Footer : Change language', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should go to FO home page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

    await homePage.goToFo(page);

    const isHomePage = await homePage.isHomePage(page);
    await expect(isHomePage).to.be.true;
  });

  it('should check \'languages\' link', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkLanguagesLink', baseContext);

    await homePage.changeLanguage(page, 'fr');

    let language = await homePage.getShopLanguage(page);
    expect(language).to.equal('Français');

    await homePage.changeLanguage(page, 'en');

    language = await homePage.getShopLanguage(page);
    expect(language).to.equal('English');
  });
});
