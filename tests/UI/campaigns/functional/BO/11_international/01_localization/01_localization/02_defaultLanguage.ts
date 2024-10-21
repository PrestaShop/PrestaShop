// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLocalizationPage,
  boLoginPage,
  type BrowserContext,
  dataLanguages,
  foClassicHomePage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_international_localization_localization_defaultLanguage';

describe('BO - International - Localization : Update default language', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  [
    {args: {language: dataLanguages.french.name, defaultBrowserLanguage: false, languageToCheck: 'Français'}},
    {args: {language: dataLanguages.english.name, defaultBrowserLanguage: false, languageToCheck: 'English'}},
    // To back to the default values
    {args: {language: dataLanguages.english.name, defaultBrowserLanguage: true}},
  ].forEach((test, index: number) => {
    describe(`Set default language to '${test.args.language}' and default language from browser to`
      + ` '${test.args.defaultBrowserLanguage}'`, async () => {
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

      it('should go to \'International > localization\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToLocalizationPage_${index}`, baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.internationalParentLink,
          boDashboardPage.localizationLink,
        );
        await boLocalizationPage.closeSfToolBar(page);

        const pageTitle = await boLocalizationPage.getPageTitle(page);
        expect(pageTitle).to.contains(boLocalizationPage.pageTitle);
      });

      it('should set \'Default language\' and \'Set language from browser\'', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `setDEfaultLanguage_${index}`, baseContext);

        const textResult = await boLocalizationPage.setDefaultLanguage(
          page,
          test.args.language,
          test.args.defaultBrowserLanguage,
        );
        expect(textResult).to.equal('Update successful');
      });
    });

    // Do not check the FO language when index = 2
    if (index !== 2) {
      describe(`Check if the FO language is '${test.args.languageToCheck}'`, async () => {
        before(async function () {
          browserContext = await utilsPlaywright.createBrowserContext(this.browser);
          page = await utilsPlaywright.newTab(browserContext);
        });

        after(async () => {
          await utilsPlaywright.closeBrowserContext(browserContext);
        });

        it('should open the shop page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `openShop_${index}`, baseContext);

          await foClassicHomePage.goTo(page, global.FO.URL);

          const isHomePage = await foClassicHomePage.isHomePage(page);
          expect(isHomePage).to.eq(true);
        });

        it('should go to FO and check the language', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkLanguageInFO_${index}`, baseContext);

          const defaultLanguage = await foClassicHomePage.getDefaultShopLanguage(page);
          expect(defaultLanguage).to.equal(test.args.languageToCheck);
        });
      });
    }
  });
});
