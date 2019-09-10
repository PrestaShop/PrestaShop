// Using chai
const {expect} = require('chai');
// Using CommonPage
const CommonPage = require('../commonPage');

module.exports = class Install extends CommonPage {
  constructor(page) {
    super(page);

    // Define Step Titles
    this.firstStepFrTitle = 'Bienvenue sur l\'installateur de PrestaShop';
    this.firstStepEnTitle = 'Welcome to the PrestaShop';
    this.secondStepEnTitle = 'License Agreements';
    this.thirdStepEnTitle = 'We are currently checking PrestaShop compatibility with your system environment';
    this.fourthStepEnTitle = 'Information about your Store';
    this.fifthStepEnTitle = 'Configure your database by filling out the following fields';
    this.finalStepEnTitle = 'Your installation is finished!';

    // Selectors for all steps
    this.nextStepButton = '#btNext';

    // Selectors for step 1
    this.firstStepPageTitle = 'h2';
    this.languageSelect = '#langList';

    // Selectors for step 2
    this.secondStepPageTitle = 'h2#licenses-agreement';
    this.termsConditionsCheckbox = '#set_license';

    // Selectors for step 3
    this.thirdStepPageTitle = '#sheet_system h2';
    this.thirdStepFinishedListItem = '#leftpannel #tabs li.finished:nth-child(3)';

    // Selectors for step 4
    this.fourthStepPageTitle = '#infosShopBlock h2';
    this.shopNameInput = '#infosShop';
    this.countrySelect = '#infosCountry';
    this.firstNameInput = '#infosFirstname';
    this.lastNameInput = '#infosName';
    this.emailInput = '#infosEmail';
    this.passwordInput = '#infosPassword';
    this.repeatPasswordInput = '#infosPasswordRepeat';

    // Selectors for step 5
    this.fifthStepPageTitle = '#dbPart h2';
    this.dbLoginInput = '#dbLogin';
    this.dbPasswordInput = '#dbPassword';
    this.testDbConnectionButton = '#btTestDB';
    this.createDbButton = '#btCreateDB';
    this.dbResultCheckOkBlock = '#dbResultCheck.okBlock';

    // Selectors for Final step
    this.installationProgressBar = '#install_process_form #progress_bar .installing';
    this.generateSettingsFileStep = '#process_step_generateSettingsFile';
    this.installDatabaseStep = '#process_step_installDatabase';
    this.installDefaultDataStep = '#process_step_installDefaultData';
    this.populateDatabaseStep = '#process_step_populateDatabase';
    this.configureShopStep = '#process_step_configureShop';
    this.installModulesStep = '#process_step_installModules';
    this.installModulesAddons = '#process_step_installModulesAddons';
    this.installThemeStep = '#process_step_installTheme';
    this.installFixturesStep = '#process_step_installFixtures';
    this.finalStepPageTitle = '#install_process_success h2';
    this.discoverFoButton = '#foBlock';

    // Selectors in FO
    this.FOLogo = '#_desktop_logo';
    this.userInfoHeaderIcon = '#_desktop_user_info';
    this.cartHeaderIcon = '#_desktop_cart';
  }

  /**
   * To check each step title
   * @param selector, where to get actual title
   * @param pageTitle, expected title
   */
  async checkStepTitle(selector, pageTitle) {
    await this.page.waitFor(selector, {visible: true, timeout: 90000});
    const title = await this.getTextContent(selector);
    await expect(title).to.contains(pageTitle);
  }

  /**
   * Change install language in step 1
   */
  async setInstallLanguage() {
    await this.page.select(this.languageSelect, global.INSTALL_LANGUAGE);
  }

  /**
   * Go to next step
   */
  async nextStep() {
    await this.page.waitForSelector(this.nextStepButton, {visible: true});
    await this.page.click(this.nextStepButton, {waitUntil: 'domcontentloaded'});
  }

  /**
   * Click on checkbox to agree on terms and conditions if its not checked already in step 2
   */
  async agreeToTermsAndConditions() {
    const isChecked = await this.elementChecked(this.termsConditionsCheckbox);
    if (!isChecked) {
      await this.page.click(this.termsConditionsCheckbox);
    }
  }

  /**
   * Fill Information and Account Forms in step 4
   */
  async fillInformationForm() {
    await this.page.type(this.shopNameInput, global.SHOPNAME);
    await this.page.select(this.countrySelect, global.INSTALL_COUNTRY);
    await this.page.type(this.firstNameInput, 'demo');
    await this.page.type(this.lastNameInput, 'demo');
    await this.page.type(this.emailInput, global.EMAIL);
    await this.page.type(this.passwordInput, global.PASSWD);
    await this.page.type(this.repeatPasswordInput, global.PASSWD);
  }

  /**
   * Fill Database Form in step 5
   */
  async fillDatabaseForm() {
    await this.page.click(this.dbLoginInput, {clickCount: 3});
    await this.page.type(this.dbLoginInput, global.db_user);
    await this.page.click(this.dbPasswordInput, {clickCount: 3});
    await this.page.type(this.dbPasswordInput, global.db_passwd);
  }

  /**
   * Check if database exist (if not, it will be created)
   * and check if all set properly to submit form
   */
  async checkDatabaseConnected() {
    await this.page.click(this.testDbConnectionButton);
    // Create database 'prestashop' if not exist
    if (await this.elementVisible(this.createDbButton, 3000)) {
      await this.page.click(this.createDbButton);
    }
    await this.page.waitForSelector(this.dbResultCheckOkBlock);
  }

  /**
   * Check if prestashop is installed properly
   */
  async checkInstallationSuccessful() {
    await this.page.waitForSelector(this.installationProgressBar, {visible: true});
    await this.page.waitFor(this.installationProgressBar, {visible: true});
    await this.page.waitFor(this.generateSettingsFileStep, {visible: true});
    await this.page.waitFor(this.installDatabaseStep, {visible: true});
    await this.page.waitFor(this.installDefaultDataStep, {visible: true, timeout: 360000});
    await this.page.waitFor(this.populateDatabaseStep, {visible: true, timeout: 360000});
    await this.page.waitFor(this.configureShopStep, {visible: true, timeout: 360000});
    await this.page.waitFor(this.installModulesStep, {visible: true});
    await this.page.waitFor(this.installModulesAddons, {visible: true, timeout: 360000});
    await this.page.waitFor(this.installThemeStep, {visible: true});
    await this.page.waitFor(this.installFixturesStep, {visible: true});
    await this.page.waitForSelector(this.finalStepPageTitle, {visible: true, timeout: 90000});
    await this.checkStepTitle(this.finalStepPageTitle, this.finalStepEnTitle);
  }

  /**
   * Go to FO after Installation and check that Prestashop logo exist
   */
  async goAndCheckFOAfterInstall() {
    await this.page.waitForSelector(this.discoverFoButton, {visible: true});
    const FOPage = await this.openLinkWithTargetBlank(this.page, this.discoverFoButton);
    await FOPage.bringToFront();
    await FOPage.waitForSelector(this.FOLogo, {visible: true});
    await FOPage.waitForSelector(this.userInfoHeaderIcon, {visible: true});
    await FOPage.waitForSelector(this.cartHeaderIcon, {visible: true});
  }
};
