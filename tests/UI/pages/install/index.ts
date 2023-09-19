import type {Page} from 'playwright';

// Import pages
import CommonPage from '@pages/commonPage';

/**
 * Install page, contains functions used in different steps of the installation
 * @class
 * @extends CommonPage
 */
class Install extends CommonPage {
  // Titles
  public readonly firstStepFrTitle: string;

  public readonly firstStepEnTitle: string;

  public readonly secondStepEnTitle: string;

  public readonly thirdStepEnTitle: string;

  public readonly fourthStepEnTitle: string;

  public readonly fifthStepEnTitle: string;

  public readonly sixthStepEnTitle: string;

  public readonly finalStepEnTitle: string;

  // Selectors
  private readonly nextStepButton: string;

  private readonly chooseLanguageStepPageTitle: string;

  private readonly languageSelect: string;

  private readonly licenseAgreementsStepPageTitle: string;

  private readonly termsConditionsCheckbox: string;

  private readonly systemCompatibilityStepPageTitle: string;

  private readonly thirdStepFinishedListItem: string;

  private readonly fourthStepFinishedListItem: string;

  private readonly storeInformationStepPageTitle: string;

  private readonly shopNameInput: string;

  private readonly countryChosenSelect: string;

  private readonly countryChosenSearchInput: string;

  private readonly enableSslRadio: (value: number) => string;

  private readonly firstNameInput: string;

  private readonly lastNameInput: string;

  private readonly emailInput: string;

  private readonly passwordInput: string;

  private readonly repeatPasswordInput: string;

  private readonly contentInformationStepPageTitle: string;

  private readonly fifthStepFinishedListItem: string;

  private readonly systemConfigurationStepPageTitle: string;

  private readonly dbServerInput: string;

  private readonly dbLoginInput: string;

  private readonly dbNameInput: string;

  private readonly dbPasswordInput: string;

  private readonly dbPrefixInput: string;

  private readonly testDbConnectionButton: string;

  private readonly createDbButton: string;

  private readonly dbResultCheckOkBlock: string;

  private readonly installationProgressBar: string;

  private readonly generateSettingsFileStep: string;

  private readonly installDatabaseStep: string;

  private readonly installDefaultDataStep: string;

  private readonly populateDatabaseStep: string;

  private readonly configureShopStep: string;

  private readonly installModulesStep: string;

  private readonly installThemeStep: string;

  private readonly installFixturesStep: string;

  private readonly installPostInstall: string;

  private readonly installationFinishedStepPageTitle: string;

  private readonly discoverFoButton: string;

  /**
   * @constructs
   * Setting up titles and selectors to use on installation page
   */
  constructor() {
    super();

    // Define Step Titles
    this.firstStepFrTitle = 'Bienvenue sur l\'installateur de PrestaShop';
    this.firstStepEnTitle = 'Welcome to the PrestaShop';
    this.secondStepEnTitle = 'License Agreements';
    this.thirdStepEnTitle = 'We are currently checking PrestaShop compatibility with your system environment';
    this.fourthStepEnTitle = 'Information about your Store';
    this.fifthStepEnTitle = 'Content of your store';
    this.sixthStepEnTitle = 'Configure your database by filling out the following fields';
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
    this.fourthStepFinishedListItem = '#leftpannel #tabs li.finished:nth-child(4)';
    this.storeInformationStepPageTitle = '#infosShopBlock h2';
    this.shopNameInput = '#infosShop';
    this.countryChosenSelect = '#infosCountry_chosen';
    this.countryChosenSearchInput = `${this.countryChosenSelect} .chosen-search input`;
    this.enableSslRadio = (value: number) => `input[name="enable_ssl"][value="${value}"]`;
    this.firstNameInput = '#infosFirstname';
    this.lastNameInput = '#infosName';
    this.emailInput = '#infosEmail';
    this.passwordInput = '#infosPassword';
    this.repeatPasswordInput = '#infosPasswordRepeat';

    // Selectors for step 5
    this.contentInformationStepPageTitle = '#contentInfosBlock h2';
    this.fifthStepFinishedListItem = '#leftpannel #tabs li.finished:nth-child(5)';

    // Selectors for step 6
    this.systemConfigurationStepPageTitle = '#dbPart h2';
    this.dbServerInput = '#dbServer';
    this.dbLoginInput = '#dbLogin';
    this.dbNameInput = '#dbName';
    this.dbPasswordInput = '#dbPassword';
    this.dbPrefixInput = '#db_prefix';
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
    this.installThemeStep = '#process_step_installTheme';
    this.installFixturesStep = '#process_step_installFixtures';
    this.installPostInstall = '#process_step_postInstall';
    this.installationFinishedStepPageTitle = '#install_process_success h2';
    this.discoverFoButton = '#foBlock';
  }

