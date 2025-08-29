// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLanguagesPage,
  boLocalizationPage,
  boLoginPage,
  type BrowserContext,
  dataLanguages,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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

  it('should go to \'International > Localization\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLocalizationPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.internationalParentLink,
      boDashboardPage.localizationLink,
    );
    await boLocalizationPage.closeSfToolBar(page);

    const pageTitle = await boLocalizationPage.getPageTitle(page);
    expect(pageTitle).to.contains(boLocalizationPage.pageTitle);
  });

  it('should go to \'Languages\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLanguagesPage', baseContext);

    await boLocalizationPage.goToSubTabLanguages(page);

    const pageTitle = await boLanguagesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boLanguagesPage.pageTitle);
  });

  it('should reset all filters and get number of languages in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfLanguages = await boLanguagesPage.resetAndGetNumberOfLines(page);
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
            filterValue: dataLanguages.english.id.toString(),
          },
      },
      {
        args:
          {
            testIdentifier: 'filterName',
            filterType: 'input',
            filterBy: 'name',
            filterValue: dataLanguages.english.name,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterIsoCode',
            filterType: 'input',
            filterBy: 'iso_code',
            filterValue: dataLanguages.english.isoCode,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterLanguageCode',
            filterType: 'input',
            filterBy: 'language_code',
            filterValue: dataLanguages.english.languageCode,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterLocale',
            filterType: 'input',
            filterBy: 'locale',
            filterValue: dataLanguages.english.locale,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterDateFormatLite',
            filterType: 'input',
            filterBy: 'date_format_lite',
            filterValue: dataLanguages.english.dateFormat,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterDateFormatFull',
            filterType: 'input',
            filterBy: 'date_format_full',
            filterValue: dataLanguages.english.fullDateFormat,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterActive',
            filterType: 'select',
            filterBy: 'active',
            filterValue: dataLanguages.english.enabled ? '1' : '0',
          },
      },
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        await boLanguagesPage.filterTable(
          page,
          test.args.filterType,
          test.args.filterBy,
          test.args.filterValue,
        );

        // Check number of languages
        const numberOfLanguagesAfterFilter = await boLanguagesPage.getNumberOfElementInGrid(page);
        expect(numberOfLanguagesAfterFilter).to.be.at.most(numberOfLanguages);
        expect(numberOfLanguagesAfterFilter).to.be.at.least(1);

        for (let i = 1; i <= numberOfLanguagesAfterFilter; i++) {
          if (test.args.filterBy === 'active') {
            const languageStatus = await boLanguagesPage.getStatus(page, i);
            expect(languageStatus).to.equal(test.args.filterValue === '1');
          } else {
            const textColumn = await boLanguagesPage.getTextColumnFromTable(
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

        const numberOfLanguagesAfterReset = await boLanguagesPage.resetAndGetNumberOfLines(page);
        expect(numberOfLanguagesAfterReset).to.equal(numberOfLanguages);
      });
    });
  });

  describe('Disable default language', async () => {
    it('should filter by iso_code \'en\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDisableDefaultLanguage', baseContext);

      await boLanguagesPage.filterTable(page, 'input', 'iso_code', dataLanguages.english.isoCode);

      // Check number of languages
      const numberOfLanguagesAfterFilter = await boLanguagesPage.getNumberOfElementInGrid(page);
      expect(numberOfLanguagesAfterFilter).to.be.at.least(1);

      const textColumn = await boLanguagesPage.getTextColumnFromTable(page, 1, 'iso_code');
      expect(textColumn).to.contains(dataLanguages.english.isoCode);
    });

    it('should disable \'en\' language and check error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableMainLanguage', baseContext);

      await boLanguagesPage.setStatus(page, 1, false);

      const textError = await boLanguagesPage.getAlertDangerBlockParagraphContent(page);
      expect(textError).to.equal(boLanguagesPage.unSuccessfulUpdateDefaultLanguageStatusMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableDefaultLanguageReset', baseContext);

      const numberOfLanguagesAfterReset = await boLanguagesPage.resetAndGetNumberOfLines(page);
      expect(numberOfLanguagesAfterReset).to.equal(numberOfLanguages);
    });
  });

  describe('Quick edit language', async () => {
    it('should filter by iso_code \'fr\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToQuickEdit', baseContext);

      // Filter table
      await boLanguagesPage.filterTable(page, 'input', 'iso_code', dataLanguages.french.isoCode);

      // Check number od languages
      const numberOfLanguagesAfterFilter = await boLanguagesPage.getNumberOfElementInGrid(page);
      expect(numberOfLanguagesAfterFilter).to.be.at.least(1);

      const textColumn = await boLanguagesPage.getTextColumnFromTable(page, 1, 'iso_code');
      expect(textColumn).to.contains(dataLanguages.french.isoCode);
    });

    const tests = [
      {args: {action: 'disable', enabledValue: false}},
      {args: {action: 'enable', enabledValue: true}},
    ];

    tests.forEach((test) => {
      it(`should ${test.args.action} first language`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}Language`, baseContext);

        const isActionPerformed = await boLanguagesPage.setStatus(page, 1, test.args.enabledValue);

        if (isActionPerformed) {
          const resultMessage = await boLanguagesPage.getAlertSuccessBlockParagraphContent(page);
          expect(resultMessage).to.contains(boLanguagesPage.successfulUpdateStatusMessage);
        }
        const languageStatus = await boLanguagesPage.getStatus(page, 1);
        expect(languageStatus).to.be.equal(test.args.enabledValue);
      });
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'quickEditReset', baseContext);

      const numberOfLanguagesAfterReset = await boLanguagesPage.resetAndGetNumberOfLines(page);
      expect(numberOfLanguagesAfterReset).to.equal(numberOfLanguages);
    });
  });
});
