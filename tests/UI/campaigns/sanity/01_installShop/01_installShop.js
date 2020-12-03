require('module-alias/register');
const testContext = require('@utils/testContext');

const baseContext = 'sanity_installShop_installShop';
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
// Importing pages
const installPage = require('@pages/install');
const homePage = require('@pages/FO/home');

let browserContext;
let page;

// Init objects needed

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
    const result = await installPage.checkStepTitle(
      page,
      installPage.firstStepPageTitle,
      [
        installPage.firstStepFrTitle,
        installPage.firstStepEnTitle,
      ],
    );
    await expect(result).to.be.true;
  });

  it('should change language to English and check title', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'ChangeLanguageToEnglish', baseContext);
    await installPage.setInstallLanguage(page);
    const result = await installPage.checkStepTitle(
      page,
      installPage.firstStepPageTitle,
      installPage.firstStepEnTitle,
    );
    await expect(result).to.be.true;
  });

  it('should click on next and go to step \'License Agreements\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLicenseAgreements', baseContext);
    await installPage.nextStep(page);
    const result = await installPage.checkStepTitle(
      page,
      installPage.secondStepPageTitle,
      installPage.secondStepEnTitle,
    );
    await expect(result).to.be.true;
  });

  it('should agree to terms and conditions and go to step \'System compatibility\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSystemCompatibility', baseContext);
    await installPage.agreeToTermsAndConditions(page);
    await installPage.nextStep(page);
    if (!installPage.elementVisible(page, installPage.thirdStepFinishedListItem)) {
      const result = await installPage.checkStepTitle(
        page,
        installPage.thirdStepPageTitle,
        installPage.thirdStepEnTitle,
      );
      await expect(result).to.be.true;
    }
  });

  it('should click on next and go to step \'shop Information\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToShopInformation', baseContext);
    if (!installPage.elementVisible(page, installPage.thirdStepFinishedListItem)) {
      await installPage.nextStep(page);
    }
    const result = await installPage.checkStepTitle(
      page,
      installPage.fourthStepPageTitle,
      installPage.fourthStepEnTitle,
    );
    await expect(result).to.be.true;
  });

  it('should fill shop Information form and go to step \'Database Configuration\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToDatabaseConfiguration', baseContext);
    await installPage.fillInformationForm(page);
    await installPage.nextStep(page);
    const result = await installPage.checkStepTitle(
      page,
      installPage.fifthStepPageTitle,
      installPage.fifthStepEnTitle,
    );
    await expect(result).to.be.true;
  });

  it('should fill database configuration form and check database connection', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkDatabaseConnection', baseContext);
    await installPage.fillDatabaseForm(page);
    const result = await installPage.isDatabaseConnected(page);
    await expect(result).to.be.true;
  });

  it('should finish installation and check that installation is successful', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkInstallationSuccessful', baseContext);
    await installPage.nextStep(page);
    const result = await installPage.isInstallationSuccessful(page);
    await expect(result).to.be.true;
  });

  it('should go to FO and check that Prestashop logo exists', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkPrestashopFO', baseContext);
    page = await installPage.goToFOAfterInstall(page);
    const result = await homePage.isHomePage(page);
    await expect(result).to.be.true;
  });
});
