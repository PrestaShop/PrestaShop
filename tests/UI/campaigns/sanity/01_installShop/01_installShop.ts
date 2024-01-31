// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import pages
import installPage from '@pages/install';
import {homePage} from '@pages/FO/classic/home';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'sanity_installShop_installShop';

describe('Install Prestashop', async () => {
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

  // Steps
  it('should open the Install page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToInstallPage', baseContext);

    await installPage.goTo(page, global.INSTALL.URL);

    const stepTitle = await installPage.getStepTitle(page, 'Choose your language');
    const installationTitles = [installPage.firstStepFrTitle, installPage.firstStepEnTitle];

    expect(installationTitles.some((x) => stepTitle.includes(x))).to.eq(true);
  });

  it('should change language to English and check title', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'ChangeLanguageToEnglish', baseContext);

    await installPage.setInstallLanguage(page);

    const stepTitle = await installPage.getStepTitle(page, 'Choose your language');
    expect(stepTitle).to.contain(installPage.firstStepEnTitle);
  });

  it('should click on next and go to step \'License Agreements\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLicenseAgreements', baseContext);

    await installPage.nextStep(page);

    const stepTitle = await installPage.getStepTitle(page, 'License agreements');
    expect(stepTitle).to.contain(installPage.secondStepEnTitle);
  });

  it('should agree to terms and conditions and go to step \'System compatibility\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSystemCompatibility', baseContext);

    await installPage.agreeToTermsAndConditions(page);
    await installPage.nextStep(page);

    if (!(await installPage.isThirdStepVisible(page))) {
      const stepTitle = await installPage.getStepTitle(page, 'System compatibility');
      expect(stepTitle).to.contain(installPage.thirdStepEnTitle);
    }
  });

  it('should click on next and go to step \'Store Information\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToStoreInformation', baseContext);

    if (!(await installPage.isThirdStepVisible(page))) {
      await installPage.nextStep(page);
    }

    const stepTitle = await installPage.getStepTitle(page, 'Store information');
    expect(stepTitle).to.contain(installPage.fourthStepEnTitle);
  });

  it('should fill shop Information form and go to step \'Content Configuration\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToContentConfiguration', baseContext);

    await installPage.fillInformationForm(page);
    await installPage.nextStep(page);
    await installPage.waitForFinishedForthStep(page);

    const stepTitle = await installPage.getStepTitle(page, 'Content of your store');
    expect(stepTitle).to.contain(installPage.fifthStepEnTitle);
  });

  it('should click on next and go to step \'System Configuration\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToDatabaseInformation', baseContext);

    await installPage.nextStep(page);
    await installPage.waitForFinishedFifthStep(page);

    const stepTitle = await installPage.getStepTitle(page, 'System configuration');
    expect(stepTitle).to.contain(installPage.sixthStepEnTitle);
  });

  it('should fill database configuration form and check database connection', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkDatabaseConnection', baseContext);

    await installPage.fillDatabaseForm(page);
    const result = await installPage.isDatabaseConnected(page);
    expect(result).to.eq(true);
  });

  it('should start the installation process', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'startInstallation', baseContext);

    await installPage.nextStep(page);
    const result = await installPage.isInstallationInProgress(page);
    expect(result).to.eq(true);
  });

  const tests = [
    {
      args:
        {
          step: {
            name: 'Generate Setting file',
            timeout: 10000,
          },
        },
    },
    {
      args:
        {
          step: {
            name: 'Install database',
            timeout: 30000,
          },
        },
    },
    {
      args:
        {
          step: {
            name: 'Default data',
            timeout: 30000,
          },
        },
    },
    {
      args:
        {
          step: {
            name: 'Populate database',
            timeout: 30000,
          },
        },
    },
    {
      args:
        {
          step: {
            name: 'Shop configuration',
            timeout: 30000,
          },
        },
    },
    {
      args:
        {
          step: {
            name: 'Install theme',
            timeout: 60000,
          },
        },
    },
    {
      args:
        {
          step: {
            name: 'Install modules',
            timeout: 30000,
          },
        },
    },
    {
      args:
        {
          step: {
            name: 'Install fixtures',
            timeout: 30000,
          },
        },
    },
    {
      args:
        {
          step: {
            name: 'Post installation scripts',
            timeout: 60000,
          },
        },
    },
  ];

  tests.forEach((test, index: number) => {
    it(`should installation step '${test.args.step.name}' be finished`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `CheckStep${index}`, baseContext);

      const stepFinished = await installPage.isInstallationStepFinished(
        page,
        test.args.step.name,
        test.args.step.timeout,
      );
      expect(stepFinished, `Fail to finish the step ${test.args.step.name}`).to.eq(true);
    });
  });

  it('should installation be successful', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkInstallationSuccessful', baseContext);

    const result = await installPage.isInstallationSuccessful(page);
    expect(result).to.eq(true);

    const stepTitle = await installPage.getStepTitle(page, 'Installation finished');
    expect(stepTitle).to.contain(installPage.finalStepEnTitle);
  });

  it('should go to FO and check that Prestashop logo exists', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkPrestashopFO', baseContext);

    page = await installPage.goToFOAfterInstall(page);

    const result = await homePage.isHomePage(page);
    expect(result).to.eq(true);
  });
});
