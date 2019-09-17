const helper = require('../../utils/helpers');
// Importing pages
const InstallPage = require('../../../pages/Install/install');

let browser;
let page;
let installPage;

// Init objects needed
const init = async function () {
  installPage = await (new InstallPage(page));
};

describe('Install Prestashop', async () => {
  before(async () => {
    browser = await helper.createBrowser();
    page = await browser.newPage();
    await init();
  });
  after(async () => {
    await browser.close();
  });

  it('should open the Install page', async () => {
    await installPage.goTo(global.URL_INSTALL);
    await installPage.checkStepTitle(installPage.firstStepPageTitle, installPage.firstStepFrTitle);
  });
  it('should change language to English and check title', async () => {
    await installPage.setInstallLanguage();
    await installPage.checkStepTitle(installPage.firstStepPageTitle, installPage.firstStepEnTitle);
  });
  it('should click on next and go to step \'License Agreements\'', async () => {
    await installPage.nextStep();
    await installPage.checkStepTitle(installPage.secondStepPageTitle, installPage.secondStepEnTitle);
  });
  it('should agree to terms and conditions and go to step \'System compatibility\'', async () => {
    await installPage.agreeToTermsAndConditions();
    await installPage.nextStep();
    if (!installPage.elementVisible(installPage.thirdStepFinishedListItem)) {
      await installPage.checkStepTitle(installPage.thirdStepPageTitle, installPage.thirdStepEnTitle);
    }
  });
  it('should click on next and go to step \'shop Information\'', async () => {
    if (!installPage.elementVisible(installPage.thirdStepFinishedListItem)) {
      await installPage.nextStep();
    }
    await installPage.checkStepTitle(installPage.fourthStepPageTitle, installPage.fourthStepEnTitle);
  });
  it('should fill shop Information form and go to step \'Database Configuration\'', async () => {
    await installPage.fillInformationForm();
    await installPage.nextStep();
    await installPage.checkStepTitle(installPage.fifthStepPageTitle, installPage.fifthStepEnTitle);
  });
  it('should fill database configuration form and check database connection', async () => {
    await installPage.fillDatabaseForm();
    await installPage.checkDatabaseConnected();
  });
  it('should finish installation and check that installation is successful', async () => {
    await installPage.nextStep();
    await installPage.checkInstallationSuccessful();
  });
  it('should go to FO and check that Prestashop logo exists', async () => {
    await installPage.goAndCheckFOAfterInstall();
  });
});
