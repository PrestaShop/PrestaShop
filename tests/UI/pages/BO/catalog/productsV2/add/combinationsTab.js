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
    this.generateFirstCombinationsButton = '#combinations-empty-state button.generate-combinations-button';
    this.generateCombinationButton = '#combination-list-actions button.generate-combinations-button';
    this.generateCombinationsModal = '#product-combinations-generate div.modal.show';
    this.searchAttributesButton = `${this.generateCombinationsModal} input.attributes-search`;
    this.generateCombinationsButtonOnModal = `${this.generateCombinationsModal} footer button.btn.btn-primary`;
    this.generateCombinationsCloseButton = `${this.generateCombinationsModal} button.close`;
  }

  /*
  Methods
   */

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
}

module.exports = new CombinationsTab();
