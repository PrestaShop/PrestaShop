require('module-alias/register');

const {expect} = require('chai');

const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const localizationPage = require('@pages/BO/international/localization');
const languagesPage = require('@pages/BO/international/languages');

// Import data
const {Languages} = require('@data/demo/languages');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_international_localization_languages_filterLanguages';


let browserContext;
let page;
let numberOfLanguages = 0;

/*
Filter languages by id, name, iso code, date_format and enabled columns
Disable main language 'en' and check error
Disable then enable other language
 */
describe('BO - International - Languages : Filter and quick edit languages', async () => {
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
    await expect(pageTitle).to.contains(localizationPage.pageTitle);
  });

  it('should go to \'Languages\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLanguagesPage', baseContext);

    await localizationPage.goToSubTabLanguages(page);
    const pageTitle = await languagesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(languagesPage.pageTitle);
  });

  it('should reset all filters and get number of languages in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfLanguages = await languagesPage.resetAndGetNumberOfLines(page);
    await expect(numberOfLanguages).to.be.above(0);
  });

  describe('Filter languages', async () => {
    const tests = [
      {
        args:
          {
            testIdentifier: 'filterId',
            filterType: 'input',
            filterBy: 'id_lang',
            filterValue: Languages.english.id,
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
            filterValue: Languages.english.enabled,
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
        await expect(numberOfLanguagesAfterFilter).to.be.at.most(numberOfLanguages);
        await expect(numberOfLanguagesAfterFilter).to.be.at.least(1);

        for (let i = 1; i <= numberOfLanguagesAfterFilter; i++) {
          if (test.args.filterBy === 'active') {
            const languageStatus = await languagesPage.getStatus(page, i);
            await expect(languageStatus).to.equal(test.args.filterValue);
          } else {
            const textColumn = await languagesPage.getTextColumnFromTable(
              page,
              i,
              test.args.filterBy,
            );

            await expect(textColumn).to.contains(test.args.filterValue);
          }
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfLanguagesAfterReset = await languagesPage.resetAndGetNumberOfLines(page);
        await expect(numberOfLanguagesAfterReset).to.equal(numberOfLanguages);
      });
    });
  });

  describe('Disable default language', async () => {
    it('should filter by iso_code \'en\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDisableDefaultLanguage', baseContext);

      await languagesPage.filterTable(page, 'input', 'iso_code', Languages.english.isoCode);

      // Check number of languages
      const numberOfLanguagesAfterFilter = await languagesPage.getNumberOfElementInGrid(page);
      await expect(numberOfLanguagesAfterFilter).to.be.at.least(1);

      const textColumn = await languagesPage.getTextColumnFromTable(page, 1, 'iso_code');
      await expect(textColumn).to.contains(Languages.english.isoCode);
    });

    it('should disable \'en\' language and check error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableMainLanguage', baseContext);

      await languagesPage.setStatus(page, 1, false);
      const textError = await languagesPage.getAlertDangerBlockParagraphContent(page);
      await expect(textError).to.equal(languagesPage.unSuccessfulUpdateDefaultLanguageStatusMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableDefaultLanguageReset', baseContext);

      const numberOfLanguagesAfterReset = await languagesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfLanguagesAfterReset).to.equal(numberOfLanguages);
    });
  });

  describe('Quick edit language', async () => {
    it('should filter by iso_code \'fr\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToQuickEdit', baseContext);

      // Filter table
      await languagesPage.filterTable(page, 'input', 'iso_code', Languages.french.isoCode);

      // Check number od languages
      const numberOfLanguagesAfterFilter = await languagesPage.getNumberOfElementInGrid(page);
      await expect(numberOfLanguagesAfterFilter).to.be.at.least(1);

      const textColumn = await languagesPage.getTextColumnFromTable(page, 1, 'iso_code');
      await expect(textColumn).to.contains(Languages.french.isoCode);
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
          await expect(resultMessage).to.contains(languagesPage.successfulUpdateStatusMessage);
        }
        const languageStatus = await languagesPage.getStatus(page, 1);
        await expect(languageStatus).to.be.equal(test.args.enabledValue);
      });
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'quickEditReset', baseContext);

      const numberOfLanguagesAfterReset = await languagesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfLanguagesAfterReset).to.equal(numberOfLanguages);
    });
  });
});
