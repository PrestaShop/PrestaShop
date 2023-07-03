import BOBasePage from '@pages/BO/BObasePage';

import type ProductData from '@data/faker/product';
import type {ProductFilterMinMax} from '@data/types/product';

import type {Page} from 'playwright';

/**
 * Products page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class Products extends BOBasePage {
  public readonly pageTitle: string;

  public readonly productDeletedSuccessfulMessage: string;

  public readonly productMultiDeletedSuccessfulMessage: string;

  public readonly productDeactivatedSuccessfulMessage: string;

  public readonly productActivatedSuccessfulMessage: string;

  public readonly productMultiActivatedSuccessfulMessage: string;

  public readonly productMultiDeactivatedSuccessfulMessage: string;

  public readonly productMultiDuplicatedSuccessfulMessage: string;

  private readonly productListForm: string;

  private readonly productTable: string;

  private readonly productRow: string;

  private readonly productListfooterRow: string;

  private readonly productNumberBloc: string;

  private readonly dropdownToggleButton: (row: number) => string;

  private readonly dropdownMenu: (row: number) => string;

  private readonly dropdownMenuDeleteLink: (row: number) => string;

  private readonly dropdownMenuPreviewLink: (row: number) => string;

  private readonly dropdownMenuDuplicateLink: (row: number) => string;

  private readonly productRowEditLink: (row: number) => string;

  private readonly selectAllBulkCheckboxLabel: string;

  private readonly productBulkMenuButton: string;

  private readonly productBulkMenuButtonState: (state: string) => string;

  private readonly productBulkDropdownMenu: string;

  private readonly productBulkDeleteLink: string;

  private readonly productBulkEnableLink: string;

  private readonly productBulkDisableLink: string;

  private readonly productBulkDuplicateLink: string;

  private readonly productFilterIDMinInput: string;

  private readonly productFilterIDMaxInput: string;

  private readonly productFilterInput: (filterBy: string) => string;

  private readonly productFilterSelect: (filterBy: string) => string;

  private readonly productFilterPriceMinInput: string;

  private readonly productFilterPriceMaxInput: string;

  private readonly productFilterQuantityMinInput: string;

  private readonly productFilterQuantityMaxInput: string;

  private readonly filterSearchButton: string;

  private readonly filterResetButton: string;

  private readonly productsListTableRow: (row: number) => string;

  private readonly productsListTableColumnID: (row: number) => string;

  private readonly productsListTableColumnName: (row: number) => string;

  private readonly productsListTableColumnReference: (row: number) => string;

  private readonly productsListTableColumnCategory: (row: number) => string;

  private readonly productsListTableColumnPrice: (row: number) => string;

  private readonly productsListTableColumnPriceATI: (row: number) => string;

  private readonly productsListTableColumnQuantity: (row: number) => string;

  private readonly productsListTableColumnStatus: (row: number) => string;

  private readonly productsListTableColumnStatusInput: (row: number) => string;

  private readonly treeCategoriesBloc: string;

  private readonly filterByCategoriesButton: string;

  private readonly filterByCategoriesExpandButton: string;

  private readonly filterByCategoriesUnselectButton: string;

  private readonly filterByCategoriesCategoryLabel: string;

  private readonly filterByCategoriesResetButton: string;

  private readonly addProductButton: string;

  private readonly catalogDeletionModalDialog: string;

  private readonly modalDialogDeleteNowButton: string;

  private readonly tableHead: string;

  private readonly sortColumnDiv: (column: string) => string;

  private readonly sortColumnSpanButton: (column: string) => string;

  private readonly paginationLimitSelect: string;

  private readonly paginationLabel: string;

  private readonly paginationNextLink: string;

  private readonly paginationPreviousLink: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on products page
   */
  constructor() {
    super();

    this.pageTitle = 'Products •';
    this.productDeletedSuccessfulMessage = 'Product successfully deleted.';
    this.productMultiDeletedSuccessfulMessage = 'Product(s) successfully deleted.';
    this.productDeactivatedSuccessfulMessage = 'Product successfully deactivated.';
    this.productActivatedSuccessfulMessage = 'Product successfully activated.';
    this.productMultiActivatedSuccessfulMessage = 'Product(s) successfully activated.';
    this.productMultiDeactivatedSuccessfulMessage = 'Product(s) successfully deactivated.';
    this.productMultiDuplicatedSuccessfulMessage = 'Product(s) successfully duplicated.';

    // Selectors
    // List of products
    this.productListForm = '#product_catalog_list';
    this.productTable = `${this.productListForm} table`;
    this.productRow = `${this.productTable} tbody tr`;
    this.productListfooterRow = `${this.productListForm} div.pagination-block`;
    this.productNumberBloc = `${this.productListfooterRow} label.col-form-label`;
    this.dropdownToggleButton = (row: number) => `${this.productRow}:nth-of-type(${row}) button.dropdown-toggle`;
    this.dropdownMenu = (row: number) => `${this.productRow}:nth-of-type(${row}) div.dropdown-menu`;
    this.dropdownMenuDeleteLink = (row: number) => `${this.dropdownMenu(row)} a.product-edit[onclick*='delete']`;
    this.dropdownMenuPreviewLink = (row: number) => `${this.dropdownMenu(row)} a.product-edit:not([onclick])`;
    this.dropdownMenuDuplicateLink = (row: number) => `${this.dropdownMenu(row)} a.product-edit[onclick*='duplicate']`;
    this.productRowEditLink = (row: number) => `${this.productRow}:nth-of-type(${row}) a.tooltip-link.product-edit`;
    this.selectAllBulkCheckboxLabel = `${this.productListForm} .column-filters .md-checkbox label`;
    this.productBulkMenuButton = '#product_bulk_menu:not([disabled])';
    this.productBulkMenuButtonState = (state: string) => `${this.productBulkMenuButton}[aria-expanded='${state}']`;
    this.productBulkDropdownMenu = 'div.bulk-catalog div.dropdown-menu.show';
    this.productBulkDeleteLink = `${this.productBulkDropdownMenu} a[onclick*='delete_all']`;
    this.productBulkEnableLink = `${this.productBulkDropdownMenu} a[onclick*='activate_all']`;
    this.productBulkDisableLink = `${this.productBulkDropdownMenu} a[onclick*='deactivate_all']`;
    this.productBulkDuplicateLink = `${this.productBulkDropdownMenu} a[onclick*='duplicate_all']`;

    // Filters input
    this.productFilterIDMinInput = `${this.productListForm} #filter_column_id_product_min`;
    this.productFilterIDMaxInput = `${this.productListForm} #filter_column_id_product_max`;
    this.productFilterInput = (filterBy: string) => `${this.productListForm} input[name='filter_column_${filterBy}']`;
    this.productFilterSelect = (filterBy: string) => `${this.productListForm} select[name='filter_column_${filterBy}']`;
    this.productFilterPriceMinInput = `${this.productListForm} #filter_column_price_min`;
    this.productFilterPriceMaxInput = `${this.productListForm} #filter_column_price_max`;
    this.productFilterQuantityMinInput = `${this.productListForm} #filter_column_sav_quantity_min`;
    this.productFilterQuantityMaxInput = `${this.productListForm} #filter_column_sav_quantity_max`;
    this.filterSearchButton = `${this.productListForm} button[name='products_filter_submit']`;
    this.filterResetButton = `${this.productListForm} button[name='products_filter_reset']`;

    // Products list
    this.productsListTableRow = (row: number) => `${this.productRow}:nth-child(${row})`;
    this.productsListTableColumnID = (row: number) => `${this.productsListTableRow(row)}[data-product-id]`;
    this.productsListTableColumnName = (row: number) => `${this.productsListTableRow(row)} td:nth-child(4) a`;
    this.productsListTableColumnReference = (row: number) => `${this.productsListTableRow(row)} td:nth-child(5)`;
    this.productsListTableColumnCategory = (row: number) => `${this.productsListTableRow(row)} td:nth-child(6)`;
    this.productsListTableColumnPrice = (row: number) => `${this.productsListTableRow(row)} td:nth-child(7)`;
    this.productsListTableColumnPriceATI = (row: number) => `${this.productsListTableRow(row)} td:nth-child(8)`;
    this.productsListTableColumnQuantity = (row: number) => `${this.productsListTableRow(row)} td.product-sav-quantity`;
    this.productsListTableColumnStatus = (row: number) => `${this.productsListTableRow(row)} td:nth-child(10) .ps-switch`;
    this.productsListTableColumnStatusInput = (row: number) => `${this.productsListTableColumnStatus(row)} input`;

    // Filter Category
    this.treeCategoriesBloc = '#tree-categories';
    this.filterByCategoriesButton = '#product_catalog_category_tree_filter button';
    this.filterByCategoriesExpandButton = `${this.treeCategoriesBloc} a#product_catalog_category_tree_filter_expand`;
    this.filterByCategoriesUnselectButton = `${this.treeCategoriesBloc} a#product_catalog_category_tree_filter_reset`;
    this.filterByCategoriesCategoryLabel = `${this.treeCategoriesBloc} label.category-label`;
    this.filterByCategoriesResetButton = `${this.treeCategoriesBloc} button[type="reset"][name="categories_filter_reset"]`;

    // HEADER buttons
    this.addProductButton = '#page-header-desc-configuration-add';

    // Modal Dialog
    this.catalogDeletionModalDialog = '#catalog_deletion_modal div.modal-dialog';
    this.modalDialogDeleteNowButton = `${this.catalogDeletionModalDialog} button[value='confirm']`;

    // Sort Selectors
    this.tableHead = `${this.productTable} thead`;
    this.sortColumnDiv = (column: string) => `${this.tableHead} div.ps-sortable-column[data-sort-col-name='${column}']`;
    this.sortColumnSpanButton = (column: string) => `${this.sortColumnDiv(column)} span.ps-sort`;

    // Pagination selectors
    this.paginationLimitSelect = '#paginator_select_page_limit';
    this.paginationLabel = `${this.productListForm} .col-form-label`;
    this.paginationNextLink = `${this.productListForm} [data-role=next-page-link]`;
    this.paginationPreviousLink = `${this.productListForm} [data-role='previous-page-link']`;
  }

  /*
  Methods
   */
  /**
   * Filter products Min - Max
   * @param page {Page} Browser tab
   * @param idMin {number} Value of id min to set on filter input
   * @param idMax {number} Value of id max to set on filter input
   * @return {Promise<void>}
   */
  async filterIDProducts(page: Page, idMin: number, idMax: number): Promise<void> {
    await page.type(this.productFilterIDMinInput, idMin.toString());
    await page.type(this.productFilterIDMaxInput, idMax.toString());
    await page.click(this.filterSearchButton);
    await this.elementVisible(page, this.filterResetButton, 2000);
  }

  /**
   * Get Product ID
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<string>}
   */
  async getProductIDFromList(page: Page, row: number): Promise<number> {
    return this.getNumberFromText(page, this.productsListTableColumnID(row));
  }

  /**
   * Get Product Name
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<string>}
   */
  async getProductNameFromList(page: Page, row: number): Promise<string> {
    return this.getTextContent(page, this.productsListTableColumnName(row));
  }

  /**
   * Get Product Reference
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<string>}
   */
  async getProductReferenceFromList(page: Page, row: number): Promise<string> {
    return this.getTextContent(page, this.productsListTableColumnReference(row));
  }

  /**
   * Get Product Category
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<string>}
   */
  async getProductCategoryFromList(page: Page, row: number): Promise<string> {
    return this.getTextContent(page, this.productsListTableColumnCategory(row));
  }

  /**
   * Filter price Min - Max
   * @param page {Page} Browser tab
   * @param priceMin {number} Value of min price to set on filter input
   * @param priceMax {number} Value of max price to set on filter input
   * @return {Promise<void>}
   */
  async filterPriceProducts(page: Page, priceMin: number, priceMax: number): Promise<void> {
    await page.type(this.productFilterPriceMinInput, priceMin.toString());
    await page.type(this.productFilterPriceMaxInput, priceMax.toString());
    await page.click(this.filterSearchButton);
    await this.elementVisible(page, this.filterResetButton, 2000);
  }

  /**
   * Get Product Price
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param withTaxes {boolean} True if we need to get product price with tax, false if not
   * @returns {Promise<number>}
   */
  async getProductPriceFromList(page: Page, row: number, withTaxes: boolean): Promise<number> {
    const selector = withTaxes ? this.productsListTableColumnPriceATI : this.productsListTableColumnPrice;
    const text = await this.getTextContent(page, selector(row));
    const priceRegex: RegExpMatchArray | null = /\d+(\.\d+)?/g.exec(text);
    const price: string = priceRegex === null ? '0' : priceRegex.toString();

    return parseFloat(price);
  }

  /**
   * Filter Quantity Min - Max
   * @param page {Page} Browser tab
   * @param quantityMin {number} Value of quantity min to set on input
   * @param quantityMax {number} Value of quantity max to set on input
   * @return {Promise<void>}
   */
  async filterQuantityProducts(page: Page, quantityMin: number, quantityMax: number): Promise<void> {
    await page.type(this.productFilterQuantityMinInput, quantityMin.toString());
    await page.type(this.productFilterQuantityMaxInput, quantityMax.toString());
    await page.click(this.filterSearchButton);
    await this.elementVisible(page, this.filterResetButton, 2000);
  }

  /**
   * Get Product Quantity
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<number>}
   */
  async getProductQuantityFromList(page: Page, row: number): Promise<number> {
    return this.getNumberFromText(page, this.productsListTableColumnQuantity(row));
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
      `${this.productsListTableColumnStatusInput(row)}[checked]`,
      'value',
    );

    return inputValue !== '0';
  }

  /**
   * Filter products
   * @param page {Page} Browser tab
   * @param filterBy {string} Column to filter
   * @param value {ProductFilterMinMax|string} Value to put on filter
   * @param filterType {string} Input or select to choose method of filter
   * @return {Promise<void>}
   */
  async filterProducts(page: Page, filterBy: string, value: ProductFilterMinMax|string = '', filterType: string = 'input') {
    switch (filterType) {
      case 'input':
        if (typeof value === 'string') {
          await page.type(this.productFilterInput(filterBy), value);
        } else {
          switch (filterBy) {
            case 'id_product':
              await this.filterIDProducts(page, value.min, value.max);
              break;
            case 'price':
              await this.filterPriceProducts(page, value.min, value.max);
              break;
            case 'sav_quantity':
              await this.filterQuantityProducts(page, value.min, value.max);
              break;
            default:
          }
        }
        break;
      case 'select':
        await this.selectByVisibleText(
          page,
          this.productFilterSelect(filterBy),
          value ? 'Active' : 'Inactive',
        );
        break;
      default:
      // Do nothing
    }
    // click on search
    await this.clickAndWaitForLoadState(page, this.filterSearchButton);
  }

  /**
   * Get text column
   * @param page {Page} Browser tab
   * @param columnName {string} Column name to get text content
   * @param row {number} Row on table
   * @returns {Promise<string|number>}
   */
  async getTextColumn(page: Page, columnName: string, row: number): Promise<string> {
    switch (columnName) {
      case 'id_product':
        return (await this.getProductIDFromList(page, row)).toString();
      case 'name':
        return this.getProductNameFromList(page, row);
      case 'reference':
        return this.getProductReferenceFromList(page, row);
      case 'name_category':
        return this.getProductCategoryFromList(page, row);
      case 'price':
        return (await this.getProductPriceFromList(page, row, false)).toString();
      case 'sav_quantity':
        return (await this.getProductQuantityFromList(page, row)).toString();
      case 'active':
        return (await this.getProductStatusFromList(page, row)).toString();
      default:
      // Do nothing
    }
    throw new Error(`${columnName} was not found as column`);
  }

  /**
   * Get content from all rows
   * @param page {Page} Browser tab
   * @param column {string} Column name to get all rows text content
   * @return {Promise<Array<string>>}
   */
  async getAllRowsColumnContent(page: Page, column: string): Promise<string[]> {
    const rowsNumber = await this.getNumberOfProductsFromList(page);
    const allRowsContentTable: string[] = [];

    for (let i: number = 1; i <= rowsNumber; i++) {
      const rowContent = await this.getTextColumn(page, column, i);
      allRowsContentTable.push(rowContent);
    }

    return allRowsContentTable;
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

    const footerText = await this.getTextContent(page, this.productNumberBloc);
    const regexMatch: RegExpMatchArray|null = footerText.match(/out of ([0-9]+)/);

    if (regexMatch === null) {
      return 0;
    }
    const regexResult: RegExpExecArray|null = /\d+/g.exec(regexMatch.toString());

    if (regexResult === null) {
      return 0;
    }

    return parseInt(regexResult.toString(), 10);
  }

  /**
   * Get number of products displayed on the page
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfProductsOnPage(page: Page): Promise<number> {
    return (await page.$$(this.productRow)).length;
  }

  /**
   * Reset input filters
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async resetFilter(page: Page): Promise<void> {
    if (!(await this.elementNotVisible(page, this.filterResetButton, 2000))) {
      await this.clickAndWaitForLoadState(page, this.filterResetButton);
    }
    await this.waitForVisibleSelector(page, this.filterSearchButton, 2000);
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
   * Filter by Category from Dropdown
   * @param page {Page} Browser tab
   * @param categoryName {string} Value of category name to set on filter input
   * @return {Promise<void>}
   */
  async filterProductsByCategory(page: Page, categoryName: string = 'home'): Promise<void> {
    // Click and wait to be open
    await page.click(this.filterByCategoriesButton);
    await this.waitForVisibleSelector(page, `${this.filterByCategoriesButton}[aria-expanded='true']`);

    // Click on expand button
    await page.click(this.filterByCategoriesExpandButton);

    // Choose category to filter with
    const args = {allCategoriesSelector: this.filterByCategoriesCategoryLabel, val: categoryName};
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
    await this.elementVisible(page, this.filterByCategoriesResetButton, 2000);
  }

  /**
   * Reset dropDown Filter Category
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async resetFilterCategory(page: Page): Promise<void> {
    // Click and wait to be open
    await page.click(this.filterByCategoriesButton);
    await this.waitForVisibleSelector(page, `${this.filterByCategoriesButton}[aria-expanded='true']`);

    // Unselect all categories
    await this.clickAndWaitForURL(page, this.filterByCategoriesUnselectButton);
    await this.waitForVisibleSelector(page, `${this.filterByCategoriesButton}[aria-expanded='false']`);
  }

  /**
   * Go to form Add Product
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToAddProductPage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.addProductButton);
  }

  /**
   * GOTO edit product page from row
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<void>}
   */
  async goToEditProductPage(page: Page, row: number): Promise<void> {
    await this.clickAndWaitForURL(page, this.productRowEditLink(row));
  }

  /**
   * Open row dropdown for a product
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<void>}
   */
  async openProductDropdown(page: Page, row: number): Promise<void> {
    await Promise.all([
      this.waitForVisibleSelector(page, `${this.dropdownToggleButton(row)}[aria-expanded='true']`),
      page.click(this.dropdownToggleButton(row)),
    ]);
  }

  /**
   * Preview product from list
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<Page>}
   */
  async previewProduct(page: Page, row: number): Promise<Page> {
    // Open dropdown
    await this.openProductDropdown(page, row);

    // Open product in a new tab
    return this.openLinkWithTargetBlank(page, this.dropdownMenuPreviewLink(row));
  }

  /**
   * Duplicate product
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<string>}
   */
  async duplicateProduct(page: Page, row: number): Promise<string> {
    // Open dropdown
    await this.openProductDropdown(page, row);

    // Duplicate product and go to add product page
    await this.clickAndWaitForURL(page, this.dropdownMenuDuplicateLink(row));

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Delete product with dropdown Menu
   * @param page {Page} Browser tab
   * @param productData {ProductData} Data to set to filter product
   * @returns {Promise<string>}
   */
  async deleteProduct(page: Page, productData: ProductData): Promise<string> {
    // Filter By reference first
    await this.filterProducts(page, 'reference', productData.reference);

    // Then delete first product and only product shown
    await Promise.all([
      this.waitForVisibleSelector(page, `${this.dropdownToggleButton(1)}[aria-expanded='true']`),
      page.click(this.dropdownToggleButton(1)),
    ]);

    await Promise.all([
      this.waitForVisibleSelector(page, this.catalogDeletionModalDialog),
      page.click(this.dropdownMenuDeleteLink(1)),
    ]);

    await this.clickAndWaitForURL(page, this.modalDialogDeleteNowButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Select all products
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async selectAllProducts(page: Page): Promise<void> {
    await this.waitForSelector(page, this.selectAllBulkCheckboxLabel, 'attached');
    await page.$eval(this.selectAllBulkCheckboxLabel, (el: HTMLElement) => el.click());
  }

  /**
   * Delete all products with Bulk Actions
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async deleteAllProductsWithBulkActions(page: Page): Promise<string> {
    await this.selectAllProducts(page);

    await Promise.all([
      this.waitForVisibleSelector(page, this.productBulkMenuButtonState('true')),
      page.click(this.productBulkMenuButton),
    ]);

    await Promise.all([
      this.waitForVisibleSelector(page, this.catalogDeletionModalDialog),
      page.click(this.productBulkDeleteLink),
    ]);

    await this.clickAndWaitForURL(page, this.modalDialogDeleteNowButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Duplicate all products with Bulk Actions
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async duplicateAllProductsWithBulkActions(page: Page): Promise<string> {
    await this.selectAllProducts(page);

    await Promise.all([
      this.waitForVisibleSelector(page, this.productBulkMenuButtonState('true')),
      page.click(this.productBulkMenuButton),
    ]);

    await this.clickAndWaitForURL(page, this.productBulkDuplicateLink);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Bulk set status
   * @param page {Page} Browser tab
   * @param status {boolean} True if we need to bulk enable status, false if not
   * @return {Promise<string>}
   */
  async bulkSetStatus(page: Page, status: boolean): Promise<string> {
    await this.closeAlertBlock(page);
    await this.selectAllProducts(page);

    await Promise.all([
      this.waitForVisibleSelector(page, this.productBulkMenuButtonState('true')),
      page.click(this.productBulkMenuButton),
    ]);

    await this.clickAndWaitForURL(page, status ? this.productBulkEnableLink : this.productBulkDisableLink);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Quick edit toggle column value
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param valueWanted {boolean} True if we want to enable product status, false if not
   * @return {Promise<boolean>} return true if action is done, false otherwise
   */
  async setProductStatus(page: Page, row: number, valueWanted: boolean = true): Promise<boolean> {
    const actualValue = await this.getProductStatusFromList(page, row);

    if (actualValue !== valueWanted) {
      await page.click(this.productsListTableColumnStatus(row));
      return true;
    }

    return false;
  }

  /**
   * Go to product page
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<void>}
   */
  async goToProductPage(page: Page, row: number = 1): Promise<void> {
    await this.waitForVisibleSelector(page, this.productsListTableColumnName(row));
    await this.clickAndWaitForURL(page, this.productsListTableColumnName(row));
  }

  /* Sort methods */
  /**
   * Sort table by clicking on column name
   * @param page {Page} Browser tab
   * @param sortBy {string} Column to sort with
   * @param sortDirection {string} Sort direction asc or desc
   * @return {Promise<void>}
   */
  async sortTable(page: Page, sortBy: string, sortDirection: string = 'asc'): Promise<void> {
    const sortColumnDiv = `${this.sortColumnDiv(sortBy)}[data-sort-direction='${sortDirection}']`;
    const sortColumnSpanButton = this.sortColumnSpanButton(sortBy);

    let i: number = 0;
    while (await this.elementNotVisible(page, sortColumnDiv, 2000) && i < 2) {
      await page.hover(this.sortColumnDiv(sortBy));
      await this.clickAndWaitForURL(page, sortColumnSpanButton);
      i += 1;
    }

    await this.waitForVisibleSelector(page, sortColumnDiv, 2000);
  }

  /* Pagination methods */
  /**
   * Get pagination label
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  getPaginationLabel(page: Page): Promise<string> {
    return this.getTextContent(page, this.paginationLabel);
  }

  /**
   * Select pagination limit
   * @param page {Page} Browser tab
   * @param number {number} Value of pagination limit to select
   * @returns {Promise<string>}
   */
  async selectPaginationLimit(page: Page, number: number): Promise<string> {
    await this.selectByVisibleText(page, this.paginationLimitSelect, number);

    return this.getPaginationLabel(page);
  }

  /**
   * Click on next
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async paginationNext(page: Page): Promise<string> {
    await this.clickAndWaitForURL(page, this.paginationNextLink);

    return this.getPaginationLabel(page);
  }

  /**
   * Click on previous
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async paginationPrevious(page: Page): Promise<string> {
    await this.clickAndWaitForURL(page, this.paginationPreviousLink);

    return this.getPaginationLabel(page);
  }
}

export default new Products();
