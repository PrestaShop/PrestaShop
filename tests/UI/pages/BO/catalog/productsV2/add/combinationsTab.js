require('module-alias/register');
// Importing page
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Combinations tab on new product V2 page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class CombinationsTab extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on combinations tab
   */
  constructor() {
    super();
    this.generateCombinationsMessage = (number) => `Generate ${number} combinations`;
    this.successfulGenerateCombinationsMessage = (number) => `Successfully generated ${number} combinations.`;
    this.editCombinationsModalTitle = (number) => `Edit ${number} combinations`;
    this.editCombinationsModalMessage = (number) => `Editing ${number}/${number} combinations`;
    this.successfulUpdateMessage = 'Update successful';

    // Selectors in combinations tab
    this.combinationsTabLink = '#product_combinations-tab-nav';
    this.attributesAndFeaturesLink = '#combinations-empty-state p.mx-auto.showcase-list-card__message a.alert-link';
    this.generateFirstCombinationsButton = '#combinations-empty-state button.generate-combinations-button';
    this.generateCombinationButton = '#combination-list-actions button.generate-combinations-button';
    this.generateCombinationsModal = '#product-combinations-generate div.modal.show';
    this.searchAttributesButton = `${this.generateCombinationsModal} input.attributes-search`;
    this.generateCombinationsButtonOnModal = `${this.generateCombinationsModal} footer button.btn.btn-primary`;
    this.generateCombinationsCloseButton = `${this.generateCombinationsModal} button.close`;
    this.saveCombinationEditButton = '#save-combinations-edition';

    // Bulk actions selectors
    this.bulkActionsButton = '#combination-bulk-actions-btn';
    this.bulkEditButton = '#combination-bulk-form-btn';

    // Bulk edit modal
    this.bulkEditModal = '#bulk-combination-form-modal';
    this.bulkEditModalTitle = `${this.bulkEditModal} .modal-header .modal-title`;
    // Edit stocks
    this.bulkEditModalStocksButton = '#bulk_combination_stock_accordion_header h2 button';
    this.bulkEditModalQuantitySwitchButton = (toEnable) => '#bulk_combination_stock_disabling_switch_delta_quantity_'
      + `${toEnable}`;
    this.bulkEditModalQuantityInput = '#bulk_combination_stock_delta_quantity_delta';
    this.bulkEditModalMinimalQuantitySwitchButton = (toEnable) => '#bulk_combination_stock_disabling_switch_minimal'
      + `_quantity_${toEnable}`;
    this.bulkEditModalMinimalQuantityInput = '#bulk_combination_stock_minimal_quantity';
    this.bulkEditModalStockLocationSwitchButton = (toEnable) => '#bulk_combination_stock_disabling_switch_stock_'
      + `location_${toEnable}`;
    this.bulkEditModalStockLocationInput = '#bulk_combination_stock_stock_location';
    this.bulkEditModalSaveButton = '#bulk-combination-form-modal div.modal-footer button.btn-confirm-submit';
    // Edit retail price
    this.bulkEditModalRetailPriceButton = '#bulk_combination_price_accordion_header h2 button';
    this.bulkEditModalCostPriceSwitchButton = (toEnable) => '#bulk_combination_price_disabling_switch_wholesale_'
      + `price_${toEnable}`;
    this.bulkEditModalCostPriceInput = '#bulk_combination_price_wholesale_price';
    this.bulkEditModalImpactOnPriceTIncSwitchButton = (toEnable) => '#bulk_combination_price_disabling_switch_price_'
      + `tax_excluded_${toEnable}`;
    this.bulkEditModalImpactOnPriceTIncInput = '#bulk_combination_price_price_tax_excluded';
    this.bulkEditModalImpactOnWeightSwitchButton = (toEnable) => '#bulk_combination_price_disabling_switch_weight_'
      + `${toEnable}`;
    this.bulkEditModalImpactOnWeightInput = '#bulk_combination_price_weight';
    // Edit specific references
    this.bulkEditModalSpecificReferences = '#bulk_combination_references_accordion_header h2 button';
    this.bulkEditModalReferenceSwitchButton = (toEnable) => '#bulk_combination_references_disabling_switch_'
      + `reference_${toEnable}`;
    this.bulkEditModalReferenceInput = '#bulk_combination_references_reference';
    // Save progress modal
    this.bulkCombinationProgressModal = '#bulk-combination-progress-modal';
    this.bulkCombinationProgressBarDone = '#modal_progressbar_done[aria-valuenow="100"]';
    this.bulkCombinationProgressModalHeader = '#bulk-combination-progress-modal div.progress-headline div';
    this.bulkCombinationProgressModalCloseButton = '#bulk-combination-progress-modal button.close-modal-button';

    // Filter by size selectors
    this.filterBySizeButton = '#form_invoice_prefix[data-role=filter-by-size]';
    this.filterBySizeDropDownMenu = '.combinations-filters-dropdown div.dropdown-menu';
    this.filterBySizeCheckboxButton = (id) => `div.combinations-filters div:nth-child(${id}) .md-checkbox-container`;
    this.clearFilterButton = 'div.combinations-filters button.combinations-filters-clear';

    // Selectors of combinations table
    this.combinationsListTable = '#combination_list';
    this.combinationListTableRow = (row) => `#combination-list-row-${row - 1}`;
    this.combinationListTableColumn = (row, column) => `td input#combination_list_${row - 1}_${column}`;
    this.combinationListTableActionsColumn = (row, action) => `td button#combination_list_${row - 1}_actions_${action}`;
    this.combinationListTableSelectAllButton = '#bulk-all-selection-dropdown-button';
    this.combinationListSelectAllDropDownMenu = '#bulk-all-selection-dropdown .dropdown-menu.show';
    this.combinationListBulkSelectAll = '#bulk-all-selection-dropdown label[for="bulk-select-all"]';
    this.combinationListBulkSelectAllInPage = '#bulk-select-all-in-page + i';

    // Edit combination modal
    this.editCombinationIframe = '.combination-iframe';
    this.editCombinationEditModal = '#combination-edit-modal';
    this.editCombinationModal = '#combination-edit-modal div.combination-modal div.modal.show';
    this.editCombinationModalQuantityInput = '#combination_form_stock_quantities_delta_quantity_delta';
    this.editCombinationModalMinimalQuantityInput = '#combination_form_stock_quantities_minimal_quantity';
    this.editCombinationModalImpactOnPriceTExcInput = '#combination_form_price_impact_price_tax_excluded';
    this.editCombinationModalReferenceInput = '#combination_form_references_reference';
    this.editCombinationModalSaveButton = `${this.editCombinationModal} footer button.btn-primary`;
    this.editCombinationModalCancelButton = `${this.editCombinationModal} footer button.btn-close`;
    this.editCombinationCloseModal = `${this.editCombinationEditModal} div.modal-prevent-close div.modal.show`;
    this.editCombinationModalDiscardButton = `${this.editCombinationCloseModal} button.btn-primary`;
    this.combinationStockMovementsDate = (row) => `#combination_form_stock_quantities_stock_movements_${row - 1}_`
      + 'date + span';
    this.combinationStockMovementsEmployeeName = (row) => '#combination_form_stock_quantities_stock_movements_'
      + `${row - 1}_employee_name + span`;
    this.combinationStockMovements = (row) => `#combination_form_stock_quantities_stock_movements_${row - 1}`
      + '_delta_quantity + span';

    // Delete combination modal
    this.modalConfirmDeleteCombination = '#modal-confirm-delete-combination';
    this.modalDeleteCombinationCancelButton = `${this.modalConfirmDeleteCombination} button.btn-outline-secondary`;
    this.modalDeleteCombinationDeletelButton = `${this.modalConfirmDeleteCombination} button.btn-confirm-submit`;

    // Sort Selectors
    this.tableHead = `${this.combinationsListTable} thead`;
    this.sortColumnDiv = (columnNumber) => `${this.tableHead} th:nth-child(${columnNumber}) `
      + 'div.ps-sortable-column[data-sort-col-name]';
    this.sortColumnSpanButton = (columnNumber) => `${this.sortColumnDiv(columnNumber)} span.ps-sort`;

    // Pagination selectors
    this.paginationBlock = '#combinations-pagination';
    this.paginationLabel = '#pagination-info';
    this.paginationLimitSelect = '#paginator-limit';
    this.paginationNextLink = `${this.paginationBlock} .page-link.next:not(.disabled)`;
    this.paginationPreviousLink = `${this.paginationBlock} .page-link.previous:not(.disabled)`;
  }

  /*
  Methods
   */

  // Methods for create combinations
  /**
   * Click on attributes & features link
   * @param page {Page} Browser tab
   * @returns {Promise<Page>}
   */
  async clickOnAttributesAndFeaturesLink(page) {
    await this.waitForSelectorAndClick(page, this.combinationsTabLink);

    return this.openLinkWithTargetBlank(page, this.attributesAndFeaturesLink, 'body');
  }

  /**
   * Add combination
   * @param page {Page} Browser tab
   * @param combination {string} Attribute to set
   * @returns {Promise<void>}
   */
  async selectAttribute(page, combination) {
    await page.type(this.searchAttributesButton, combination);
    await page.keyboard.press('ArrowDown');
    await page.keyboard.press('Enter');
  }

  /**
   * Set product attributes
   * @param page {Page} Browser tab
   * @param attributes {Object} Combinations of the product
   * @returns {Promise<string>}
   */
  async setProductAttributes(page, attributes) {
    await this.waitForSelectorAndClick(page, this.combinationsTabLink);
    if (await this.elementVisible(page, this.generateCombinationButton, 2000)) {
      await this.waitForSelectorAndClick(page, this.generateCombinationButton);
    } else {
      await this.waitForSelectorAndClick(page, this.generateFirstCombinationsButton);
    }

    await this.waitForVisibleSelector(page, this.generateCombinationsModal);
    const keys = Object.keys(attributes);
    /*eslint-disable*/
    for (const key of keys) {
      for (const value of attributes[key]) {
        await this.selectAttribute(page, `${key} : ${value}`);
      }
    }
    /* eslint-enable */

    return this.getTextContent(page, this.generateCombinationsButtonOnModal);
  }

  /**
   * Generate combinations
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async generateCombinations(page) {
    await this.waitForSelectorAndClick(page, this.generateCombinationsButtonOnModal);

    return this.getGrowlMessageContent(page);
  }

  /**
   * Close generateCombinations modal
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async closeGenerateCombinationModal(page) {
    await this.waitForSelectorAndClick(page, this.generateCombinationsCloseButton);

    return this.elementNotVisible(page, this.generateCombinationsModal, 1000);
  }

  // Methods for edit/delete combinations

  /**
   * Click on edit icon
   * @param page {Page} Browser tab
   * @param row {number} Row in table
   * @returns {Promise<void>}
   */
  async clickOnEditIcon(page, row = 1) {
    await this.waitForSelectorAndClick(page, `${this.combinationListTableActionsColumn(row, 'edit')}`);

    return this.elementVisible(page, this.editCombinationModal, 2000);
  }

  /**
   * Edit combination
   * @param page {Page} Browser tab
   * @param combinationData {object} Data to set to edit combination
   * @param row {number} Row in table
   * @returns {Promise<string>}
   */
  async editCombination(page, combinationData, row = 1) {
    await this.closeGrowlMessage(page);
    await this.setValue(page, `${this.combinationListTableColumn(row, 'reference')}`, combinationData.reference);
    await this.setValue(
      page,
      `${this.combinationListTableColumn(row, 'impact_on_price_te')}`,
      combinationData.impactOnPriceTExc,
    );
    await this.setValue(
      page,
      `${this.combinationListTableColumn(row, 'delta_quantity_delta')}`,
      combinationData.quantity,
    );

    await this.waitForSelectorAndClick(page, this.saveCombinationEditButton);

    return this.getGrowlMessageContent(page);
  }

  /**
   * Click on delete icon then (delete/cancel)
   * @param page {Page} Browser tab
   * @param action {string} Delete/cancel
   * @param row {number} Row in table
   * @returns {Promise<string|boolean>}
   */
  async clickOnDeleteIcon(page, action, row = 1) {
    await this.waitForSelectorAndClick(page, `${this.combinationListTableActionsColumn(row, 'delete')}`);

    if (action === 'cancel') {
      await this.waitForSelectorAndClick(page, this.modalDeleteCombinationCancelButton);
      return !(await this.elementNotVisible(page, this.modalConfirmDeleteCombination, 2000));
    }

    await this.waitForSelectorAndClick(page, this.modalDeleteCombinationDeletelButton);

    return this.getGrowlMessageContent(page);
  }

  /**
   * Edit combination from modal
   * @param page {Page} Browser tab
   * @param combinationData {object}
   * @returns {Promise<string>}
   */
  async editCombinationFromModal(page, combinationData) {
    await page.waitForTimeout(2000);
    await this.waitForVisibleSelector(page, this.editCombinationIframe);

    const combinationFrame = await page.frame({url: /sell\/catalog\/products-v2\/combinations/gmi});

    await this.setValue(combinationFrame, this.editCombinationModalQuantityInput, combinationData.quantity);
    await this.setValue(
      combinationFrame,
      this.editCombinationModalMinimalQuantityInput,
      combinationData.minimalQuantity,
    );
    await this.setValue(
      combinationFrame,
      this.editCombinationModalImpactOnPriceTExcInput,
      combinationData.impactOnPriceTExc,
    );
    await this.setValue(combinationFrame, this.editCombinationModalReferenceInput, combinationData.reference);

    await this.waitForSelectorAndClick(page, this.editCombinationModalSaveButton);

    return this.getAlertSuccessBlockParagraphContent(combinationFrame);
  }

  /**
   * Get recent stock movements
   * @param page {Page} Browser tab
   * @param row {number} Row in table
   * @returns {Promise<{dateTime: string, quantity: string, employee: string}>}
   */
  async getRecentStockMovements(page, row = 1) {
    const combinationFrame = await page.frame({url: /sell\/catalog\/products-v2\/combinations/gmi});

    return {
      dateTime: await this.getTextContent(combinationFrame, this.combinationStockMovementsDate(row)),
      employee: await this.getTextContent(combinationFrame, this.combinationStockMovementsEmployeeName(row)),
      quantity: await this.getNumberFromText(combinationFrame, this.combinationStockMovements(row)),
    };
  }

  /**
   * Close edit combination modal
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async closeEditCombinationModal(page) {
    const combinationFrame = await page.frame({url: /sell\/catalog\/products-v2\/combinations/gmi});

    await this.waitForSelectorAndClick(page, this.editCombinationModalCancelButton);
    await this.waitForSelectorAndClick(page, this.editCombinationModalDiscardButton);

    return this.elementVisible(combinationFrame, this.editCombinationModalQuantityInput, 2000);
  }

  // Methods for sort
  /**
   * Sort table by clicking on column name
   * @param page {Page} Browser tab
   * @param sortBy {string} Column to sort with
   * @param column {number} The number of columns
   * @param sortDirection {string} Sort direction asc or desc
   * @return {Promise<void>}
   */
  async sortTable(page, sortBy, column, sortDirection = 'asc') {
    const sortColumnDiv = `${this.sortColumnDiv(column)}[data-sort-direction='${sortDirection}']`;
    const sortColumnSpanButton = this.sortColumnSpanButton(column);

    let i = 0;
    while (await this.elementNotVisible(page, sortColumnDiv, 2000) && i < 2) {
      await page.hover(this.sortColumnDiv(column));
      await this.waitForSelectorAndClick(page, sortColumnSpanButton);
      i += 1;
    }
  }

  /**
   * Get text column
   * @param page {Page} Browser tab
   * @param column {} Column name to get text content
   * @param row {number} Row on table
   * @returns {Promise<string>}
   */
  async getTextColumn(page, column, row = 1) {
    const selector = this.combinationListTableColumn(row, column);
    let text;

    switch (column) {
      case 'combination_id':
        text = await this.getTextContent(page, `${selector} + span`);
        break;
      case 'name':
        text = await this.getTextContent(page, `${selector} + span`);
        break;
      case 'impact_on_price_te':
        text = await this.getAttributeContent(page, selector, 'value');
        break;
      case 'impact_on_price_ti':
        text = await this.getAttributeContent(page, selector, 'value');
        break;
      case 'final_price_te':
        text = await this.getTextContent(page, `${selector} + span`);
        break;
      case 'quantity':
        text = await this.getTextContent(page, selector);
        break;
      default:
      // Do nothing
    }
    // click on search

    return text;
  }

  /**
   * Get content from all rows
   * @param page {Page} Browser tab
   * @param numberOfCombinations {number} Number of combinations
   * @param column {string} Column name to get all rows text content
   * @return {Promise<Array<string>>}
   */
  async getAllRowsColumnContent(page, numberOfCombinations, column) {
    const allRowsContentTable = [];

    for (let i = 1; i <= numberOfCombinations; i++) {
      const rowContent = await this.getTextColumn(page, column, i);
      allRowsContentTable.push(rowContent);
    }

    return allRowsContentTable;
  }

  // Methods for pagination
  /**
   * Get pagination label
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async getPaginationLabel(page) {
    await page.waitForTimeout(2000);

    return this.getTextContent(page, this.paginationLabel);
  }

  /**
   * Select pagination limit
   * @param page {Page} Browser tab
   * @param number {number} Value of pagination limit to select
   * @returns {Promise<string>}
   */
  async selectPaginationLimit(page, number) {
    await this.selectByVisibleText(page, this.paginationLimitSelect, number);

    return this.getPaginationLabel(page);
  }

  /**
   * Click on next
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async paginationNext(page) {
    await this.waitForSelectorAndClick(page, this.paginationNextLink);

    return this.getPaginationLabel(page);
  }

  /**
   * Click on previous
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async paginationPrevious(page) {
    await this.waitForSelectorAndClick(page, this.paginationPreviousLink);

    return this.getPaginationLabel(page);
  }

  // Methods for filter combinations by size
  /**
   * Get number of combinations displayed in list
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfCombinationsFromList(page) {
    const footerText = await this.getTextContent(page, this.paginationLabel);
    const numberOfCombinations = /\d+/g.exec(footerText.match(/out of ([0-9]+)/)).toString();

    return parseInt(numberOfCombinations, 10);
  }

  /**
   * Filter combinations by color
   * @param page {Page} Browser tab
   * @param sizeID {number} Size number in list
   * @returns {Promise<void>}
   */
  async filterCombinationsBySize(page, sizeID) {
    await this.waitForSelectorAndClick(page, this.filterBySizeButton);
    await this.waitForVisibleSelector(page, `${this.filterBySizeDropDownMenu}.show`);
    await this.setChecked(page, this.filterBySizeCheckboxButton(sizeID));

    await this.waitForSelectorAndClick(page, this.filterBySizeButton);
  }

  /**
   * Get filter button name
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getFilterBySizeButtonName(page) {
    return this.getTextContent(page, this.filterBySizeButton);
  }

  /**
   * Clear filter
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async clearFilter(page) {
    await this.waitForSelectorAndClick(page, this.clearFilterButton);

    return this.getNumberOfCombinationsFromList(page);
  }

  // Methods for bulk actions
  /**
   * Select All combinations
   * @param page {Page} Browser tab
   * @param allCombinations {boolean} true to select all combinations, false to select all in page
   * @returns {Promise<boolean>}
   */
  async selectAllCombinations(page, allCombinations = true) {
    await this.waitForSelectorAndClick(page, this.combinationListTableSelectAllButton);

    await this.waitForVisibleSelector(page, this.combinationListSelectAllDropDownMenu);
    if (allCombinations) {
      await this.setChecked(page, this.combinationListBulkSelectAll);
    } else await this.setChecked(page, this.combinationListBulkSelectAllInPage);

    return this.elementVisible(page, this.bulkActionsButton, 2000);
  }

  /**
   * Click on edit Combinations by bulk actions
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async clickOnEditCombinationsByBulkActions(page) {
    await this.waitForSelectorAndClick(page, this.bulkActionsButton);
    await this.waitForSelectorAndClick(page, this.bulkEditButton);

    await this.waitForVisibleSelector(page, this.bulkEditModal);

    return this.getTextContent(page, this.bulkEditModalTitle);
  }

  /**
   * Bulk edit stock
   * @param page {Page} Browser tab
   * @param editStockData {object} Data to set on bulk edit stock form
   * @returns {Promise<void>}
   */
  async bulkEditStock(page, editStockData) {
    await this.waitForSelectorAndClick(page, this.bulkEditModalStocksButton);
    await this.setChecked(
      page,
      this.bulkEditModalQuantitySwitchButton(editStockData.quantityToEnable ? 1 : 0),
    );
    await this.setValue(page, this.bulkEditModalQuantityInput, editStockData.quantity);
    await this.setChecked(
      page,
      this.bulkEditModalMinimalQuantitySwitchButton(editStockData.minimalQuantityToEnable ? 1 : 0),
    );
    await this.setValue(
      page,
      this.bulkEditModalMinimalQuantityInput,
      editStockData.minimalQuantity,
    );
    await this.setChecked(
      page,
      this.bulkEditModalStockLocationSwitchButton(editStockData.stockLocationToEnable ? 1 : 0),
    );
    await this.setValue(
      page,
      this.bulkEditModalStockLocationInput,
      editStockData.stockLocation,
    );
    await this.waitForSelectorAndClick(page, this.bulkEditModalStocksButton);
  }

  /**
   * Bulk edit retail price
   * @param page {Page} Browser tab
   * @param editRetailPriceData {object} Data to set on bulk edit retail price form
   * @returns {Promise<void>}
   */
  async bulkEditRetailPrice(page, editRetailPriceData) {
    await this.waitForSelectorAndClick(page, this.bulkEditModalRetailPriceButton);
    await this.setChecked(page, this.bulkEditModalCostPriceSwitchButton(editRetailPriceData.costPriceToEnable ? 1 : 0),
    );
    await this.setValue(page, this.bulkEditModalCostPriceInput, editRetailPriceData.costPrice);
    await this.setChecked(page,
      this.bulkEditModalImpactOnPriceTIncSwitchButton(editRetailPriceData.impactOnPriceTIncToEnable ? 1 : 0),
    );
    await this.setValue(page, this.bulkEditModalImpactOnPriceTIncInput, editRetailPriceData.impactOnPriceTInc);
    await this.setChecked(
      page,
      this.bulkEditModalImpactOnWeightSwitchButton(editRetailPriceData.impactOnWeightToEnable ? 1 : 0),
    );
    await this.setValue(page, this.bulkEditModalImpactOnWeightInput, editRetailPriceData.impactOnWeight);
    await this.waitForSelectorAndClick(page, this.bulkEditModalRetailPriceButton);
  }

  /**
   * Bulk edit references
   * @param page {Page} Browser tab
   * @param specificReferencesData {object} Data to set on specific references form
   * @returns {Promise<void>}
   */
  async bulkEditSpecificPrice(page, specificReferencesData) {
    await this.waitForSelectorAndClick(page, this.bulkEditModalSpecificReferences);
    await this.setChecked(
      page,
      this.bulkEditModalReferenceSwitchButton(specificReferencesData.referenceToEnable ? 1 : 0),
    );
    await this.setValue(page, this.bulkEditModalReferenceInput, specificReferencesData.reference);
  }

  /**
   * Edit combinations by bulk actions
   * @param page {Page} Browser tab
   * @param editCombinationsData {object}
   * @returns {Promise<string>}
   */
  async editCombinationsByBulkActions(page, editCombinationsData) {
    const bulkEditCombinationFrame = await page.frame('bulk-combination-form-modal-iframe');

    // Edit stocks
    if (editCombinationsData.stocks) {
      await this.bulkEditStock(bulkEditCombinationFrame, editCombinationsData.stocks);
    }
    // Edit retail price
    if (editCombinationsData.retailPrice) {
      await this.bulkEditRetailPrice(bulkEditCombinationFrame, editCombinationsData.retailPrice);
    }
    // Edit specific references
    if (editCombinationsData.specificReferences) {
      await this.bulkEditSpecificPrice(bulkEditCombinationFrame, editCombinationsData.specificReferences);
    }
    // Save and close progress modal
    await this.waitForSelectorAndClick(page, this.bulkEditModalSaveButton);
    await this.waitForVisibleSelector(page, this.bulkCombinationProgressModal);
    await this.waitForVisibleSelector(page, this.bulkCombinationProgressBarDone);
    const result = await this.getTextContent(page, this.bulkCombinationProgressModalHeader);
    await this.waitForSelectorAndClick(page, this.bulkCombinationProgressModalCloseButton);

    // Return text in modal
    return result;
  }
}

module.exports = new CombinationsTab();
