// Importing pages
const INSTALL_PAGE = require('../../pages/Install/install');

let page;
let INSTALL;

// Init objects needed
const init = async () => {
  page = await global.browser.newPage();
  INSTALL = await (new INSTALL_PAGE(page));
};

// Scenario
global.scenario('Install Prestashop', async () => {
  test('should open the Install page', async () => {
    await INSTALL.goTo(global.URL_INSTALL);
    await INSTALL.checkStepTitle(INSTALL.firstStepPageTitle, INSTALL.firstStepFrTitle);
  });
  test('should change language to English and check title', async () => {
    await INSTALL.setInstallLanguage();
    await INSTALL.checkStepTitle(INSTALL.firstStepPageTitle, INSTALL.firstStepEnTitle);
  });
  test('should click on next and go to step \'License Agreements\'', async () => {
    await INSTALL.nextStep();
    await INSTALL.checkStepTitle(INSTALL.secondStepPageTitle, INSTALL.secondStepEnTitle);
  });
  test('should agree to terms and conditions and go to step \'System compatibility\'', async () => {
    await INSTALL.agreeToTermsAndConditions();
    await INSTALL.nextStep();
  });
  test('should click on next and go to step \'shop Information\'', async () => {
    if (!INSTALL.elementVisible(INSTALL.stepFinishedLeftMenu)) {
      await INSTALL.checkStepTitle(INSTALL.thirdStepPageTitle, INSTALL.thirdStepEnTitle);
      await INSTALL.nextStep();
    }
    await INSTALL.checkStepTitle(INSTALL.fourthStepPageTitle, INSTALL.fourthStepEnTitle);
  });
  test('should fill shop Information form and go to step \'Database Configuration\'', async () => {
    await INSTALL.fillInformationForm();
    await INSTALL.nextStep();
    await INSTALL.checkStepTitle(INSTALL.fifthStepPageTitle, INSTALL.fifthStepEnTitle);
  });
  test('should fill database configuration form and check database connection', async () => {
    await INSTALL.fillDatabaseForm();
    await INSTALL.checkDatabaseConnected();
  });
  test('should finish installation and check that installation is successful', async () => {
    await INSTALL.nextStep();
    await INSTALL.checkInstallationSuccessful();
  });
  test('should go to FO and check that Prestashop logo exists', async () => {
    await INSTALL.goAndCheckFOAfterInstall();
  });
}, init, true);
