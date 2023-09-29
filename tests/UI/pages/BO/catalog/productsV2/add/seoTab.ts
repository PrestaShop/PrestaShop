// Import pages
import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';
import ProductData from "@data/faker/product";
import createProductPage from "@pages/BO/catalog/productsV2/add/index";

/**
 * SEO tab on new product V2 page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class SeoTab extends BOBasePage {
  private readonly productMetaTitleInput: string;

  private readonly productMetaDescriptionInput: string;

  private readonly productLinkRewriteInput: string;

  private readonly productRedirectTypeSelect: string;

  private readonly productRedirectProduct: string;

  private readonly tagInput: string;

  private readonly alertText: string;

  private readonly generateURLFromNameButton: string;

  private readonly redirectionWhenOfflineSelect: string;

  private readonly searchOptionTargetInput: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on Shipping tab
   */
  constructor() {
    super();

    // Selectors in seo tab
    this.productMetaTitleInput = '#product_seo_meta_title';
    this.productMetaDescriptionInput = '#product_seo_meta_description';
    this.productLinkRewriteInput = '#product_seo_link_rewrite';
    this.productRedirectTypeSelect = '#product_seo_redirect_option_type';
    this.productRedirectProduct = '#product_seo_redirect_option_target_0_id';
    this.tagInput = '#product_seo_tags_1-tokenfield';
    this.alertText = '#product_seo div.alert-danger div.alert-text';
    this.generateURLFromNameButton = '#product_seo button.reset-link-rewrite';
    this.redirectionWhenOfflineSelect = '#product_seo_redirect_option_type';
    this.searchOptionTargetInput = '#product_seo_redirect_option_target_search_input';
  }

  /*
  Methods
   */
  /**
   * Set meta title
   * @param page {Page} Browser tab
   * @param metaTitle {string} Meta title to set in the input
   * @returns {Promise<void>}
   */
  async setMetaTitle(page: Page, metaTitle: string): Promise<void> {
    await this.setValue(page, `${this.productMetaTitleInput}_1`, metaTitle);
  }

  /**
   * Set meta description
   * @param page {Page} Browser tab
   * @param metaDescription {string} Meta description to set in the input
   * @returns {Promise<void>}
   */
  async setMetaDescription(page: Page, metaDescription: string): Promise<void> {
    await this.setValue(page, `${this.productMetaDescriptionInput}_1`, metaDescription);
  }

  /**
   * Set friendly URL
   * @param page {Page} Browser tab
   * @param friendlyUrl {string} Friendly URL to set in the input
   * @returns {Promise<void>}
   */
  async setFriendlyUrl(page: Page, friendlyUrl: string): Promise<void> {
    await this.setValue(page, `${this.productLinkRewriteInput}_1`, friendlyUrl);
  }

  /**
   * Get error message of friendly URL
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getErrorMessageOfFriendlyUrl(page: Page): Promise<string> {
    await this.clickAndWaitForLoadState(page, createProductPage.saveProductButton);

    return this.getTextContent(page, this.alertText);
  }

  /**
   * Click on generate URL from name button
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async clickOnGenerateUrlFromNameButton(page: Page): Promise<void> {
    await page.locator(this.generateURLFromNameButton).click();
  }

  /**
   * Select redirect page
   * @param page {Page} Browser tab
   * @param redirectionPage {string} Redirect page to select
   * @returns {Promise<void>}
   */
  async selectRedirectionPage(page: Page, redirectionPage: string): Promise<void> {
    await this.selectByVisibleText(page, this.redirectionWhenOfflineSelect, redirectionPage);
  }

  /**
   * Search option target
   * @param page {Page} Browser tab
   * @param target {string} Target to search
   * @returns {Promise<void>}
   */
  async searchOptionTarget(page: Page, target: string): Promise<void> {
    await page.locator(this.searchOptionTargetInput).fill(target);
    await page.waitForTimeout(1000);
    await page.keyboard.press('ArrowDown');
    await page.keyboard.press('Enter');
  }

  /**
   * Set tag
   * @param page {Page} Browser tab
   * @param tag {string} tag to set in the input
   * @returns {Promise<void>}
   */
  async setTag(page: Page, tag: string): Promise<void> {
    await page.locator(this.tagInput).fill(tag);
    await page.keyboard.press('Enter');
  }

  /**
   * Returns the value of a form element
   * @param page {Page}
   * @param inputName {string}
   * @param languageId {string | undefined}
   */
  async getValue(page: Page, inputName: string, languageId?: string): Promise<string> {
    switch (inputName) {
      case 'id_type_redirected':
        return this.getAttributeContent(page, this.productRedirectProduct, 'value');
      case 'link_rewrite':
        return this.getAttributeContent(page, `${this.productLinkRewriteInput}_${languageId}`, 'value');
      case 'meta_description':
        return this.getTextContent(page, `${this.productMetaDescriptionInput}_${languageId}`, false);
      case 'meta_title':
        return this.getAttributeContent(page, `${this.productMetaTitleInput}_${languageId}`, 'value');
      case 'redirect_type':
        return page.$eval(this.productRedirectTypeSelect, (node: HTMLSelectElement) => node.value);
      default:
        throw new Error(`Input ${inputName} was not found`);
    }
  }
}

export default new SeoTab();
