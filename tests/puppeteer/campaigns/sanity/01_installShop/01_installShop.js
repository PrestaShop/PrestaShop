// Importing pages
const InstallPage = require('../../../pages/Install/install');

let page;
let installPage;

// Init objects needed
const init = async () => {
  page = await global.browser.newPage();
  installPage = await (new InstallPage(page));
};

// Scenario
global.scenario('Install Prestashop', async () => {
  test('should open the Install page', async () => {
    await installPage.goTo(global.URL_INSTALL);
    await installPage.checkStepTitle(installPage.firstStepPageTitle, installPage.firstStepFrTitle);
  });
  test('should change language to English and check title', async () => {
    await installPage.setInstallLanguage();
    await installPage.checkStepTitle(installPage.firstStepPageTitle, installPage.firstStepEnTitle);
  });
  test('should click on next and go to step \'License Agreements\'', async () => {
    await installPage.nextStep();
    await installPage.checkStepTitle(installPage.secondStepPageTitle, installPage.secondStepEnTitle);
  });
  test('should agree to terms and conditions and go to step \'System compatibility\'', async () => {
    await installPage.agreeToTermsAndConditions();
    await installPage.nextStep();
    if (!installPage.elementVisible(installPage.thirdStepFinishedListItem)) {
      await installPage.checkStepTitle(installPage.thirdStepPageTitle, installPage.thirdStepEnTitle);
    }
  });
  test('should click on next and go to step \'shop Information\'', async () => {
    if (!installPage.elementVisible(installPage.thirdStepFinishedListItem)) {
      await installPage.nextStep();
    }
    await installPage.checkStepTitle(installPage.fourthStepPageTitle, installPage.fourthStepEnTitle);
  });
  test('should fill shop Information form and go to step \'Database Configuration\'', async () => {
    await installPage.fillInformationForm();
    await installPage.nextStep();
    await installPage.checkStepTitle(installPage.fifthStepPageTitle, installPage.fifthStepEnTitle);
  });
  test('should fill database configuration form and check database connection', async () => {
    await installPage.fillDatabaseForm();
    await installPage.checkDatabaseConnected();
  });
  test('should finish installation and check that installation is successful', async () => {
    await installPage.nextStep();
    await installPage.checkInstallationSuccessful();
  });
  test('should go to FO and check that Prestashop logo exists', async () => {
    await installPage.goAndCheckFOAfterInstall();
  });
}, init, true);
