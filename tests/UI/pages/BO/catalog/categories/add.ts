import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';
import {
  type FakerCategory,
} from '@prestashop-core/ui-testing';

class AddCategory extends BOBasePage {
  public readonly pageTitleCreate: string;

  public readonly pageTitleEdit: string;

  private readonly nameInput: string;

  private readonly displayedToggleInput: (toggle: number) => string;

  private readonly descriptionIframe: string;

  private readonly categoryCoverImage: string;

  private readonly categoryThumbnailImage: string;

  private readonly metaTitleInput: string;

  private readonly metaDescriptionTextarea: string;

  private readonly linkRewriteInput: string;

  private readonly redirectionWhenNotDisplayedSelect: string;

  private readonly redirectedCategory: string;

  private readonly selectAllGroupAccessCheckbox: string;

  private readonly saveCategoryButton: string;

  private readonly rootCategoryNameInput: string;

  private readonly rootCategoryDisplayedToggleInput: (toggle: number) => string;

  private readonly rootCategoryDescriptionIframe: string;

  private readonly rootCategoryCoverImage: string;

  private readonly rootCategoryMetaTitleInput: string;

  private readonly rootCategoryMetaDescriptionTextarea: string;

  constructor() {
    super();

    this.pageTitleCreate = `New category • ${global.INSTALL.SHOP_NAME}`;
    this.pageTitleEdit = 'Editing category ';

    // Selectors
    this.nameInput = '#category_name_1';
    this.displayedToggleInput = (toggle: number) => `#category_active_${toggle}`;
    this.descriptionIframe = '#category_description_1_ifr';
    this.categoryCoverImage = '#category_cover_image';
    this.categoryThumbnailImage = '#category_thumbnail_image';
    this.metaTitleInput = '#category_meta_title_1';
    this.metaDescriptionTextarea = '#category_meta_description_1';
    this.linkRewriteInput = '#category_link_rewrite_1';
    this.redirectionWhenNotDisplayedSelect = '#category_redirect_option_type';
    this.redirectedCategory = '#category_redirect_option_target_search_input';
    this.selectAllGroupAccessCheckbox = '.js-choice-table-select-all';
    this.saveCategoryButton = '#save-button';

    // Selectors fo root category
    this.rootCategoryNameInput = '#root_category_name_1';
    this.rootCategoryDisplayedToggleInput = (toggle: number) => `#root_category_active_${toggle}`;
    this.rootCategoryDescriptionIframe = '#root_category_description_1_ifr';
    this.rootCategoryCoverImage = '#root_category_cover_image';
    this.rootCategoryMetaTitleInput = '#root_category_meta_title_1';
    this.rootCategoryMetaDescriptionTextarea = '#root_category_meta_description_1';
  }

  /*
  Methods
   */

  /**
   * Select all groups
   * @param page
   * @return {Promise<void>}
   */
  async selectAllGroups(page: Page): Promise<void> {
    if (!(await this.isChecked(page, this.selectAllGroupAccessCheckbox))) {
      const parentElement = await this.getParentElement(page, this.selectAllGroupAccessCheckbox);

      if (parentElement instanceof HTMLElement) {
        parentElement.click();
      }
    }
  }

  /**
   * Fill form for add/edit category
   * @param page {Page} Browser tab
   * @param categoryData {FakerCategory} Data to set on new/edit category form
   * @returns {Promise<string>}
   */
  async createEditCategory(page: Page, categoryData: FakerCategory): Promise<string> {
    await this.setValue(page, this.nameInput, categoryData.name);
    await this.setChecked(page, this.displayedToggleInput(categoryData.displayed ? 1 : 0));
    await this.setValueOnTinymceInput(page, this.descriptionIframe, categoryData.description);
    if (categoryData.coverImage) {
      await this.uploadFile(page, this.categoryCoverImage, categoryData.coverImage);
    }
    if (categoryData.thumbnailImage) {
      await this.uploadFile(page, this.categoryThumbnailImage, categoryData.thumbnailImage);
    }
    await this.setValue(page, this.metaTitleInput, categoryData.metaTitle);
    await this.setValue(page, this.metaDescriptionTextarea, categoryData.metaDescription);
    await page.locator(this.redirectionWhenNotDisplayedSelect).selectOption({
      value: categoryData.redirectionWhenNotDisplayed,
    });
    if (categoryData.redirectedCategory && ['301', '302'].includes(categoryData.redirectionWhenNotDisplayed)) {
      await this.searchOptionTarget(page, categoryData.redirectedCategory.name);
    }
    await this.selectAllGroups(page);

    // Save Category
    await this.clickAndWaitForURL(page, this.saveCategoryButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Edit home category
   * @param page {Page} Browser tab
   * @param categoryData {FakerCategory} Data to set on edit home category form
   * @returns {Promise<string>}
   */
  async editHomeCategory(page: Page, categoryData: FakerCategory): Promise<string> {
    await this.setValue(page, this.rootCategoryNameInput, categoryData.name);
    await this.setChecked(page, this.rootCategoryDisplayedToggleInput(categoryData.displayed ? 1 : 0));
    await this.setValueOnTinymceInput(page, this.rootCategoryDescriptionIframe, categoryData.description);
    await this.uploadFile(page, this.rootCategoryCoverImage, `${categoryData.name}.jpg`);
    await this.setValue(page, this.rootCategoryMetaTitleInput, categoryData.metaTitle);
    await this.setValue(page, this.rootCategoryMetaDescriptionTextarea, categoryData.metaDescription);
    await this.selectAllGroups(page);
    // Save Category
    await this.clickAndWaitForURL(page, this.saveCategoryButton);
    return this.getPageTitle(page);
  }

  /**
   * Search option target
   * @param page {Page} Browser tab
   * @param target {string} Target to search
   * @returns {Promise<void>}
   */
  private async searchOptionTarget(page: Page, target: string): Promise<void> {
    await page.locator(this.redirectedCategory).fill(target);
    await page.waitForTimeout(1000);
    await page.keyboard.press('ArrowDown');
    await page.keyboard.press('Enter');
  }

  /**
   * Return input value
   * @param page {Page}
   * @param inputName {string}
   */
  async getValue(page: Page, inputName: string): Promise<string> {
    switch (inputName) {
      case 'friendlyUrl':
        return page.inputValue(this.linkRewriteInput);
      default:
        throw new Error(`Input ${inputName} was not found`);
    }
  }
}

export default new AddCategory();
