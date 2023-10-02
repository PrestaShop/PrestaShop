// Import pages
import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * Options tab on new product V2 page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class OptionsTab extends BOBasePage {
  private readonly productVisibilityRadio: string;

  private readonly productAvailableForOrderRadio: (toEnable: number) => string;

  private readonly productShowPricesRadio: (toEnable: number) => string;

  private readonly productOnlineOnlyRadio: (toEnable: number) => string;

  private readonly productOptionVisibilityRadio: (option: number) => string;

  private readonly supplierAssociatedCheckBox: (row: number) => string;

  private readonly defaultSupplierSection: string;

  private readonly supplierReferencesSection: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on options tab
   */
  constructor() {
    super();

    // Selectors in options tab
    this.productOptionVisibilityRadio = (option: number) => `#product_options_visibility_visibility_${option}`;
    this.productVisibilityRadio = 'input[name="product[options][visibility][visibility]"]';
    this.productAvailableForOrderRadio = (toEnable: number) => `#product_options_visibility_available_for_order_${toEnable}`;
    this.productShowPricesRadio = (toEnable: number) => `#product_options_visibility_show_price_${toEnable}`;
    this.productOnlineOnlyRadio = (toEnable: number) => `#product_options_visibility_online_only_${toEnable}`;
    this.supplierAssociatedCheckBox = (row: number) => `#product_options_suppliers_supplier_ids div:nth-child(${row}) div`;
    this.defaultSupplierSection = '#product_options_suppliers_default_supplier_id';
    this.supplierReferencesSection = '#product_options_product_suppliers';
  }

  /*
  Methods
   */
  /**
   * Set visibility
   * @param page {page} Browser tab
   * @param visibility {string} Option to choose
   * @returns {Promise<void>}
   */
  async setVisibility(page: Page, visibility: string): Promise<void> {
    switch (visibility) {
      case 'everywhere':
        await this.setChecked(page, this.productOptionVisibilityRadio(0));
        break;
      case 'catalog_only':
        await this.setChecked(page, this.productOptionVisibilityRadio(1));
        break;
      case 'search_only':
        await this.setChecked(page, this.productOptionVisibilityRadio(2));
        break;
      case 'nowhere':
        await this.setChecked(page, this.productOptionVisibilityRadio(3));
        break;
      default:
        throw new Error(`Option ${visibility} was not found`);
    }
  }

  /**
   * Set available for order
   * @param page {page} Browser tab
   * @param toEnable {boolean} True if we need to enable available for order
   * @returns {Promise<void>}
   */
  async setAvailableForOrder(page: Page, toEnable: boolean): Promise<void> {
    await this.setChecked(page, this.productAvailableForOrderRadio(toEnable ? 1 : 0));
  }

  /**
   * Set show price
   * @param page {page} Browser tab
   * @param toEnable {boolean} True if we need to enable show price
   * @returns {Promise<void>}
   */
  async setShowPrice(page: Page, toEnable: boolean): Promise<void> {
    await this.setChecked(page, this.productShowPricesRadio(toEnable ? 1 : 0));
  }

  /**
   * Set web only
   * @param page {page} Browser tab
   * @param toEnable {boolean} True if we need to enable web only
   * @returns {Promise<void>}
   */
  async setWebOnly(page: Page, toEnable: boolean): Promise<void> {
    await this.setChecked(page, this.productOnlineOnlyRadio(toEnable ? 1 : 0));
  }

  /**
   * Choose supplier
   * @param page {page} Browser tab
   * @param supplierRow {number} Supplier to choose
   * @returns {Promise<void>}
   */
  async chooseSupplier(page: Page, supplierRow: number): Promise<void> {
    await this.waitForSelectorAndClick(page, this.supplierAssociatedCheckBox(supplierRow));
  }

  /**
   * Is default supplier section visible
   * @param page {page} Browser tab
   * @returns {Promise<boolean>}
   */
  async isDefaultSupplierSectionVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.defaultSupplierSection, 1000);
  }

  /**
   * Is supplier references section visible
   * @param page {page} Browser tab
   * @returns {Promise<boolean>}
   */
  async isSupplierReferencesSectionVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.supplierReferencesSection, 1000);
  }

  /**
   * Returns the value of a form element
   * @param page {Page}
   * @param inputName {string}
   */
  async getValue(page: Page, inputName: string): Promise<string> {
    switch (inputName) {
      case 'available_for_order':
        return (await this.isChecked(page, this.productAvailableForOrderRadio(1))) ? '1' : '0';
      case 'online_only':
        return (await this.isChecked(page, this.productShowPricesRadio(1))) ? '1' : '0';
      case 'show_price':
        return (await this.isChecked(page, this.productShowPricesRadio(1))) ? '1' : '0';
      case 'visibility':
        return this.getAttributeContent(page, `${this.productVisibilityRadio}[checked="checked"]`, 'value');
      default:
        throw new Error(`Input ${inputName} was not found`);
    }
  }
}

export default new OptionsTab();
