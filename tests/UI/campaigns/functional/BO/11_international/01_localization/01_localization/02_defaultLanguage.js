require('module-alias/register');

const {expect} = require('chai');

const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const localizationPage = require('@pages/BO/international/localization');
const homePage = require('@pages/FO/home');

// Import Data
const {Languages} = require('@data/demo/languages');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_international_localization_defaultLanguage';

let browserContext;
let page;

describe('BO - International - Localization : Update default language', async () => {
  [
    {args: {language: Languages.french.name, defaultBrowserLanguage: false, languageToCheck: 'Français'}},
    {args: {language: Languages.english.name, defaultBrowserLanguage: false, languageToCheck: 'English'}},
    // To back to the default values
    {args: {language: Languages.english.name, defaultBrowserLanguage: true}},
  ].forEach((test, index) => {
    describe(`Set default language to '${test.args.language}' and default language from browser to`
      + ` '${test.args.defaultBrowserLanguage}'`, async () => {
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

      it('should go to \'International > localization\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToLocalizationPage_${index}`, baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.internationalParentLink,
          dashboardPage.localizationLink,
        );

        await localizationPage.closeSfToolBar(page);

        const pageTitle = await localizationPage.getPageTitle(page);
        await expect(pageTitle).to.contains(localizationPage.pageTitle);
      });

      it('should set \'Default language\' and \'Set language from browser\'', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `setDEfaultLanguage_${index}`, baseContext);

        const textResult = await localizationPage.setDefaultLanguage(
          page,
          test.args.language,
          test.args.defaultBrowserLanguage,
        );

        await expect(textResult).to.equal('Update successful');
      });
    });

    // Do not check the FO language when index = 2
    if (index !== 2) {
      describe(`Check if the FO language is '${test.args.languageToCheck}'`, async () => {
        before(async function () {
          browserContext = await helper.createBrowserContext(this.browser);
          page = await helper.newTab(browserContext);
        });

        after(async () => {
          await helper.closeBrowserContext(browserContext);
        });

        it('should open the shop page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `openShop_${index}`, baseContext);

          await homePage.goTo(page, global.FO.URL);
          const isHomePage = await homePage.isHomePage(page);
          await expect(isHomePage).to.be.true;
        });

        it('should go to FO and check the language', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkLanguageInFO_${index}`, baseContext);

          const defaultLanguage = await homePage.getShopLanguage(page);
          expect(defaultLanguage).to.equal(test.args.languageToCheck);
        });
      });
    }
  });
});
