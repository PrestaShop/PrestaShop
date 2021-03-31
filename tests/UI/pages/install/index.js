require('module-alias/register');
// Using CommonPage
const CommonPage = require('@pages/commonPage');

class Install extends CommonPage {
  constructor() {
    super();

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
    this.chooseLanguageStepPageTitle = 'h2';
    this.languageSelect = '#langList';

    // Selectors for step 2
    this.licenseAgreementsStepPageTitle = 'h2#licenses-agreement';
    this.termsConditionsCheckbox = '#set_license';

    // Selectors for step 3
    this.systemCompatibilityStepPageTitle = '#sheet_system h2';
    this.thirdStepFinishedListItem = '#leftpannel #tabs li.finished:nth-child(3)';

    // Selectors for step 4
    this.storeInformationStepPageTitle = '#infosShopBlock h2';
    this.shopNameInput = '#infosShop';
    this.countrySelect = '#infosCountry';
    this.firstNameInput = '#infosFirstname';
    this.lastNameInput = '#infosName';
    this.emailInput = '#infosEmail';
    this.passwordInput = '#infosPassword';
    this.repeatPasswordInput = '#infosPasswordRepeat';

    // Selectors for step 5
    this.systemConfigurationStepPageTitle = '#dbPart h2';
    this.dbServerInput = '#dbServer';
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
    this.installationFinishedStepPageTitle = '#install_process_success h2';
    this.discoverFoButton = '#foBlock';
  }

  /**
   * Get step title
   * @param page
   * @param step
   * @returns {Promise<string>}
   */
  async getStepTitle(page, step) {
    let selector;

    switch (step) {
      case 'Choose your language':
        selector = this.chooseLanguageStepPageTitle;
        break;

      case 'License agreements':
        selector = this.licenseAgreementsStepPageTitle;
        break;

      case 'System compatibility':
        selector = this.systemCompatibilityStepPageTitle;
        break;

      case 'Store information':
        selector = this.storeInformationStepPageTitle;
        break;

      case 'System configuration':
        selector = this.systemConfigurationStepPageTitle;
        break;

      case 'Installation finished':
        selector = this.installationFinishedStepPageTitle;
        break;

      default:
        throw new Error(`'${step}' was not found on the installation process`);
    }

    return this.getTextContent(page, selector);
  }

  /**
   * Change install language in step 1
   * @param page
   * @return {Promise<void>}
   */
  async setInstallLanguage(page) {
    await page.selectOption(this.languageSelect, global.INSTALL.LANGUAGE);
  }

  /**
   * Go to next step
   * @param page
   * @return {Promise<void>}
   */
  async nextStep(page) {
    await this.waitForVisibleSelector(page, this.nextStepButton);
    await page.click(this.nextStepButton, {waitUntil: 'load'});
  }

  /**
   * Click on checkbox to agree on terms and conditions if its not checked already in step 2
   * @param page
   * @return {Promise<void>}
   */
  async agreeToTermsAndConditions(page) {
    const isChecked = await this.elementChecked(page, this.termsConditionsCheckbox);
    if (!isChecked) {
      await page.click(this.termsConditionsCheckbox);
    }
  }

  /**
   * Fill Information and Account Forms in step 4
   * @param page
   * @return {Promise<void>}
   */
  async fillInformationForm(page) {
    await page.type(this.shopNameInput, global.INSTALL.SHOP_NAME);
    await page.selectOption(this.countrySelect, global.INSTALL.COUNTRY);
    await page.type(this.firstNameInput, global.BO.FIRSTNAME);
    await page.type(this.lastNameInput, global.BO.LASTNAME);
    await page.type(this.emailInput, global.BO.EMAIL);
    await page.type(this.passwordInput, global.BO.PASSWD);
    await page.type(this.repeatPasswordInput, global.BO.PASSWD);
  }

  /**
   * Fill Database Form in step 5
   * @param page
   * @return {Promise<void>}
   */
  async fillDatabaseForm(page) {
    await this.setValue(page, this.dbServerInput, global.INSTALL.DB_SERVER);
    await this.setValue(page, this.dbNameInput, global.INSTALL.DB_NAME);
    await this.setValue(page, this.dbLoginInput, global.INSTALL.DB_USER);
    await this.setValue(page, this.dbPasswordInput, global.INSTALL.DB_PASSWD);
  }

  /**
   * Check if database exist (if not, it will be created)
   * and check if all set properly to submit form
   * @param page
   * @return {Promise<boolean>}
   */
  async isDatabaseConnected(page) {
    await page.click(this.testDbConnectionButton);

    // Create database 'prestashop' if not exist
    if (await this.elementVisible(page, this.createDbButton, 3000)) {
      await page.click(this.createDbButton);
    }

    return this.elementVisible(page, this.dbResultCheckOkBlock, 3000);
  }

  /**
   * Check if prestashop is installed properly
   * @param page
   * @return {Promise<*>}
   */
  async isInstallationSuccessful(page) {
    await Promise.all([
      this.waitForVisibleSelector(page, this.installationProgressBar, 30000),
      this.waitForVisibleSelector(page, this.generateSettingsFileStep, 30000),
      this.waitForVisibleSelector(page, this.installDatabaseStep, 60000),
      this.waitForVisibleSelector(page, this.installDefaultDataStep, 120000),
      this.waitForVisibleSelector(page, this.populateDatabaseStep, 180000),
      this.waitForVisibleSelector(page, this.configureShopStep, 240000),
      this.waitForVisibleSelector(page, this.installModulesStep, 360000),
      this.waitForVisibleSelector(page, this.installModulesAddons, 360000),
      this.waitForVisibleSelector(page, this.installThemeStep, 360000),
      this.waitForVisibleSelector(page, this.installFixturesStep, 360000),
      this.waitForVisibleSelector(page, this.installationFinishedStepPageTitle, 360000),
    ]);

    return true;
  }

  /**
   * Go to FO after Installation and check that Prestashop logo exist
   * @param page
   * @return {Promise<*>}
   */
  async goToFOAfterInstall(page) {
    await this.waitForVisibleSelector(page, this.discoverFoButton);
    return this.openLinkWithTargetBlank(page, this.discoverFoButton);
  }
}

module.exports = new Install();
