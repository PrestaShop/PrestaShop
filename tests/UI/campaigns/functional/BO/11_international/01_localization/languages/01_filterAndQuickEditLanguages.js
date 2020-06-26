require('module-alias/register');

const {expect} = require('chai');

const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const LocalizationPage = require('@pages/BO/international/localization');
const LanguagesPage = require('@pages/BO/international/languages');

// Import data
const {Languages} = require('@data/demo/languages');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_international_localization_languages_filterLanguages';


let browserContext;
let page;
let numberOfLanguages = 0;

// Init objects needed
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    localizationPage: new LocalizationPage(page),
    languagesPage: new LanguagesPage(page),
  };
};

/*
Filter languages by id, name, iso code, date_format and enabled columns
Disable main language 'en' and check error
Disable then enable other language
 */
describe('Filter and quick edit languages', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    this.pageObjects = await init();
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // Login into BO and go to localization page
  loginCommon.loginBO();

  it('should go to localization page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLocalizationPage', baseContext);

    await this.pageObjects.dashboardPage.goToSubMenu(
      this.pageObjects.dashboardPage.internationalParentLink,
      this.pageObjects.dashboardPage.localizationLink,
    );

    await this.pageObjects.localizationPage.closeSfToolBar();

    const pageTitle = await this.pageObjects.localizationPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.localizationPage.pageTitle);
  });

  it('should go to languages page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLanguagesPage', baseContext);

    await this.pageObjects.localizationPage.goToSubTabLanguages();
    const pageTitle = await this.pageObjects.languagesPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.languagesPage.pageTitle);
  });

  it('should reset all filters and get number of languages in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfLanguages = await this.pageObjects.languagesPage.resetAndGetNumberOfLines();
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
        expected: 'check',
      },
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        await this.pageObjects.languagesPage.filterTable(
          test.args.filterType,
          test.args.filterBy,
          test.args.filterValue,
        );

        // Check number of languages
        const numberOfLanguagesAfterFilter = await this.pageObjects.languagesPage.getNumberOfElementInGrid();
        await expect(numberOfLanguagesAfterFilter).to.be.at.most(numberOfLanguages);
        await expect(numberOfLanguagesAfterFilter).to.be.at.least(1);

        for (let i = 1; i <= numberOfLanguagesAfterFilter; i++) {
          const textColumn = await this.pageObjects.languagesPage.getTextColumnFromTable(
            i,
            test.args.filterBy,
          );

          if (test.expected !== undefined) {
            await expect(textColumn).to.contains(test.expected);
          } else {
            await expect(textColumn).to.contains(test.args.filterValue);
          }
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);
        const numberOfLanguagesAfterReset = await this.pageObjects.languagesPage.resetAndGetNumberOfLines();
        await expect(numberOfLanguagesAfterReset).to.equal(numberOfLanguages);
      });
    });
  });

  describe('Disable default language', async () => {
    it('should filter by iso_code \'en\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDisableDefaultLanguage', baseContext);

      await this.pageObjects.languagesPage.filterTable('input', 'iso_code', Languages.english.isoCode);

      // Check number of languages
      const numberOfLanguagesAfterFilter = await this.pageObjects.languagesPage.getNumberOfElementInGrid();
      await expect(numberOfLanguagesAfterFilter).to.be.at.least(1);

      const textColumn = await this.pageObjects.languagesPage.getTextColumnFromTable(1, 'iso_code');
      await expect(textColumn).to.contains(Languages.english.isoCode);
    });

    it('should disable \'en\' language and check error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableMainLanguage', baseContext);

      await this.pageObjects.languagesPage.quickEditLanguage(1, false);
      const textError = await this.pageObjects.languagesPage.getAlertDangerMessage();
      await expect(textError).to.equal(this.pageObjects.languagesPage.unSuccessfulUpdateDefaultLanguageStatusMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableDefaultLanguageReset', baseContext);

      const numberOfLanguagesAfterReset = await this.pageObjects.languagesPage.resetAndGetNumberOfLines();
      await expect(numberOfLanguagesAfterReset).to.equal(numberOfLanguages);
    });
  });

  describe('Quick edit language', async () => {
    it('should filter by iso_code \'fr\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToQuickEdit', baseContext);

      // Filter table
      await this.pageObjects.languagesPage.filterTable('input', 'iso_code', Languages.french.isoCode);

      // Check number od languages
      const numberOfLanguagesAfterFilter = await this.pageObjects.languagesPage.getNumberOfElementInGrid();
      await expect(numberOfLanguagesAfterFilter).to.be.at.least(1);

      const textColumn = await this.pageObjects.languagesPage.getTextColumnFromTable(1, 'iso_code');
      await expect(textColumn).to.contains(Languages.french.isoCode);
    });

    const tests = [
      {args: {action: 'disable', enabledValue: false}},
      {args: {action: 'enable', enabledValue: true}},
    ];

    tests.forEach((test) => {
      it(`should ${test.args.action} first language`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}Language`, baseContext);

        const isActionPerformed = await this.pageObjects.languagesPage.quickEditLanguage(1, test.args.enabledValue);

        if (isActionPerformed) {
          const resultMessage = await this.pageObjects.languagesPage.getTextContent(
            this.pageObjects.languagesPage.alertSuccessBlockParagraph,
          );

          await expect(resultMessage).to.contains(this.pageObjects.languagesPage.successfulUpdateStatusMessage);
        }
        const languageStatus = await this.pageObjects.languagesPage.isEnabled(1);
        await expect(languageStatus).to.be.equal(test.args.enabledValue);
      });
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'quickEditReset', baseContext);

      const numberOfLanguagesAfterReset = await this.pageObjects.languagesPage.resetAndGetNumberOfLines();
      await expect(numberOfLanguagesAfterReset).to.equal(numberOfLanguages);
    });
  });
});
