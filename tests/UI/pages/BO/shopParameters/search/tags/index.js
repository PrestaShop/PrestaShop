require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class Tags extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Tags •';

    // Selectors
    // Header links
    this.addNewTagLink = '#page-header-desc-tag-new_tag';

    // Form selectors
    this.gridForm = '#form-tag';
    this.gridTableHeaderTitle = `${this.gridForm} .panel-heading`;
    this.gridTableNumberOfTitlesSpan = `${this.gridTableHeaderTitle} span.badge`;

    // Table selectors
    this.gridTable = '#table-carrier';

    // Filter selectors
    this.filterRow = `${this.gridTable} tr.filter`;
    this.filterColumn = filterBy => `${this.filterRow} [name='tagFilter_${filterBy}']`;
    this.filterSearchButton = '#submitFilterButtontag';
    this.filterResetButton = 'button[name=\'submitResettag\']';
  }

  /*
  Methods
   */

  /**
   * Get Number of lines
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
   * Reset and get number of lines
   * @param page
   * @return {Promise<number>}
   */
  async resetAndGetNumberOfLines(page) {
    await this.resetFilter(page);
    return this.getNumberOfElementInGrid(page);
  }

  /**
   * Go to add new tag page
   * @param page
   * @returns {Promise<void>}
   */
  async goToAddNewTagPage(page) {
    await this.clickAndWaitForNavigation(page, this.addNewTagLink);
  }
}

module.exports = new Tags();
