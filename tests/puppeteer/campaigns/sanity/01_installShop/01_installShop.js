require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
// Importing pages
const InstallPage = require('@pages/Install/install');
const HomePage = require('@pages/FO/home');

let browser;
let page;

// Init objects needed
const init = async function () {
  return {
    installPage: new InstallPage(page),
    homePage: new HomePage(page),
  };
};

describe('Install Prestashop', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });
  // Steps
  it('should open the Install page', async function () {
    await this.pageObjects.installPage.goTo(global.INSTALL.URL);
    const result = await this.pageObjects.installPage.checkStepTitle(
      this.pageObjects.installPage.firstStepPageTitle,
      [
        this.pageObjects.installPage.firstStepFrTitle,
        this.pageObjects.installPage.firstStepEnTitle,
      ],
    );
    await expect(result).to.be.true;
  });

  it('should change language to English and check title', async function () {
    await this.pageObjects.installPage.setInstallLanguage();
    const result = await this.pageObjects.installPage.checkStepTitle(
      this.pageObjects.installPage.firstStepPageTitle,
      this.pageObjects.installPage.firstStepEnTitle,
    );
    await expect(result).to.be.true;
  });

  it('should click on next and go to step \'License Agreements\'', async function () {
    await this.pageObjects.installPage.nextStep();
    const result = await this.pageObjects.installPage.checkStepTitle(
      this.pageObjects.installPage.secondStepPageTitle,
      this.pageObjects.installPage.secondStepEnTitle,
    );
    await expect(result).to.be.true;
  });

  it('should agree to terms and conditions and go to step \'System compatibility\'', async function () {
    await this.pageObjects.installPage.agreeToTermsAndConditions();
    await this.pageObjects.installPage.nextStep();
    if (!this.pageObjects.installPage.elementVisible(this.pageObjects.installPage.thirdStepFinishedListItem)) {
      const result = await this.pageObjects.installPage.checkStepTitle(
        this.pageObjects.installPage.thirdStepPageTitle,
        this.pageObjects.installPage.thirdStepEnTitle,
      );
      await expect(result).to.be.true;
    }
  });

  it('should click on next and go to step \'shop Information\'', async function () {
    if (!this.pageObjects.installPage.elementVisible(this.pageObjects.installPage.thirdStepFinishedListItem)) {
      await this.pageObjects.installPage.nextStep();
    }
    const result = await this.pageObjects.installPage.checkStepTitle(
      this.pageObjects.installPage.fourthStepPageTitle,
      this.pageObjects.installPage.fourthStepEnTitle,
    );
    await expect(result).to.be.true;
  });

  it('should fill shop Information form and go to step \'Database Configuration\'', async function () {
    await this.pageObjects.installPage.fillInformationForm();
    await this.pageObjects.installPage.nextStep();
    const result = await this.pageObjects.installPage.checkStepTitle(
      this.pageObjects.installPage.fifthStepPageTitle,
      this.pageObjects.installPage.fifthStepEnTitle,
    );
    await expect(result).to.be.true;
  });

  it('should fill database configuration form and check database connection', async function () {
    await this.pageObjects.installPage.fillDatabaseForm();
    const result = await this.pageObjects.installPage.isDatabaseConnected();
    await expect(result).to.be.true;
  });

  it('should finish installation and check that installation is successful', async function () {
    await this.pageObjects.installPage.nextStep();
    const result = await this.pageObjects.installPage.isInstallationSuccessful();
    await expect(result).to.be.true;
  });

  it('should go to FO and check that Prestashop logo exists', async function () {
    page = await this.pageObjects.installPage.goToFOAfterInstall();
    this.pageObjects = await init();
    const result = await this.pageObjects.homePage.isHomePage();
    await expect(result).to.be.true;
  });
});