  /**
   * Get step title
   * @param page {Page} Browser tab
   * @param step {string} Step to get title from
   * @returns {Promise<string>}
   */
  async getStepTitle(page : Page, step: string): Promise<string> {
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

      case 'Content of your store':
        selector = this.contentInformationStepPageTitle;
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
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async setInstallLanguage(page: Page): Promise<void> {
    await page.selectOption(this.languageSelect, global.INSTALL.LANGUAGE);
  }

  /**
   * Go to next step
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async nextStep(page: Page): Promise<void> {
    await this.waitForVisibleSelector(page, this.nextStepButton);
    await page.click(this.nextStepButton);
  }

  /**
   * Click on checkbox to agree on terms and conditions if it's not checked already in step 2
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async agreeToTermsAndConditions(page: Page):Promise<void> {
    await this.setChecked(page, this.termsConditionsCheckbox);
  }

  /**
   * Return the visibility of the third step
   * @param page {Page} Browser Tab
   */
  isThirdStepVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.thirdStepFinishedListItem, 500);
  }

  /**
   * Wait for Fourth step to be visible
   * @param page {Page} Browser Tab
   */
  async waitForFinishedForthStep(page: Page): Promise<void> {
    await this.waitForVisibleSelector(page, this.fourthStepFinishedListItem, 500);
  }

  /**
   * Wait for Fifth step to be visible
   * @param page {Page} Browser Tab
   */
  async waitForFinishedFifthStep(page: Page): Promise<void> {
    await this.waitForVisibleSelector(page, this.fifthStepFinishedListItem, 500);
  }

  /**
   * Fill Information and Account Forms in step 4
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async fillInformationForm(page: Page): Promise<void> {
    await page.locator(this.shopNameInput).fill(global.INSTALL.SHOP_NAME);

    // Choosing country
    await page.click(this.countryChosenSelect);
    await page.locator(this.countryChosenSearchInput).pressSequentially(global.INSTALL.COUNTRY);
    await page.keyboard.press('Enter');
    await page.click(this.enableSslRadio(global.INSTALL.ENABLE_SSL ? 1 : 0));

    await page.locator(this.firstNameInput).fill(global.BO.FIRSTNAME);
    await page.locator(this.lastNameInput).fill(global.BO.LASTNAME);
    await page.locator(this.emailInput).fill(global.BO.EMAIL);
    await page.locator(this.passwordInput).fill(global.BO.PASSWD);
    await page.locator(this.repeatPasswordInput).fill(global.BO.PASSWD);
  }

  /**
   * Fill Database Form in step 5
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async fillDatabaseForm(page: Page): Promise<void> {
    await this.setValue(page, this.dbServerInput, global.INSTALL.DB_SERVER);
    await this.setValue(page, this.dbNameInput, global.INSTALL.DB_NAME);
    await this.setValue(page, this.dbLoginInput, global.INSTALL.DB_USER);
    await this.setValue(page, this.dbPasswordInput, global.INSTALL.DB_PASSWD);
    await this.setValue(page, this.dbPrefixInput, global.INSTALL.DB_PREFIX);
  }

  /**
   * Check if database exist (if not, it will be created)
   * and check if all set properly to submit form
   * @param page {Page} Browser tab
   * @return {Promise<boolean>}
   */
  async isDatabaseConnected(page: Page): Promise<boolean> {
    await page.click(this.testDbConnectionButton);

    // Create database 'prestashop' if not exist
    if (await this.elementVisible(page, this.createDbButton, 3000)) {
      await page.click(this.createDbButton);
    }

    return this.elementVisible(page, this.dbResultCheckOkBlock, 3000);
  }

  /**
   * Check if progress bar is visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  isInstallationInProgress(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.installationProgressBar, 30000);
  }

  /**
   * Check if step installation is finished
   * @param page {Page} Browser tab
   * @param step {string} The installation step
   * @param timeout {number} Time to wait for step to finish
   * @returns {Promise<boolean>}
   */
  async isInstallationStepFinished(page: Page, step: string, timeout: number = 30000): Promise<boolean> {
    let selector;

    switch (step) {
      case 'Generate Setting file':
        selector = this.generateSettingsFileStep;
        break;

      case 'Install database':
        selector = this.installDatabaseStep;
        break;

      case 'Default data':
        selector = this.installDefaultDataStep;
        break;

      case 'Populate database':
        selector = this.populateDatabaseStep;
        break;

      case 'Shop configuration':
        selector = this.configureShopStep;
        break;

      case 'Install modules':
        selector = this.installModulesStep;
        break;

      case 'Install theme':
        selector = this.installThemeStep;
        break;

      case 'Install fixtures':
        selector = this.installFixturesStep;
        break;

      case 'Post installation scripts':
        selector = this.installPostInstall;
        break;

      default:
        throw new Error(`${step} was not found as an option`);
    }

    return this.elementVisible(page, `${selector}.success`, timeout);
  }

  /**
   * Check if prestashop is installed properly
   * @param page {Page} Browser tab
   * @return {Promise<boolean>}
   */
  isInstallationSuccessful(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.installationFinishedStepPageTitle, 30000);
  }

  /**
   * Go to FO after Installation and check that Prestashop logo exist
   * @param page {Page} Browser tab
   * @return {Promise<Page>}
   */
  async goToFOAfterInstall(page: Page): Promise<Page> {
    await this.waitForVisibleSelector(page, this.discoverFoButton);
    return this.openLinkWithTargetBlank(page, this.discoverFoButton);
  }
}

export default new Install();
