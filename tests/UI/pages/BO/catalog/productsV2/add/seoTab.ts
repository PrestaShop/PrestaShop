// Import pages
import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

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

  /**
   * @constructs
   * Setting up texts and selectors to use on Shipping tab
   */
  constructor() {
    super();

    // Selectors in stocks tab
    this.productMetaTitleInput = '#product_seo_meta_title';
    this.productMetaDescriptionInput = '#product_seo_meta_description';
    this.productLinkRewriteInput = '#product_seo_link_rewrite';
    this.productRedirectTypeSelect = '#product_seo_redirect_option_type';
    this.productRedirectProduct = '#product_seo_redirect_option_target_0_id';
  }

  /*
  Methods
   */

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
