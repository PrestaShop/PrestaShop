require('module-alias/register');
// Using CommonPage
const CommonPage = require('@pages/commonPage');

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
    this.dbNameInput = '#dbName';
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
  }

  /**
   * To check each step title
   * @param selector, where to get actual title
   * @param pageTitle, expected title
   * @return {Promise<*>}
   */
  async checkStepTitle(selector, pageTitle) {
    await this.waitForVisibleSelector(selector);
    const title = await this.getTextContent(selector);
    if (Array.isArray(pageTitle)) {
      return pageTitle.some(arrVal => title.includes(arrVal));
    }
    return title.includes(pageTitle);
  }

  /**
   * Change install language in step 1
   */
  async setInstallLanguage() {
    await this.page.select(this.languageSelect, global.INSTALL.LANGUAGE);
  }

  /**
   * Go to next step
   */
  async nextStep() {
    await this.waitForVisibleSelector(this.nextStepButton);
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
    await this.page.type(this.shopNameInput, global.INSTALL.SHOPNAME);
    await this.page.select(this.countrySelect, global.INSTALL.COUNTRY);
    await this.page.type(this.firstNameInput, global.BO.FIRSTNAME);
    await this.page.type(this.lastNameInput, global.BO.LASTNAME);
    await this.page.type(this.emailInput, global.BO.EMAIL);
    await this.page.type(this.passwordInput, global.BO.PASSWD);
    await this.page.type(this.repeatPasswordInput, global.BO.PASSWD);
  }

  /**
   * Fill Database Form in step 5
   */
  async fillDatabaseForm() {
    await this.setValue(this.dbNameInput, global.INSTALL.DB_NAME);
    await this.setValue(this.dbLoginInput, global.INSTALL.DB_USER);
    await this.setValue(this.dbPasswordInput, global.INSTALL.DB_PASSWD);
  }

  /**
   * Check if database exist (if not, it will be created)
   * and check if all set properly to submit form
   * @return {Promise<boolean>}
   */
  async isDatabaseConnected() {
    await this.page.click(this.testDbConnectionButton);
    // Create database 'prestashop' if not exist
    if (await this.elementVisible(this.createDbButton, 3000)) {
      await this.page.click(this.createDbButton);
    }
    return this.elementVisible(this.dbResultCheckOkBlock, 3000);
  }

  /**
   * Check if prestashop is installed properly
   */
  async isInstallationSuccessful() {
    await Promise.all([
      this.waitForVisibleSelector(this.installationProgressBar, 30000),
      this.waitForVisibleSelector(this.generateSettingsFileStep, 30000),
      this.waitForVisibleSelector(this.installDatabaseStep, 60000),
      this.waitForVisibleSelector(this.installDefaultDataStep, 120000),
      this.waitForVisibleSelector(this.populateDatabaseStep, 180000),
      this.waitForVisibleSelector(this.configureShopStep, 240000),
      this.waitForVisibleSelector(this.installModulesStep, 360000),
      this.waitForVisibleSelector(this.installModulesAddons, 360000),
      this.waitForVisibleSelector(this.installThemeStep, 360000),
      this.waitForVisibleSelector(this.installFixturesStep, 360000),
      this.waitForVisibleSelector(this.finalStepPageTitle, 360000),
    ]);
    return this.checkStepTitle(this.finalStepPageTitle, this.finalStepEnTitle);
  }

  /**
   * Go to FO after Installation and check that Prestashop logo exist
   */
  async goToFOAfterInstall() {
    await this.waitForVisibleSelector(this.discoverFoButton);
    return this.openLinkWithTargetBlank(this.page, this.discoverFoButton, false);
  }
};
