import LocalizationBasePage from '@pages/BO/international/localization/localizationBasePage';

import type CurrencyData from '@data/faker/currency';
import {CurrencyFormat} from '@data/types/currency';

import type {Page} from 'playwright';

/**
 * Add currency page, contains functions that can be used on the page
 * @class
 * @extends LocalizationBasePage
 */
class AddCurrency extends LocalizationBasePage {
  public readonly pageTitle: string;

  public readonly pageTitleEdit: (currency: string) => string;

  public readonly editCurrencyPage: string;

  public readonly resetCurrencyFormatMessage: string;

  private readonly currencySelect: string;

  private readonly alternativeCurrencyCheckBox: string;

  private readonly currencyNameInput: (id: number) => string;

  private readonly isoCodeInput: string;

  private readonly exchangeRateInput: string;

  private readonly precisionInput: string;

  private readonly statusToggleInput: (toggle: number) => string;

  private readonly saveButton: string;

  private readonly currencyLoadingModal: string;

  private readonly currencyFormatDiv: string;

  private readonly currencyFormatTable: string;

  private readonly currencyFormatRows: string;

  private readonly currencyFormatRow: (nthRow: number) => string;

  private readonly currencyFormatCell: (nthRow: number, nthColumn: number) => string;

  private readonly currencyFormatEdit: (nthRow: number) => string;

  private readonly currencyFormatReset: (nthRow: number) => string;

  private readonly currencyFormatEditModal: string;

  private readonly currencyFormatEditSymbolInput: string;

  private readonly currencyFormatEditFormatRadio: (format: string) => string;

  private readonly currencyFormatEditSubmit: string;

  private readonly restoreDefaultSettingsButton: string;

  private readonly restoreDefaultSettingsModal: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on add currency page
   */
  constructor() {
    super();

    this.pageTitle = `New currency • ${global.INSTALL.SHOP_NAME}`;
    this.pageTitleEdit = (currency: string) => `Editing currency ${currency} • ${global.INSTALL.SHOP_NAME}`;
    this.editCurrencyPage = 'Editing currency';
    this.resetCurrencyFormatMessage = 'Your symbol and format customizations have been successfully reset for this language.';

    // Selectors
    this.currencySelect = '#currency_selected_iso_code';
    this.alternativeCurrencyCheckBox = '#currency_unofficial';
    this.currencyNameInput = (id: number) => `#currency_names_${id}`;
    this.isoCodeInput = '#currency_iso_code';
    this.exchangeRateInput = '#currency_exchange_rate';
    this.precisionInput = '#currency_precision';
    this.statusToggleInput = (toggle: number) => `#currency_active_${toggle}`;
    this.saveButton = '#save-button';

    // currency modal
    this.currencyLoadingModal = '#currency_loading_data_modal';

    // Currency Format
    this.currencyFormatDiv = '#currency_formatter';
    this.currencyFormatTable = `${this.currencyFormatDiv} table`;
    this.currencyFormatRows = `${this.currencyFormatTable} tbody tr`;
    this.currencyFormatRow = (nthRow: number) => `${this.currencyFormatRows}:nth-child(${nthRow})`;
    this.currencyFormatCell = (nthRow: number, nthColumn:number) => `${this.currencyFormatRow(nthRow)} `
      + `td:nth-child(${nthColumn})`;
    this.currencyFormatEdit = (nthRow: number) => `${this.currencyFormatCell(nthRow, 3)} `
      + 'div.btn-group-action button';
    this.currencyFormatReset = (nthRow: number) => `${this.currencyFormatCell(nthRow, 4)} `
      + 'div.btn-group-action button';

    // Currency Format Modal
    this.currencyFormatEditModal = 'div[data-role="currency-format-edit-modal"] .modal';
    this.currencyFormatEditSymbolInput = `${this.currencyFormatEditModal} input[data-role="custom-symbol"]`;
    this.currencyFormatEditFormatRadio = (format: string) => `${this.currencyFormatEditModal} div#${format} label`;
    this.currencyFormatEditSubmit = `${this.currencyFormatEditModal} footer button.btn-primary`;

    this.restoreDefaultSettingsButton = '#currency_reset_default_settings';
    this.restoreDefaultSettingsModal = '#currency_loading_data_modal';
  }

  /*
  Methods
   */

  /**
   * Add official currency
   * @param page {Page} Browser tab
   * @param currencyData {CurrencyData} Data to set on add currency form
   * @returns {Promise<string>}, successful text message that appears
   */
  async addOfficialCurrency(page: Page, currencyData: CurrencyData): Promise<string> {
    // Select currency
    await page.selectOption(this.currencySelect, currencyData.isoCode);
    await this.waitForVisibleSelector(page, `${this.currencyLoadingModal}.show`);
    // Waiting for currency to be loaded : 10 sec max
    // To check if modal still exist
    let displayed = false;

    for (let i = 0; i < 50 && !displayed; i++) {
      /* eslint-env browser */
      displayed = await page.evaluate(
        (selector) => {
          const element: HTMLElement|null = document.querySelector(selector);

          if (element === null) {
            return false;
          }
          return window.getComputedStyle(element).getPropertyValue('display') === 'none';
        },
        this.currencyLoadingModal,
      );
      await page.waitForTimeout(200);
    }

    // Wait for input to have value
    let inputHasValue = false;

    for (let i = 0; i < 50 && !inputHasValue; i++) {
      /* eslint-env browser */
      inputHasValue = await page.evaluate(
        (selector) => {
          const element: HTMLInputElement|null = document.querySelector(selector);

          if (element === null) {
            return false;
          }

          return element.value !== '';
        },
        this.currencyNameInput(1),
      );

      await page.waitForTimeout(200);
    }

    await this.setChecked(page, this.statusToggleInput(currencyData.enabled ? 1 : 0));

    return this.saveCurrencyForm(page);
  }

