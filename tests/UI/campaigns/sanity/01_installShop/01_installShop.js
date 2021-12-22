require('module-alias/register');

const {expect} = require('chai');

// Import browser helper
const helper = require('@utils/helpers');

// Import pages
const installPage = require('@pages/install');
const homePage = require('@pages/FO/home');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'sanity_installShop_installShop';

let browserContext;
let page;

describe('Install Prestashop', async () => {
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

    await expect(installationTitles.some(x => stepTitle.includes(x))).to.be.true;
  });

  it('should change language to English and check title', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'ChangeLanguageToEnglish', baseContext);

    await installPage.setInstallLanguage(page);

    const stepTitle = await installPage.getStepTitle(page, 'Choose your language');
    await expect(stepTitle).to.contain(installPage.firstStepEnTitle);
  });

  it('should click on next and go to step \'License Agreements\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLicenseAgreements', baseContext);

    await installPage.nextStep(page);

    const stepTitle = await installPage.getStepTitle(page, 'License agreements');
    await expect(stepTitle).to.contain(installPage.secondStepEnTitle);
  });

  it('should agree to terms and conditions and go to step \'System compatibility\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSystemCompatibility', baseContext);

    await installPage.agreeToTermsAndConditions(page);
    await installPage.nextStep(page);

    if (!(await installPage.elementVisible(page, installPage.thirdStepFinishedListItem, 500))) {
      const stepTitle = await installPage.getStepTitle(page, 'System compatibility');
      await expect(stepTitle).to.contain(installPage.thirdStepEnTitle);
    }
  });

  it('should click on next and go to step \'shop Information\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToShopInformation', baseContext);

    if (!(await installPage.elementVisible(page, installPage.thirdStepFinishedListItem, 500))) {
      await installPage.nextStep(page);
    }

    const stepTitle = await installPage.getStepTitle(page, 'Store information');
    await expect(stepTitle).to.contain(installPage.fourthStepEnTitle);
  });

  it('should fill shop Information form and go to step \'Database Configuration\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToDatabaseConfiguration', baseContext);

    await installPage.fillInformationForm(page);
    await installPage.nextStep(page);

    const stepTitle = await installPage.getStepTitle(page, 'System configuration');
    await expect(stepTitle).to.contain(installPage.fifthStepEnTitle);
  });

  it('should fill database configuration form and check database connection', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkDatabaseConnection', baseContext);

    await installPage.fillDatabaseForm(page);
    const result = await installPage.isDatabaseConnected(page);
    await expect(result).to.be.true;
  });

  it('should start the installation process', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'startInstallation', baseContext);

    await installPage.nextStep(page);
    const result = await installPage.isInstallationInProgress(page);
    await expect(result).to.be.true;
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
            name: 'Install modules',
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

  tests.forEach((test, index) => {
    it(`should installation step '${test.args.step.name}' be finished`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `CheckStep${index}`, baseContext);

      const stepFinished = await installPage.isInstallationStepFinished(
        page,
        test.args.step.name,
        test.args.step.timeout,
      );

      await expect(stepFinished, `Fail to finish the step ${test.args.step.name}`).to.be.true;
    });
  });

  it('should installation be successful', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkInstallationSuccessful', baseContext);

    const result = await installPage.isInstallationSuccessful(page);
    await expect(result).to.be.true;

    const stepTitle = await installPage.getStepTitle(page, 'Installation finished');
    await expect(stepTitle).to.contain(installPage.finalStepEnTitle);
  });

  it('should go to FO and check that Prestashop logo exists', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkPrestashopFO', baseContext);

    page = await installPage.goToFOAfterInstall(page);
    const result = await homePage.isHomePage(page);
    await expect(result).to.be.true;
  });
});
