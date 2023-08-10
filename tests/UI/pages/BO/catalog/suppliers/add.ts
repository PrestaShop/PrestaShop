import BOBasePage from '@pages/BO/BObasePage';

import type SupplierData from '@data/faker/supplier';

import type {Page} from 'playwright';

/**
 * Add supplier page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class AddSupplier extends BOBasePage {
  public readonly pageTitle: string;

  public readonly pageTitleEdit: string;

  private readonly nameInput: string;

  private readonly descriptionDiv: string;

  private readonly descriptionLangNavItemLink: (lang: string) => string;

  private readonly descriptionIFrame: (id: number) => string;

  private readonly homePhoneInput: string;

  private readonly mobilePhoneInput: string;

  private readonly addressInput: string;

  private readonly secondaryAddressInput: string;

  private readonly postalCodeInput: string;

  private readonly cityInput: string;

  private readonly countryInput: string;

  private readonly selectCountryList: string;

  private readonly searchCountryInput: string;

  private readonly countrySearchResult: string;

  private readonly stateInput: string;

  private readonly logoFileInput: string;

  private readonly metaTitleLangButton: string;

  private readonly metaTitleLangSpan: (lang: string) => string;

  private readonly metaTitleInput: (id: number) => string;

  private readonly metaDescriptionTextarea: (id: number) => string;

  private readonly statusToggleInput: (toggle: number) => string;

  private readonly saveButton: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on add supplier page
   */
  constructor() {
    super();

    this.pageTitle = `New supplier • ${global.INSTALL.SHOP_NAME}`;
    this.pageTitleEdit = 'Editing supplier';

    // Selectors
    this.nameInput = '#supplier_name';
    this.descriptionDiv = '#supplier_description';
    this.descriptionLangNavItemLink = (lang: string) => `${this.descriptionDiv} ul li a[data-locale='${lang}']`;
    this.descriptionIFrame = (id: number) => `#supplier_description_${id}_ifr`;
    this.homePhoneInput = '#supplier_phone';
    this.mobilePhoneInput = '#supplier_mobile_phone';
    this.addressInput = '#supplier_address';
    this.secondaryAddressInput = '#supplier_address2';
    this.postalCodeInput = '#supplier_post_code';
    this.cityInput = '#supplier_city';
    this.countryInput = '#supplier_id_country';
    this.selectCountryList = '#select2-supplier_id_country-container';
    this.searchCountryInput = '.select2-search__field';
    this.countrySearchResult = '#select2-supplier_id_country-results li.select2-results__option--highlighted';
    this.stateInput = '#supplier_id_state';
    this.logoFileInput = '#supplier_logo';
    this.metaTitleLangButton = '#supplier_meta_title_dropdown';
    this.metaTitleLangSpan = (lang: string) => 'div.dropdown-menu[aria-labelledby=\'supplier_meta_title_dropdown\']'
      + ` span[data-locale='${lang}']`;
    this.metaTitleInput = (id: number) => `#supplier_meta_title_${id}`;
    this.metaDescriptionTextarea = (id: number) => `#supplier_meta_description_${id}`;
    this.statusToggleInput = (toggle: number) => `#supplier_is_enabled_${toggle}`;

    this.saveButton = '.card-footer button';
  }

  /*
  Methods
   */

  /**
   * Create or edit Supplier
   * @param page {Page} Browser tab
   * @param supplierData {SupplierData} Data to set on new/edit supplier form
   * @return {Promise<string>}
   */
  async createEditSupplier(page: Page, supplierData: SupplierData): Promise<string> {
    // Fill Name
    await this.setValue(page, this.nameInput, supplierData.name);

    // Fill Address information
    await this.setValue(page, this.homePhoneInput, supplierData.homePhone);
    await this.setValue(page, this.mobilePhoneInput, supplierData.mobilePhone);
    await this.setValue(page, this.addressInput, supplierData.address);
    await this.setValue(page, this.secondaryAddressInput, supplierData.secondaryAddress);
    await this.setValue(page, this.postalCodeInput, supplierData.postalCode);
    await this.setValue(page, this.cityInput, supplierData.city);
    // Select country
    await page.click(this.selectCountryList);
    await this.setValue(page, this.searchCountryInput, supplierData.country);
    await this.waitForSelectorAndClick(page, this.countrySearchResult);

    // Add logo
    await this.uploadFile(page, this.logoFileInput, supplierData.logo);

    // Fill Description, meta title and meta description in english
    await this.changeLanguageForSelectors(page, 'en');
    await this.setValueOnTinymceInput(page, this.descriptionIFrame(1), supplierData.description);
    await this.setValue(page, this.metaTitleInput(1), supplierData.metaTitle);
    await this.setValue(page, this.metaDescriptionTextarea(1), supplierData.metaDescription);

    // Fill Description, meta title and meta description in french
    await this.changeLanguageForSelectors(page, 'fr');
    await this.setValueOnTinymceInput(page, this.descriptionIFrame(2), supplierData.descriptionFr);
    await this.setValue(page, this.metaTitleInput(2), supplierData.metaTitleFr);
    await this.setValue(page, this.metaDescriptionTextarea(2), supplierData.metaDescriptionFr);

    // Set status value
    await this.setChecked(page, this.statusToggleInput(supplierData.enabled ? 1 : 0));

    // Save Supplier
    await this.clickAndWaitForURL(page, this.saveButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Change language for description and meta selectors
   * @param page {Page} Browser tab
   * @param lang {string} To choose which language ('en' or 'fr')
   * @return {Promise<void>}
   */
  async changeLanguageForSelectors(page: Page, lang: string = 'en'): Promise<void> {
    // Change language for Description input
    await Promise.all([
      page.click(this.descriptionLangNavItemLink(lang)),
      this.waitForVisibleSelector(page, `${this.descriptionLangNavItemLink(lang)}.active`),
    ]);

    // Change language for meta selectors
    await Promise.all([
      page.click(this.metaTitleLangButton),
      this.waitForVisibleSelector(page, `${this.metaTitleLangButton}[aria-expanded='true']`),
    ]);
    await Promise.all([
      page.click(this.metaTitleLangSpan(lang)),
      this.waitForVisibleSelector(page, `${this.metaTitleLangButton}[aria-expanded='false']`),
    ]);
  }
}

export default new AddSupplier();