  /**
   * Create unofficial currency
   * @param page {Page} Browser tab
   * @param currencyData {CurrencyData} Data to set on add currency form
   * @returns {Promise<string>}
   */
  async createUnOfficialCurrency(page: Page, currencyData: CurrencyData): Promise<string> {
    await this.setCheckedWithIcon(page, this.alternativeCurrencyCheckBox);
    await this.setValue(page, this.currencyNameInput(1), currencyData.name);
    await this.setValue(page, this.isoCodeInput, currencyData.isoCode);
    await this.setValue(page, this.exchangeRateInput, currencyData.exchangeRate.toString());
    await this.setChecked(page, this.statusToggleInput(currencyData.enabled ? 1 : 0));

    return this.saveCurrencyForm(page);
  }

  /**
   * Update exchange rate
   * @param page {Page} Browser tab
   * @param value {number} Value to set on exchange rate input
   * @returns {Promise<string>}
   */
  async updateExchangeRate(page: Page, value: number): Promise<string> {
    await this.setValue(page, this.exchangeRateInput, value.toString());

    return this.saveCurrencyForm(page);
  }

  /**
   * Set precision for a currency
   * @param page {Page} Browser tab
   * @param value {number} Value to set on exchange rate input
   * @return {Promise<string>}
   */
  async setCurrencyPrecision(page: Page, value: number = 2): Promise<string> {
    await this.setValue(page, this.precisionInput, value.toString());

    return this.saveCurrencyForm(page);
  }

  /**
   * Save the currency form
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async saveCurrencyForm(page: Page): Promise<string> {
    await this.clickAndWaitForURL(page, this.saveButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /* Table methods */
  /**
   * Get number of element in grid
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfElementInGrid(page: Page): Promise<number> {
    return (await page.$$(this.currencyFormatRows)).length;
  }

  /**
   * Get text from a column
   * @param page {Page} Browser tab
   * @param row {number} Row in table to get text column
   * @param column {number} Column to get text content
   * @return {Promise<string>}
   */
  async getTextColumnFromTable(page: Page, row: number, column: number): Promise<string> {
    return this.getTextContent(page, this.currencyFormatCell(row, column));
  }

  /**
   * Click on the edit and displays the modal
   * @param {Page} page
   * @param {number} row
   * @return {boolean} Return if the modal is visible
   */
  async editCurrencyFormat(page: Page, row: number): Promise<boolean> {
    await page.click(this.currencyFormatEdit(row));

    return this.elementVisible(page, this.currencyFormatEditModal, 2000);
  }

  /**
   * Click on the edit and displays the modal
   * @param {Page} page
   * @param {number} row
   * @return {string} Return the message of the grow
   */
  async resetCurrencyFormat(page: Page, row: number): Promise<string|null> {
    await page.click(this.currencyFormatReset(row));

    return this.getGrowlMessageContent(page);
  }

  /**
   * Set the symbol for the currency format
   * @param {Page} page
   * @param {string} symbol
   * @return {void}
   */
  async setCurrencyFormatSymbol(page: Page, symbol: string): Promise<void> {
    await this.waitForVisibleSelector(page, this.currencyFormatEditSymbolInput, 2000);
    await this.setValue(page, this.currencyFormatEditSymbolInput, symbol);
  }

  /**
   * Set the format for the currency format
   * @param {Page} page
   * @param {CurrencyFormat} format
   * @return {void}
   */
  async setCurrencyFormatFormat(page: Page, format: CurrencyFormat): Promise<void> {
    await page.click(this.currencyFormatEditFormatRadio(format));
  }

  /**
   * Save the currency format form
   * @param {Page} page
   * @return {void}
   */
  async saveCurrencyFormat(page: Page): Promise<void> {
    await page.click(this.currencyFormatEditSubmit);
  }

  /**
   * Restore default settings and return if restore modal is displayed
   * @param {Page} page
   */
  async restoreDefaultSettings(page: Page): Promise<boolean> {
    await page.click(this.restoreDefaultSettingsButton);

    const isVisible = this.elementVisible(page, `${this.restoreDefaultSettingsModal}.show`, 2000);

    await this.elementVisible(page, `${this.restoreDefaultSettingsButton}:not(.spinner)`, 2000);

    await this.elementNotVisible(page, `${this.restoreDefaultSettingsModal}.show`, 2000);

    return isVisible;
  }
}

export default new AddCurrency();
