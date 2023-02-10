// Import pages
import BOBasePage from '@pages/BO/BObasePage';

import type {Frame, Page} from 'playwright';
import {expect} from 'chai';
import {ProductFilterMinMax} from '@data/types/product';

/**
 * Products V2 page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class Products extends BOBasePage {
  public readonly pageTitle: string;

  public readonly alertDangerIDFilterValue: string;

  public readonly alertDangerPriceFilterValue: string;

  public readonly alertDangerQuantityFilterValue: string;

  public readonly standardProductDescription: string;

  public readonly productWithCombinationsDescription: string;

  public readonly virtualProductDescription: string;

  public readonly packOfProductsDescription: string;

  private readonly newProductButton: string;

  private readonly addNewProductButton: string;

  private readonly productGridPanel: string;

  private readonly productGridHeader: string;

  private readonly headerTitle: string;

  private readonly productGrid: string;

  private readonly filterByCategoryBlock: string;

  private readonly filterByCategoriesButton: string;

  private readonly filterByCategoriesExpandButton: string;

  private readonly filterByCategoriesUnselectButton: string;

  private readonly filterByCategoriesLabel: string;

  private readonly clearFilterButton: string;

  private readonly productBulkMenuButton: string;

  private readonly bulkActionsDropDownMenu: string;

  private readonly bulkActionsSelectionLink: (action: string) => string;

  private readonly modalBulkActionsProducts: (action: string) => string;

  private readonly modalBulkActionsProductsBody: (action: string) => string;

  private readonly modalBulkActionsProductsFooter: (action: string) => string;

  private readonly modalDialogBulkActionButton: (action: string) => string;

  private readonly modalBulkActionsProductsProgress: (action: string) => string;

  private readonly modalBulkActionsProductsProgressBody: (action: string) => string;

  private readonly modalBulkActionsProgressSuccessMessage: (action: string) => string;

  private readonly modalBulkActionsProductsProgressFooter: (action: string) => string;

  private readonly modalBulkActionsProductsProgressBarDone: string;

  private readonly modalBulkActionsProductsCloseButton: (action: string) => string;

  private readonly productGridTable: string;

  private readonly productTableFilterLine: string;

  private readonly filterSearchButton: string;

  private readonly filterResetButton: string;

  private readonly selectAllProductsCheckbox: string;

  private readonly productFilterIDMinInput: string;

  private readonly productFilterIDMaxInput: string;

  private readonly productFilterNameInput: string;

  private readonly productFilterReferenceInput: string;

  private readonly productFilterCategoryInput: string;

  private readonly productFilterPriceMinInput: string;

  private readonly productFilterPriceMaxInput: string;

  private readonly productFilterQuantityMinInput: string;

  private readonly productFilterQuantityMaxInput: string;

  private readonly productFilterSelectStatus: string;

  private readonly productFilterPositionInput: string;

  private readonly productRow: string;

  private readonly productEmptyRow: string;

  private readonly productsListTableRow: (row: number) => string;

  private readonly productsListTableColumnID: (row: number) => string;

  private readonly productsListTableColumnName: (row: number) => string;

  private readonly productsListTableColumnReference: (row: number) => string;

  private readonly productsListTableColumnCategory: (row: number) => string;

  private readonly productsListTableColumnPriceTExc: (row: number) => string;

  private readonly productsListTableColumnPriceATI: (row: number) => string;

  private readonly productsListTableColumnQuantity: (row: number) => string;

  private readonly productsListTableColumnStatus: (row: number) => string;

  private readonly productsListTableColumnPosition: (row: number) => string;

  private readonly productListTableDropDownList: (row: number) => string;

  private readonly productListTableDeleteButton: (row: number) => string;

  private readonly productListTableDuplicateButton: (row: number) => string;

  readonly modalCreateProduct: string;

  private readonly modalCreateProductLoader: string;

  private readonly productTypeChoices: string;

  private readonly productTypeDescription: string;

  private readonly productType: (type: string) => string;

  protected readonly modalDialog: string;

  private readonly modalDialogFooter: string;

  private readonly modalDialogConfirmButton: string;

  private readonly paginationBlock: string;

  private readonly productsNumberLabel: string;

  private readonly paginationNextLink: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on products V2 page
   */
  constructor() {
    super();

    this.pageTitle = 'Products';
    this.alertDangerIDFilterValue = 'ID: Maximum value must be higher than minimum value.';
    this.alertDangerPriceFilterValue = 'Price (tax excl.): Maximum value must be higher than minimum value.';
    this.alertDangerQuantityFilterValue = 'Quantity: Maximum value must be higher than minimum value.';

    this.standardProductDescription = 'A physical product that needs to be shipped.';
    this.productWithCombinationsDescription = 'A product with different variations (size, color, etc.) from which '
      + 'customers can choose.';
    this.virtualProductDescription = 'An intangible product that doesn\'t require shipping. You can also add a '
      + 'downloadable file.';
    this.packOfProductsDescription = 'A collection of products from your catalog.';

    // Header selectors
    this.newProductButton = '#page-header-desc-configuration-add';

    // Products page selectors
    this.addNewProductButton = '#create_product_create';
    this.productGridPanel = '#product_grid_panel';
    this.productGridHeader = `${this.productGridPanel} div.js-grid-header`;
    this.headerTitle = `${this.productGridHeader} .card-header-title`;
    this.productGrid = '#product_grid';

    // Filter by categories
    this.filterByCategoryBlock = `${this.productGrid} form.d-inline-block`;
    this.filterByCategoriesButton = `${this.filterByCategoryBlock} div button.dropdown-toggle`;
    this.filterByCategoriesExpandButton = `${this.filterByCategoryBlock} div button.category_tree_filter_expand`;
    this.filterByCategoriesUnselectButton = `${this.filterByCategoryBlock} div button.category_tree_filter_reset.btn`;
    this.filterByCategoriesLabel = '#category_filter ul.category-tree li div.category-label';
    this.clearFilterButton = `${this.filterByCategoryBlock} button.btn-link.category_tree_filter_reset`;

    // Bulk actions selectors
    this.productBulkMenuButton = `${this.productGridPanel} button.js-bulk-actions-btn`;
    this.bulkActionsDropDownMenu = 'div.dropdown-menu.show';
    this.bulkActionsSelectionLink = (action: string) => `#product_grid_bulk_action_${action}_ajax`;

    // Modal bulk actions products selectors
    this.modalBulkActionsProducts = (action: string) => `#product-ajax-${action}_ajax-confirm-modal`;
    this.modalBulkActionsProductsBody = (action: string) => `${this.modalBulkActionsProducts(action)} div.modal-body`;
    this.modalBulkActionsProductsFooter = (action: string) => `${this.modalBulkActionsProducts(action)} div.modal-footer`;
    this.modalDialogBulkActionButton = (action: string) => `${this.modalBulkActionsProductsFooter(action)} `
      + 'button.btn-confirm-submit';
    this.modalBulkActionsProductsProgress = (action: string) => `#product-ajax-${action}_ajax-progress-modal`;
    this.modalBulkActionsProductsProgressBody = (action: string) => `${this.modalBulkActionsProductsProgress(action)} `
      + 'div.modal-body';
    this.modalBulkActionsProgressSuccessMessage = (action: string) => `${this.modalBulkActionsProductsProgressBody(action)} `
      + 'div.progress-message';
    this.modalBulkActionsProductsProgressFooter = (action: string) => `${this.modalBulkActionsProductsProgress(action)} `
      + 'div.modal-footer';
    this.modalBulkActionsProductsProgressBarDone = '#modal_progressbar_done';
    this.modalBulkActionsProductsCloseButton = (action: string) => `${this.modalBulkActionsProductsProgressFooter(action)} `
      + 'button.close-modal-button';

    // Products table selectors
    this.productGridTable = '#product_grid_table';
    this.productTableFilterLine = `${this.productGridTable} tr.column-filters`;
    this.filterSearchButton = `${this.productTableFilterLine} button.grid-search-button`;
    this.filterResetButton = `${this.productTableFilterLine} button.js-reset-search`;
    this.selectAllProductsCheckbox = `${this.productTableFilterLine} td[data-type=bulk_action] div.md-checkbox`;

    // Filters input
    this.productFilterIDMinInput = '#product_id_product_min_field';
    this.productFilterIDMaxInput = '#product_id_product_max_field';
    this.productFilterNameInput = '#product_name';
    this.productFilterReferenceInput = '#product_reference';
    this.productFilterCategoryInput = '#product_category';
    this.productFilterPriceMinInput = '#product_final_price_tax_excluded_min_field';
    this.productFilterPriceMaxInput = '#product_final_price_tax_excluded_max_field';
    this.productFilterQuantityMinInput = '#product_quantity_min_field';
    this.productFilterQuantityMaxInput = '#product_quantity_max_field';
    this.productFilterSelectStatus = '#product_active';
    this.productFilterPositionInput = '#product_position';

    // Products list
    this.productRow = `${this.productGridTable} tbody tr`;
    this.productEmptyRow = `${this.productRow}.empty_row`;
    this.productsListTableRow = (row: number) => `${this.productRow}:nth-child(${row})`;
    this.productsListTableColumnID = (row: number) => `${this.productsListTableRow(row)} td.column-id_product`;
    this.productsListTableColumnName = (row: number) => `${this.productsListTableRow(row)} td.column-name a`;
    this.productsListTableColumnReference = (row: number) => `${this.productsListTableRow(row)} td.column-reference`;
    this.productsListTableColumnCategory = (row: number) => `${this.productsListTableRow(row)} td.column-category`;
    this.productsListTableColumnPriceTExc = (row: number) => `${this.productsListTableRow(row)} `
      + 'td.column-final_price_tax_excluded a';
    this.productsListTableColumnPriceATI = (row: number) => `${this.productsListTableRow(row)} `
      + 'td.column-price_tax_included';
    this.productsListTableColumnQuantity = (row: number) => `${this.productsListTableRow(row)} td.column-quantity a`;
    this.productsListTableColumnStatus = (row: number) => `${this.productsListTableRow(row)} td.column-active input`;
    this.productsListTableColumnPosition = (row: number) => `${this.productsListTableRow(row)} td.column-position`;
    this.productListTableDropDownList = (row: number) => `${this.productsListTableRow(row)} td.column-actions `
      + 'a.dropdown-toggle';
    this.productListTableDeleteButton = (row: number) => `${this.productsListTableRow(row)}`
      + ' td.column-actions a.grid-delete-row-link';
    this.productListTableDuplicateButton = (row: number) => `${this.productsListTableRow(row)}`
      + ' td.column-actions a.grid-duplicate-row-link';

    // Modal create product selectors
    this.modalCreateProduct = '#modal-create-product';
    this.modalCreateProductLoader = `${this.modalCreateProduct} div.modal-iframe-loader`;
    this.productTypeChoices = '#create_product div.product-type-choices';
    this.productTypeDescription = '#create_product div.product-type-description';
    this.productType = (type: string) => `${this.productTypeChoices} button.product-type-choice[data-value=${type}]`;

    // Modal dialog
    this.modalDialog = '#product-grid-confirm-modal .modal-dialog';
    this.modalDialogFooter = `${this.modalDialog} div.modal-footer`;
    this.modalDialogConfirmButton = `${this.modalDialogFooter} button.btn-confirm-submit`;

    // Pagination
    this.paginationBlock = `${this.productGridPanel} div.pagination-block`;
    this.productsNumberLabel = `${this.paginationBlock} label.col-form-label`;
    this.paginationNextLink = '.page-item.next:not(.disabled) [data-role=next-page-link]';
  }

  /*
  Methods
   */

  /**
   * Click on new product button
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async clickOnNewProductButton(page: Page): Promise<boolean> {
    await this.waitForSelectorAndClick(page, this.newProductButton);

    return this.elementVisible(page, this.modalCreateProduct, 1000);
  }

  /**
   * Get product type description
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getProductDescription(page: Page): Promise<string> {
    await this.waitForVisibleSelector(page, `${this.modalCreateProduct} iframe`);
    await this.waitForHiddenSelector(page, this.modalCreateProductLoader);

    const createProductFrame: Frame|null = await page.frame({url: /sell\/catalog\/products-v2\/create/gmi});
    await expect(createProductFrame).to.be.not.null;

    return this.getTextContent(createProductFrame!, this.productTypeDescription);
  }

  /**
   * Get alert danger block content
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getAlertDangerBlockContent(page: Page): Promise<string> {
    return this.getAlertDangerBlockParagraphContent(page);
  }

  /**
   * Choose product type
   * @param page {Page} Browser tab
   * @param productType {string} Product type to select
   * @returns {Promise<void>}
   */
  async selectProductType(page: Page, productType: string): Promise<void> {
    await this.waitForVisibleSelector(page, `${this.modalCreateProduct} iframe`);
    await this.waitForHiddenSelector(page, this.modalCreateProductLoader);

    const createProductFrame: Frame|null = await page.frame({url: /sell\/catalog\/products-v2\/create/gmi});
    await expect(createProductFrame).to.be.not.null;

    await this.waitForSelectorAndClick(createProductFrame!, this.productType(productType));
  }

  /**
   * Choose product type
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async clickOnAddNewProduct(page: Page): Promise<void> {
    const createProductFrame: Frame|null = await page.frame({url: /sell\/catalog\/products-v2\/create/gmi});
    await expect(createProductFrame).to.be.not.null;

    await this.waitForSelectorAndClick(createProductFrame!, this.addNewProductButton);
  }

  /**
   * Get number of products from header
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfProductsFromHeader(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.headerTitle);
  }

  /**
   * Is reset button visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  isResetButtonVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.filterResetButton, 1000);
  }

  /**
   * Go to product page
   * @param page {Page} Browser tab
   * @param row {number} Row in product table
   * @returns {Promise<void>}
   */
  async goToProductPage(page: Page, row: number = 1): Promise<void> {
    await this.waitForSelectorAndClick(page, this.productsListTableColumnName(row));
  }

  // Bulk delete products functions
  /**
   * Bulk select products
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async bulkSelectProducts(page: Page): Promise<boolean> {
    await this.waitForSelectorAndClick(page, this.selectAllProductsCheckbox);

    return this.elementNotVisible(page, `${this.productBulkMenuButton}[disabled]`, 1000);
  }

  /**
   * Click on bulk action products
   * @param page {Page} Browser tab
   * @param action {string} Enable/disable/duplicate or delete
   * @returns {Promise<string>}
   */
  async clickOnBulkActionsProducts(page: Page, action: string): Promise<string> {
    await Promise.all([
      await this.waitForSelectorAndClick(page, this.productBulkMenuButton),
      await this.waitForVisibleSelector(page, this.bulkActionsDropDownMenu),
    ]);

    const bulkActionsSelectionLink = this.bulkActionsSelectionLink(
      (action === 'enable' || action === 'disable') ? `${action}_selection` : `bulk_${action}`,
    );

    const modalBulkActionsProducts = this.modalBulkActionsProducts(
      (action === 'enable' || action === 'disable') ? `${action}_selection` : `bulk_${action}`,
    );
    const modalBulkActionsProductsBody = this.modalBulkActionsProductsBody(
      (action === 'enable' || action === 'disable') ? `${action}_selection` : `bulk_${action}`,
    );
    await this.waitForSelectorAndClick(page, bulkActionsSelectionLink);
    await this.waitForVisibleSelector(page, modalBulkActionsProducts);

    return this.getTextContent(page, modalBulkActionsProductsBody);
  }

  /**
   * Bulk actions products
   * @param page {Page} Browser tab
   * @param action {string} Enable/disable/duplicate or delete
   * @returns {Promise<string>}
   */
  async bulkActionsProduct(page: Page, action: string): Promise<string> {
    const modalDialogBulkActionButton = this.modalDialogBulkActionButton(
      (action === 'enable' || action === 'disable') ? `${action}_selection` : `bulk_${action}`,
    );
    const modalBulkActionsProgressSuccessMessage = this.modalBulkActionsProgressSuccessMessage(
      (action === 'enable' || action === 'disable') ? `${action}_selection` : `bulk_${action}`,
    );
    await this.waitForSelectorAndClick(page, modalDialogBulkActionButton);
    await this.waitForVisibleSelector(page, this.modalBulkActionsProductsProgressBarDone);

    return this.getTextContent(page, modalBulkActionsProgressSuccessMessage);
  }

  /**
   * Close bulk actions progress modal
   * @param page {Page} Browser tab
   * @param action {string} Enable/disable/duplicate or delete
   * @returns {Promise<boolean>}
   */
  async closeBulkActionsProgressModal(page: Page, action: string): Promise<boolean> {
    const modalBulkActionsProductsCloseButton = this.modalBulkActionsProductsCloseButton(
      (action === 'enable' || action === 'disable') ? `${action}_selection` : `bulk_${action}`,
    );
    const modalBulkActionsProductsProgress = this.modalBulkActionsProductsProgress(
      (action === 'enable' || action === 'disable') ? `${action}_selection` : `bulk_${action}`,
    );
    await this.clickAndWaitForNavigation(page, modalBulkActionsProductsCloseButton);

    return this.elementNotVisible(page, modalBulkActionsProductsProgress, 1000);
  }

  // Filter products table methods
  /**
   * Reset filter by category
   * @param page {Page} Browser tab
   * @param categoryName {string} Category name to filter by
   * @returns {Promise<void>}
   */
  async filterProductsByCategory(page: Page, categoryName: string = 'Home'): Promise<void> {
    // Click and wait to be open
    await page.click(this.filterByCategoriesButton);
    await this.waitForVisibleSelector(page, `${this.filterByCategoriesButton}[aria-expanded='true']`);

    // Click on expand button
    await page.click(this.filterByCategoriesExpandButton);

    // Choose category to filter with
    const args = {allCategoriesSelector: this.filterByCategoriesLabel, val: categoryName};
    // eslint-disable-next-line no-eval
    const fn = eval(`({
      async categoryClick(args) {
        /* eslint-env browser */
        const allCategories = [...await document.querySelectorAll(args.allCategoriesSelector)];
        const category = await allCategories.find((el) => el.textContent.includes(args.val));

        if (category === undefined) {
          return false;
        }
        await category.querySelector('input').click();
        return true;
      }
    })`);
    const found = await page.evaluate(fn.categoryClick, args);

    if (!found) {
      throw new Error(`${categoryName} not found as a category`);
    }
    await page.waitForNavigation({
      waitUntil: 'networkidle',
    });
  }

  /**
   * Get filter by categories button name
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getFilterByCategoryButtonName(page: Page): Promise<string> {
    return this.getTextContent(page, this.filterByCategoriesButton);
  }

  /**
   * Reset filter by category
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async resetFilterCategory(page: Page): Promise<void> {
    // Click and wait to be open
    await page.click(this.filterByCategoriesButton);
    await this.waitForVisibleSelector(page, `${this.filterByCategoriesButton}[aria-expanded='true']`);

    // Unselect all categories
    await this.clickAndWaitForNavigation(page, this.filterByCategoriesUnselectButton);
    await this.waitForVisibleSelector(page, `${this.filterByCategoriesButton}[aria-expanded='false']`);
  }

  /**
   * Is clear filter link visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  isClearFilterLinkVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.clearFilterButton, 2000);
  }

  /**
   * Click on clear filter link
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async clickOnClearFilterLink(page: Page): Promise<void> {
    await this.clickAndWaitForNavigation(page, this.clearFilterButton);
  }

  /**
   * Is position column visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async isPositionColumnVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.productFilterPositionInput);
  }

  /**
   * Filter products Min - Max
   * @param page {Page} Browser tab
   * @param idMin {number} Value of id min to set on filter input
   * @param idMax {number} Value of id max to set on filter input
   * @return {Promise<void>}
   */
  async filterProductsByID(page: Page, idMin: number, idMax: number): Promise<void> {
    await page.type(this.productFilterIDMinInput, idMin.toString());
    await page.type(this.productFilterIDMaxInput, idMax.toString());
  }

  /**
   * Filter Quantity Min - Max
   * @param page {Page} Browser tab
   * @param quantityMin {number} Value of quantity min to set on input
   * @param quantityMax {number} Value of quantity max to set on input
   * @return {Promise<void>}
   */
  async filterProductsByQuantity(page: Page, quantityMin: number, quantityMax: number): Promise<void> {
    await page.type(this.productFilterQuantityMinInput, quantityMin.toString());
    await page.type(this.productFilterQuantityMaxInput, quantityMax.toString());
  }

  /**
   * Filter price Min - Max
   * @param page {Page} Browser tab
   * @param priceMin {number} Value of min price to set on filter input
   * @param priceMax {number} Value of max price to set on filter input
   * @return {Promise<void>}
   */
  async filterProductsByPrice(page: Page, priceMin: number, priceMax: number): Promise<void> {
    await page.type(this.productFilterPriceMinInput, priceMin.toString());
    await page.type(this.productFilterPriceMaxInput, priceMax.toString());
  }

  /**
   * Filter products
   * @param page {Page} Browser tab
   * @param filterBy {string} Column to filter
   * @param value {string|ProductFilterMinMax} Value to put on filter
   * @param filterType {string} Input or select to choose method of filter
   * @return {Promise<void>}
   */
  async filterProducts(page: Page, filterBy: string, value: string | ProductFilterMinMax = '', filterType: string = 'input')
    : Promise<void> {
    switch (filterType) {
      case 'input':
        if (typeof value === 'string') {
          switch (filterBy) {
            case 'product_name':
              await page.type(this.productFilterNameInput, value);
              break;
            case 'reference':
              await page.type(this.productFilterReferenceInput, value);
              break;
            case 'category':
              await page.type(this.productFilterCategoryInput, value);
              break;
            case 'position':
              await this.setValue(page, this.productFilterPositionInput, value);
              break;
            default:
          }
        } else {
          switch (filterBy) {
            case 'id_product':
              await this.filterProductsByID(page, value.min, value.max);
              break;
            case 'price':
              await this.filterProductsByPrice(page, value.min, value.max);
              break;
            case 'quantity':
              await this.filterProductsByQuantity(page, value.min, value.max);
              break;
            default:
          }
        }
        break;
      case 'select':
        await this.selectByVisibleText(page, this.productFilterSelectStatus, typeof value === 'string' ? value : 'No');
        break;
      default:
      // Do nothing
    }
    // click on search
    await this.clickAndWaitForNavigation(page, this.filterSearchButton);
  }

  /**
   * Reset input filters
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async resetFilter(page: Page): Promise<void> {
    if (!(await this.elementNotVisible(page, this.filterResetButton, 2000))) {
      await this.clickAndWaitForNavigation(page, this.filterResetButton);
    }
  }

  /**
   * Reset filter and get number of elements in list
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async resetAndGetNumberOfLines(page: Page): Promise<number> {
    await this.resetFilter(page);

    return this.getNumberOfProductsFromList(page);
  }

  /**
   * Get number of products displayed in list
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfProductsFromList(page: Page): Promise<number> {
    const found = await this.elementVisible(page, this.paginationNextLink, 1000);

    // In case we filter products and there is only one page, link next from pagination does not appear
    if (!found) {
      return (await page.$$(this.productRow)).length;
    }

    const footerText = await this.getTextContent(page, this.productsNumberLabel);
    const resultFooterMatch: RegExpMatchArray|null = footerText.match(/out of ([0-9]+)/);

    if (resultFooterMatch === null) {
      return 0;
    }
    const resultExecArray: RegExpExecArray|null = /\d+/g.exec(resultFooterMatch.toString());

    if (resultExecArray === null) {
      return 0;
    }

    return parseInt(resultExecArray.toString(), 10);
  }

  /**
   * Get Product Price
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param withTaxes {boolean} True if we need to get product price with tax, false if not
   * @returns {Promise<number>}
   */
  async getProductPriceFromList(page: Page, row: number, withTaxes: boolean): Promise<number> {
    const selector = withTaxes ? this.productsListTableColumnPriceATI : this.productsListTableColumnPriceTExc;
    const text = await this.getTextContent(page, selector(row));
    const resultExecArray: RegExpExecArray|null = /\d+/g.exec(text);

    if (resultExecArray === null) {
      return 0;
    }

    return parseFloat(resultExecArray.toString());
  }

  /**
   * Get Product Status
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<boolean>}
   */
  async getProductStatusFromList(page: Page, row: number): Promise<boolean> {
    const inputValue = await this.getAttributeContent(
      page,
      `${this.productsListTableColumnStatus(row)}[checked]`,
      'value',
    );

    return inputValue !== '0';
  }

  /**
   * Get text column
   * @param page {Page} Browser tab
   * @param columnName {string} Column name to get text content
   * @param row {number} Row on table
   * @returns {Promise<string|number|boolean>}
   */
  async getTextColumn(page: Page, columnName: string, row: number = 1): Promise<string | number | boolean> {
    switch (columnName) {
      case 'id_product':
        return this.getNumberFromText(page, this.productsListTableColumnID(row));
      case 'product_name':
        return this.getTextContent(page, this.productsListTableColumnName(row));
      case 'reference':
        return this.getTextContent(page, this.productsListTableColumnReference(row));
      case 'category':
        return this.getTextContent(page, this.productsListTableColumnCategory(row));
      case 'price':
        return this.getProductPriceFromList(page, row, false);
      case 'quantity':
        return this.getNumberFromText(page, this.productsListTableColumnQuantity(row));
      case 'active':
        return this.getProductStatusFromList(page, row);
      case 'position':
        return this.getNumberFromText(page, this.productsListTableColumnPosition(row));
      default:
      // Do nothing
    }
    throw new Error(`${columnName} was not found as column`);
  }

  /**
   * Get text for empty table
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getTextForEmptyTable(page: Page): Promise<string> {
    return this.getTextContent(page, this.productEmptyRow);
  }

  /**
   * Click on duplicate product button
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<boolean>}
   */
  async clickOnDeleteProductButton(page: Page, row: number = 1): Promise<boolean> {
    await this.waitForSelectorAndClick(page, this.productListTableDropDownList(row));
    await this.waitForSelectorAndClick(page, this.productListTableDeleteButton(row));

    return this.elementVisible(page, this.modalDialog, 1000);
  }

  /**
   * Click on delete product button
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<boolean>}
   */
  async clickOnDuplicateProductButton(page: Page, row: number = 1): Promise<boolean> {
    await this.waitForSelectorAndClick(page, this.productListTableDropDownList(row));
    await this.waitForSelectorAndClick(page, this.productListTableDuplicateButton(row));

    return this.elementVisible(page, this.modalDialog, 1000);
  }

  /**
   * Confirm dialog product
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async clickOnConfirmDialogButton(page: Page): Promise<string> {
    await this.waitForSelectorAndClick(page, this.modalDialogConfirmButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

export default new Products();
