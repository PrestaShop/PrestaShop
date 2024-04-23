import BOBasePage from '@pages/BO/BObasePage';

import {ImageTypeRegeneration, ImageTypeRegenerationSpecific} from '@data/types/imageType';

import type {Page} from 'playwright';

/**
 * Image settings page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class ImageSettings extends BOBasePage {
  public readonly pageTitle: string;

  public readonly messageThumbnailsRegenerated: string;

  public readonly messageSettingsUpdated: string;

  private readonly newImageTypeLink: string;

  private readonly gridPanel: string;

  private readonly gridForm: string;

  private readonly gridTableHeaderTitle: string;

  private readonly filterRow: string;

  private readonly selectAllRowsDiv: string;

  private readonly filterColumn: (filterBy: string) => string;

  private readonly filterSearchButton: string;

  private readonly filterResetButton: string;

  private readonly tableBody: string;

  private readonly tableBodyRows: string;

  private readonly tableBodyRow: (row: number) => string;

  private readonly tableBodyColumn: (row: number) => string;

  private readonly tableBodySpecificColumn: (row: number, columnName: string) => string;

  private readonly tableColumnStatus: (row: number, columnName: string, status: string) => string;

  private readonly tableHead: string;

  private readonly sortColumnDiv: (column: string) => string;

  private readonly sortColumnSpanButton: (column: string) => string;

  private readonly tableColumnActions: (row: number) => string;

  private readonly tableColumnActionsEditLink: (row: number) => string;

  private readonly tableColumnActionsToggleButton: (row: number) => string;

  private readonly tableColumnActionsDropdownMenu: (row: number) => string;

  private readonly tableColumnActionsDeleteLink: (row: number) => string;

  private readonly deleteModal: string;

  private readonly deleteModalButtonYes: string;

  private readonly deleteModalCheckboxDeleteLinkedImages: (toggle: number) => string;

  private readonly bulkActionsToggleButton: string;

  private readonly bulkActionsDeleteButton: string;

  private readonly bulkDeleteModal: string;

  private readonly bulkDeleteModalButton: string;

  private readonly paginationLimitSelect: string;

  private readonly paginationLabel: string;

  private readonly paginationPreviousLink: string;

  private readonly paginationNextLink: string;

  private readonly formImageGenerationOptions: string;

  private readonly checkboxImageFormat: (imageFormat: string) => string;

  private readonly checkboxBaseFormat: (baseFormat: string) => string;

  private readonly submitImageGenerationOptions: string;

  private readonly formRegenerateThumbnails: string;

  private readonly selectRegenerateThumbnailsImage: string;

  private readonly selectRegenerateThumbnailsFormat: string;

  private readonly optionSelectRegenerateThumbnailsFormat: string;

  private readonly checkboxRegenerateThumbnailsErasePreviousImages: (toggle: number) => string;

  private readonly submitRegenerateThumbnails: string;

  private readonly modalRegenerateThumbnails: string;

  private readonly modalSubmitRegenerateThumbnails: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on image settings page
   */
  constructor() {
    super();

    this.successfulUpdateMessage = 'Update successful';

    this.pageTitle = 'Image Settings • ';
    this.messageThumbnailsRegenerated = 'The thumbnails were successfully regenerated.';
    this.messageSettingsUpdated = 'The settings have been successfully updated.';

    // Header selectors
    this.newImageTypeLink = 'a#page-header-desc-configuration-add';

    // Form selectors
    this.gridPanel = '#image_type_grid_panel';
    this.gridForm = '#image_type_grid';
    this.gridTableHeaderTitle = `${this.gridPanel} h3.card-header-title`;

    // Filter selectors
    this.filterRow = `${this.gridPanel} tr.column-filters`;
    this.selectAllRowsDiv = `${this.filterRow} .grid_bulk_action_select_all`;
    this.filterColumn = (filterBy: string) => `${this.filterRow} [name='image_type[${filterBy}]']`;
    this.filterSearchButton = `${this.gridForm} .grid-search-button`;
    this.filterResetButton = `${this.gridForm} .grid-reset-button`;

    // Table body selectors
    this.tableBody = `${this.gridPanel} tbody`;
    this.tableBodyRows = `${this.tableBody} tr`;
    this.tableBodyRow = (row: number) => `${this.tableBodyRows}:nth-child(${row})`;
    this.tableBodyColumn = (row: number) => `${this.tableBodyRow(row)} td`;

    // Columns selectors
    this.tableBodySpecificColumn = (row: number, columnName: string) => `${this.tableBodyColumn(row)}.column-${columnName}`;
    this.tableColumnStatus = (row: number, columnName: string, status: string) => `${
      this.tableBodySpecificColumn(row, columnName)} span.action-${status}`;

    // Sort Selectors
    this.tableHead = `${this.gridPanel} thead`;
    this.sortColumnDiv = (column: string) => `${this.tableHead} div.ps-sortable-column[data-sort-col-name='${column}']`;
    this.sortColumnSpanButton = (column: string) => `${this.sortColumnDiv(column)} span.ps-sort`;

    // Row actions selectors
    this.tableColumnActions = (row: number) => `${this.tableBodyColumn(row)} .btn-group-action`;
    this.tableColumnActionsEditLink = (row: number) => `${this.tableColumnActions(row)} a.grid-edit-row-link`;
    this.tableColumnActionsToggleButton = (row: number) => `${this.tableColumnActions(row)} a.dropdown-toggle`;
    this.tableColumnActionsDropdownMenu = (row: number) => `${this.tableColumnActions(row)} .dropdown-menu`;
    this.tableColumnActionsDeleteLink = (row: number) => `${this.tableColumnActionsDropdownMenu(row)} a.grid-delete-row-link`;

    // Confirmation modal
    this.deleteModal = '#image_type_grid_delete_image_type_modal';
    this.deleteModalButtonYes = `${this.deleteModal} button.js-submit-delete-image-type`;
    this.deleteModalCheckboxDeleteLinkedImages = (toggle: number) => `${this.deleteModal} `
      + `#delete_image_type_delete_images_files_too_${toggle}`;
    this.deleteModalButtonYes = `${this.deleteModal} button.js-submit-delete-image-type`;

    // Bulk actions selectors
    this.bulkActionsToggleButton = `${this.gridForm} button.dropdown-toggle.js-bulk-actions-btn`;
    this.bulkActionsDeleteButton = `${this.gridForm} #image_type_grid_bulk_action_delete_selection`;
    this.bulkDeleteModal = '#image_type-grid-confirm-modal';
    this.bulkDeleteModalButton = `${this.bulkDeleteModal} button.btn-confirm-submit`;

    // Pagination selectors
    this.paginationLimitSelect = '#paginator_select_page_limit';
    this.paginationLabel = `${this.gridPanel} .col-form-label`;
    this.paginationNextLink = `${this.gridPanel} [data-role=next-page-link]`;
    this.paginationPreviousLink = `${this.gridPanel} [data-role='previous-page-link']`;

    // Images generation options
    this.formImageGenerationOptions = 'form[name="image_settings"]';
    this.checkboxImageFormat = (imageFormat: string) => `${this.formImageGenerationOptions} `
      + `input[name="image_settings[formats][]"][value="${imageFormat}"]`;
    this.checkboxBaseFormat = (baseFormat: string) => `${this.formImageGenerationOptions} `
      + `input[name="image_settings[base-format]"][value="${baseFormat}"]`;
    this.submitImageGenerationOptions = `${this.formImageGenerationOptions} button#save-button`;

    // Regenerate thumbnails
    this.formRegenerateThumbnails = 'form[name="regenerate_thumbnails"]';
    this.selectRegenerateThumbnailsImage = `${this.formRegenerateThumbnails} select#regenerate_thumbnails_image`;
    this.selectRegenerateThumbnailsFormat = `${this.formRegenerateThumbnails} select#regenerate_thumbnails_image-type`;
    this.optionSelectRegenerateThumbnailsFormat = `${this.selectRegenerateThumbnailsFormat} option[style=""]`;
    this.checkboxRegenerateThumbnailsErasePreviousImages = (toggle: number) => `${this.formRegenerateThumbnails} `
      + `input#regenerate_thumbnails_erase-previous-images_${toggle}`;
    this.submitRegenerateThumbnails = `${this.formRegenerateThumbnails} button#regenerate-thumbnails-button`;
    this.modalRegenerateThumbnails = '#regeneration-confirm-modal';
    this.modalSubmitRegenerateThumbnails = `${this.modalRegenerateThumbnails} .btn-confirm-submit`;
  }

  /* Header methods */
  /**
   * Go to new image type page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToNewImageTypePage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.newImageTypeLink);
  }

  /* Filter methods */

  /**
   * Get number of image types
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  async getNumberOfElementInGrid(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.gridTableHeaderTitle);
  }

  /**
   * Reset all filters
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async resetFilter(page: Page): Promise<void> {
    if (await this.elementVisible(page, this.filterResetButton, 2000)) {
      await this.clickAndWaitForLoadState(page, this.filterResetButton);
      await this.elementNotVisible(page, this.filterResetButton, 2000);
    }
  }

  /**
   * Reset and get number of image types
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  async resetAndGetNumberOfLines(page: Page): Promise<number> {
    await this.resetFilter(page);

    return this.getNumberOfElementInGrid(page);
  }

  /**
   * Filter image types
   * @param page {Page} Browser tab
   * @param filterType {string} Input or select to choose method of filter
   * @param filterBy {string} Column to filter
   * @param value {string} Value to filter with
   * @return {Promise<void>}
   */
  async filterTable(page: Page, filterType: string, filterBy: string, value: string): Promise<void> {
    switch (filterType) {
      case 'input':
        await this.setValue(page, this.filterColumn(filterBy), value);
        break;

      case 'select':
        await this.selectByVisibleText(page, this.filterColumn(filterBy), value);
        break;

      default:
        throw new Error(`Filter ${filterBy} was not found`);
    }
    // click on search
    await page.locator(this.filterSearchButton).click();
    await this.elementVisible(page, this.filterResetButton, 2000);
  }

  /* Column methods */

  /**
   * Get text from column in table
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param columnName {string} Value of column name to get text column
   * @return {Promise<string>}
   */
  getTextColumn(page: Page, row: number, columnName: string): Promise<string> {
    return this.getTextContent(page, this.tableBodySpecificColumn(row, columnName));
  }

  /**
   * Get content from all rows
   * @param page {Page} Browser tab
   * @param columnName {string} Value of column name to get all rows column content
   * @return {Promise<Array<string>>}
   */
  async getAllRowsColumnContent(page: Page, columnName: string): Promise<string[]> {
    const rowsNumber = await this.getNumberOfElementInGrid(page);
    const allRowsContentTable: string[] = [];

    for (let i = 1; i <= rowsNumber; i++) {
      const rowContent = await this.getTextColumn(page, i, columnName);
      allRowsContentTable.push(rowContent);
    }

    return allRowsContentTable;
  }

  /**
   * Get image type status for pages: products, categories, manufacturers, suppliers or stores
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param columnName {string} Value of column name to get type status
   * @return {Promise<boolean>}
   */
  async getImageTypeStatus(page: Page, row: number, columnName: string): Promise<boolean> {
    return this.elementVisible(page, this.tableColumnStatus(row, columnName, 'enabled'), 1000);
  }

  /**
   * Go to edit imageType page
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<void>}
   */
  async gotoEditImageTypePage(page: Page, row: number): Promise<void> {
    await this.clickAndWaitForURL(page, this.tableColumnActionsEditLink(row));
  }

  /**
   * Delete image type from row
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param deleteLinkedImages {boolean} Delete the images linked to this image setting
   * @return {Promise<string>}
   */
  async deleteImageType(page: Page, row: number, deleteLinkedImages: boolean = false): Promise<string> {
    await Promise.all([
      page.locator(this.tableColumnActionsToggleButton(row)).click(),
      this.waitForVisibleSelector(
        page,
        `${this.tableColumnActionsToggleButton(row)}[aria-expanded='true']`,
      ),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      page.locator(this.tableColumnActionsDeleteLink(row)).click(),
      this.waitForVisibleSelector(page, `${this.deleteModal}.show`),
    ]);

    await this.setChecked(page, this.deleteModalCheckboxDeleteLinkedImages(deleteLinkedImages ? 1 : 0));
    await page.locator(this.deleteModalButtonYes).click();

    if (await this.elementVisible(page, this.alertSuccessBlockParagraph, 2000)) {
      return this.getAlertSuccessBlockParagraphContent(page);
    }
    return this.getAlertDangerBlockParagraphContent(page);
  }

  /* Bulk actions methods */
  /**
   * Bulk delete image types
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async bulkDeleteImageTypes(page: Page): Promise<string> {
    // Click on Select All
    await Promise.all([
      page.locator(this.selectAllRowsDiv).evaluate((el: HTMLElement) => el.click()),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}:not([disabled])`),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      page.locator(this.bulkActionsToggleButton).click(),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}[aria-expanded='true']`),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      page.locator(this.bulkActionsDeleteButton).click(),
      this.waitForVisibleSelector(page, `${this.bulkDeleteModal}.show`),
    ]);
    await this.clickAndWaitForLoadState(page, this.bulkDeleteModalButton);
    await this.elementNotVisible(page, this.bulkDeleteModal);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /* Sort functions */
  /**
   * Sort table by clicking on column name
   * @param page {Page} Browser tab
   * @param sortBy {string} Column to sort with
   * @param sortDirection {string} Sort direction asc or desc
   * @return {Promise<void>}
   */
  async sortTable(page: Page, sortBy: string, sortDirection: string): Promise<void> {
    const sortColumnDiv = `${this.sortColumnDiv(sortBy)}[data-sort-direction='${sortDirection}']`;
    const sortColumnSpanButton = this.sortColumnSpanButton(sortBy);

    let i: number = 0;
    while (await this.elementNotVisible(page, sortColumnDiv, 2000) && i < 2) {
      await page.locator(this.sortColumnDiv(sortBy)).hover();
      await this.clickAndWaitForURL(page, sortColumnSpanButton);
      i += 1;
    }

    await this.waitForVisibleSelector(page, sortColumnDiv, 20000);
  }

  /* Pagination methods */
  /**
   * Get pagination label
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async getPaginationLabel(page: Page): Promise<string> {
    return this.getTextContent(page, this.paginationLabel);
  }

  /**
   * Select pagination limit
   * @param page {Page} Browser tab
   * @param number {number} Value of pagination limit to select
   * @returns {Promise<string>}
   */
  async selectPaginationLimit(page: Page, number: number): Promise<string> {
    const currentUrl: string = page.url();

    await Promise.all([
      this.selectByVisibleText(page, this.paginationLimitSelect, number),
      page.waitForURL((url: URL): boolean => url.toString() !== currentUrl, {waitUntil: 'networkidle'}),
    ]);

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

  /**
   * Regenerate Thumbnails
   * @param page {Page} Browser tab
   * @param image {ImageTypeRegeneration} Image
   * @param format {string} Format
   * @param erasePreviousImages {boolean} Erase previous images
   * @returns {Promise<string>}
   */
  async regenerateThumbnails(
    page: Page,
    image: ImageTypeRegeneration = 'all',
    format: string = 'All',
    erasePreviousImages: boolean = false,
  ): Promise<string> {
    // Choose the type of image to regenerate thumbnails
    await this.selectByValue(page, this.selectRegenerateThumbnailsImage, image);
    if (image !== 'all') {
      // Choose the format of image to regenerate thumbnails
      await this.selectByVisibleText(page, this.selectRegenerateThumbnailsFormat, format);
    }
    // Erase previous images
    await this.setChecked(page, this.checkboxRegenerateThumbnailsErasePreviousImages(erasePreviousImages ? 1 : 0));
    // Click on Submit
    await page.locator(this.submitRegenerateThumbnails).click();

    // Modal & Submit
    await this.waitForVisibleSelector(page, this.modalSubmitRegenerateThumbnails);
    await page.locator(this.modalSubmitRegenerateThumbnails).click();

    // Return successful message
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Returns if the image format is checked in Image Generation Options
   * @param page {Page} Browser tab
   * @param imageFormat {string} Image Format
   * @returns {Promise<boolean>}
   */
  async isImageFormatToGenerateChecked(page: Page, imageFormat: string): Promise<boolean> {
    return this.isChecked(page, this.checkboxImageFormat(imageFormat));
  }

  /**
   Returns if the image format is disabled in Image Generation Options
   * @param page {Page} Browser tab
   * @param imageFormat {string} Image Format
   * @returns {Promise<boolean>}
   */
  async isImageFormatToGenerateDisabled(page: Page, imageFormat: string): Promise<boolean> {
    return this.isDisabled(page, this.checkboxImageFormat(imageFormat));
  }

  /**
   * Enable/Disable the image format in Image Generation Options
   * @param page {Page} Browser tab
   * @param imageFormat {string} Image Format
   * @param valueWanted {boolean} Checked or not
   * @returns {Promise<string>}
   */
  async setImageFormatToGenerateChecked(page: Page, imageFormat: string, valueWanted: boolean): Promise<string> {
    await this.setCheckedWithIcon(page, this.checkboxImageFormat(imageFormat), valueWanted);
    await page.locator(this.submitImageGenerationOptions).click();

    // Return successful message
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Returns if the base format is checked in Image Generation Options
   * @param page {Page} Browser tab
   * @param baseFormat {string} base format
   * @returns {Promise<boolean>}
   */
  async isBaseFormatToGenerateChecked(page: Page, baseFormat: string): Promise<boolean> {
    return this.isChecked(page, this.checkboxBaseFormat(baseFormat));
  }

  /**
   * Enable/Disable the base format in Image Generation Options
   * @param page {Page} Browser tab
   * @param baseFormat {string} Image Format
   * @param valueWanted {boolean} Checked or not
   * @returns {Promise<string>}
   */
  async setBaseFormatChecked(page: Page, baseFormat: string, valueWanted: boolean): Promise<string> {
    await this.setChecked(page, this.checkboxBaseFormat(baseFormat), valueWanted);
    await page.locator(this.submitImageGenerationOptions).click();

    // Return successful message
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Returns the selected value of the select "Image" in "Regenerate thumbnails" form
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getRegenerateThumbnailsImage(page: Page): Promise<string> {
    return page.locator(this.selectRegenerateThumbnailsImage).evaluate((el: HTMLSelectElement) => el.value);
  }

  /**
   * Returns values of the select "Format" in "Regenerate thumbnails" form
   * @param page {Page} Browser tab
   * @param image {ImageTypeRegeneration|ImageTypeRegenerationSpecific} Image
   * @returns {Promise<string[]>}
   */
  async getRegenerateThumbnailsFormats(
    page: Page,
    image: ImageTypeRegeneration|ImageTypeRegenerationSpecific,
  ): Promise<string[]> {
    await this.selectByValue(page, this.selectRegenerateThumbnailsImage, image);

    await this.waitForVisibleSelector(page, this.selectRegenerateThumbnailsFormat);

    return (await page
      .locator(this.optionSelectRegenerateThumbnailsFormat)
      .allTextContents())
      .filter((el: string|null): el is string => (el !== null && el !== 'All'));
  }
}

export default new ImageSettings();
