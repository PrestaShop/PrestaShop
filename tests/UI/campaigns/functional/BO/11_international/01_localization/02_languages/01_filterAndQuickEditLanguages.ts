// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import localizationPage from '@pages/BO/international/localization';
import languagesPage from '@pages/BO/international/languages';

// Import data
import Languages from '@data/demo/languages';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_international_localization_languages_filterAndQuickEditLanguages';

/*
Filter languages by id, name, iso code, date_format and enabled columns
Disable main language 'en' and check error
Disable then enable other language
 */
describe('BO - International - Languages : Filter and quick edit languages', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfLanguages: number = 0;

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

  it('should go to \'International > Localization\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLocalizationPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.internationalParentLink,
      dashboardPage.localizationLink,
    );
    await localizationPage.closeSfToolBar(page);

    const pageTitle = await localizationPage.getPageTitle(page);
    expect(pageTitle).to.contains(localizationPage.pageTitle);
  });

  it('should go to \'Languages\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLanguagesPage', baseContext);

    await localizationPage.goToSubTabLanguages(page);

    const pageTitle = await languagesPage.getPageTitle(page);
    expect(pageTitle).to.contains(languagesPage.pageTitle);
  });

  it('should reset all filters and get number of languages in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfLanguages = await languagesPage.resetAndGetNumberOfLines(page);
    expect(numberOfLanguages).to.be.above(0);
  });

  describe('Filter languages', async () => {
    const tests = [
      {
        args:
          {
            testIdentifier: 'filterId',
            filterType: 'input',
            filterBy: 'id_lang',
            filterValue: Languages.english.id.toString(),
          },
      },
      {
        args:
          {
            testIdentifier: 'filterName',
            filterType: 'input',
            filterBy: 'name',
            filterValue: Languages.english.name,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterIsoCode',
            filterType: 'input',
            filterBy: 'iso_code',
            filterValue: Languages.english.isoCode,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterLanguageCode',
            filterType: 'input',
            filterBy: 'language_code',
            filterValue: Languages.english.languageCode,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterDateFormatLite',
            filterType: 'input',
            filterBy: 'date_format_lite',
            filterValue: Languages.english.dateFormat,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterDateFormatFull',
            filterType: 'input',
            filterBy: 'date_format_full',
            filterValue: Languages.english.fullDateFormat,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterActive',
            filterType: 'select',
            filterBy: 'active',
            filterValue: Languages.english.enabled ? '1' : '0',
          },
      },
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        await languagesPage.filterTable(
          page,
          test.args.filterType,
          test.args.filterBy,
          test.args.filterValue,
        );

        // Check number of languages
        const numberOfLanguagesAfterFilter = await languagesPage.getNumberOfElementInGrid(page);
        expect(numberOfLanguagesAfterFilter).to.be.at.most(numberOfLanguages);
        expect(numberOfLanguagesAfterFilter).to.be.at.least(1);

        for (let i = 1; i <= numberOfLanguagesAfterFilter; i++) {
          if (test.args.filterBy === 'active') {
            const languageStatus = await languagesPage.getStatus(page, i);
            expect(languageStatus).to.equal(test.args.filterValue === '1');
          } else {
            const textColumn = await languagesPage.getTextColumnFromTable(
              page,
              i,
              test.args.filterBy,
            );
            expect(textColumn).to.contains(test.args.filterValue);
          }
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfLanguagesAfterReset = await languagesPage.resetAndGetNumberOfLines(page);
        expect(numberOfLanguagesAfterReset).to.equal(numberOfLanguages);
      });
    });
  });

  describe('Disable default language', async () => {
    it('should filter by iso_code \'en\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDisableDefaultLanguage', baseContext);

      await languagesPage.filterTable(page, 'input', 'iso_code', Languages.english.isoCode);

      // Check number of languages
      const numberOfLanguagesAfterFilter = await languagesPage.getNumberOfElementInGrid(page);
      expect(numberOfLanguagesAfterFilter).to.be.at.least(1);

      const textColumn = await languagesPage.getTextColumnFromTable(page, 1, 'iso_code');
      expect(textColumn).to.contains(Languages.english.isoCode);
    });

    it('should disable \'en\' language and check error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableMainLanguage', baseContext);

      await languagesPage.setStatus(page, 1, false);

      const textError = await languagesPage.getAlertDangerBlockParagraphContent(page);
      expect(textError).to.equal(languagesPage.unSuccessfulUpdateDefaultLanguageStatusMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableDefaultLanguageReset', baseContext);

      const numberOfLanguagesAfterReset = await languagesPage.resetAndGetNumberOfLines(page);
      expect(numberOfLanguagesAfterReset).to.equal(numberOfLanguages);
    });
  });

  describe('Quick edit language', async () => {
    it('should filter by iso_code \'fr\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToQuickEdit', baseContext);

      // Filter table
      await languagesPage.filterTable(page, 'input', 'iso_code', Languages.french.isoCode);

      // Check number od languages
      const numberOfLanguagesAfterFilter = await languagesPage.getNumberOfElementInGrid(page);
      expect(numberOfLanguagesAfterFilter).to.be.at.least(1);

      const textColumn = await languagesPage.getTextColumnFromTable(page, 1, 'iso_code');
      expect(textColumn).to.contains(Languages.french.isoCode);
    });

    const tests = [
      {args: {action: 'disable', enabledValue: false}},
      {args: {action: 'enable', enabledValue: true}},
    ];

    tests.forEach((test) => {
      it(`should ${test.args.action} first language`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}Language`, baseContext);

        const isActionPerformed = await languagesPage.setStatus(page, 1, test.args.enabledValue);

        if (isActionPerformed) {
          const resultMessage = await languagesPage.getAlertSuccessBlockParagraphContent(page);
          expect(resultMessage).to.contains(languagesPage.successfulUpdateStatusMessage);
        }
        const languageStatus = await languagesPage.getStatus(page, 1);
        expect(languageStatus).to.be.equal(test.args.enabledValue);
      });
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'quickEditReset', baseContext);

      const numberOfLanguagesAfterReset = await languagesPage.resetAndGetNumberOfLines(page);
      expect(numberOfLanguagesAfterReset).to.equal(numberOfLanguages);
    });
  });
});
