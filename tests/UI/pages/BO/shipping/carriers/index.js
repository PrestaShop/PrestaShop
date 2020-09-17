require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class Carriers extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Carriers •';
    this.successfulUpdateStatusMessage = 'The status has been successfully updated.';

    // Selectors
    // Header links
    this.addNewCarrierLink = '#page-header-desc-carrier-new_carrier';

    // Form selectors
    this.gridForm = '#form-carrier';
    this.gridTableHeaderTitle = `${this.gridForm} .panel-heading`;
    this.gridTableNumberOfTitlesSpan = `${this.gridTableHeaderTitle} span.badge`;

    // Table selectors
    this.gridTable = '#table-carrier';

    // Filter selectors
    this.filterRow = `${this.gridTable} tr.filter`;
    this.filterColumn = filterBy => `${this.filterRow} [name='carrierFilter_${filterBy}']`;
    this.filterSearchButton = '#submitFilterButtoncarrier';
    this.filterResetButton = 'button[name=\'submitResetcarrier\']';
  }

  /*
  Methods
   */

  /* Filter methods */

  /**
   * Get Number of carriers
   * @param page
   * @return {Promise<number>}
   */
  getNumberOfElementInGrid(page) {
    return this.getNumberFromText(page, this.gridTableNumberOfTitlesSpan);
  }

  /**
   * Reset all filters
   * @param page
   * @return {Promise<void>}
   */
  async resetFilter(page) {
    if (!(await this.elementNotVisible(page, this.filterResetButton, 2000))) {
      await this.clickAndWaitForNavigation(page, this.filterResetButton);
    }
    await this.waitForVisibleSelector(page, this.filterSearchButton, 2000);
  }

  /**
   * Reset and get number of image types
   * @param page
   * @return {Promise<number>}
   */
  async resetAndGetNumberOfLines(page) {
    await this.resetFilter(page);
    return this.getNumberOfElementInGrid(page);
  }
}

module.exports = new Carriers();
