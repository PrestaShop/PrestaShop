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
    // Selectors of combinations table
    this.combinationsListTable = '#combination_list';
    this.combinationListTableRow = (row) => `#combination-list-row-${row - 1}`;
    this.combinationListTableColumn = (row, column) => `td input#combination_list_${row - 1}_${column}`;
    this.combinationListTableActionsColumn = (row, action) => `td button#combination_list_${row - 1}_actions_${action}`;
    // Edit combination modal
    this.editCombinationModal = '#combination-edit-modal div.combination-modal div.modal.show';
    this.editCombinationModalQuantityInput = '#combination_form_stock_quantities_delta_quantity_delta';
    this.editCombinationModalMinimalQuantityInput = '#combination_form_stock_quantities_minimal_quantity';
    this.editCombinationModalImpactOnPriceTExcInput = '#combination_form_price_impact_price_tax_excluded';
    this.editCombinationModalReferenceInput = '#combination_form_references_reference';
    this.editCombinationModalSaveButton = `${this.editCombinationModal} footer button.btn.btn-primary`;
  }

  /*
  Methods
   */

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
    await this.setValue(page, `${this.combinationListTableColumn(row, 'impact_on_price_te')}`, combinationData.impactOnPriceTExc);
    await this.setValue(page, `${this.combinationListTableColumn(row, 'delta_quantity_delta')}`, combinationData.quantity);

    await this.waitForSelectorAndClick(page, this.saveCombinationEditButton);

    return this.getGrowlMessageContent(page);
  }

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
   * Edit combination from modal
   * @param page {Page} Browser tab
   * @param combinationData {object}
   * @returns {Promise<string>}
   */
  async editCombinationFromModal(page, combinationData) {
    await page.waitForTimeout(5000);
    await this.waitForVisibleSelector(page, '.combination-iframe');

    const combinationFrame = await page.frame('.combination-iframe');
    console.log(combinationFrame);

    await this.setValue(combinationFrame, this.editCombinationModalQuantityInput, combinationData.quantity);
    await this.setValue(combinationFrame, this.editCombinationModalMinimalQuantityInput, combinationData.minimalQuantity);
    await this.setValue(combinationFrame, this.editCombinationModalImpactOnPriceTExcInput, combinationData.impactOnPriceTExc);
    await this.setValue(combinationFrame, this.editCombinationModalReferenceInput, combinationData.reference);

    await this.waitForSelectorAndClick(combinationFrame, this.editCombinationModalSaveButton);

    return this.getAlertSuccessBlockParagraphContent(combinationFrame);
  }
}

module.exports = new CombinationsTab();
