import {Page} from 'playwright';
// Import FO Pages
import {QuickViewModal} from '@pages/FO/classic/modal/quickView';

/**
 * Quick view modal, contains functions that can be used on the page
 * @class
 * @extends QuickViewModal
 */
class QuickView extends QuickViewModal {
  /**
   * @constructs
   * Setting up texts and selectors to use on home page
   */
  constructor() {
    super('hummingbird');

    // Quick view modal
    this.productRowQuantityUpDownButton = (direction: string) => `div.product-actions__quantity button.js-${direction}-button`;
    this.quickViewProductName = `${this.quickViewModalDiv} .h3`;
    this.quickViewRegularPrice = `${this.quickViewModalDiv} span.product__price-regular`;
    this.quickViewProductPrice = `${this.quickViewModalDiv} div.product__current-price`;
    this.quickViewDiscountPercentage = `${this.quickViewModalDiv} div.product__discount-percentage`;
    this.quickViewTaxShippingDeliveryLabel = `${this.quickViewModalDiv} div.product__tax-label`;
    this.quickViewCoverImage = `${this.quickViewModalDiv} #product-images div.carousel-item.active img.img-fluid`;
    this.quickViewThumbImage = `${this.quickViewModalDiv} div.thumbnails__container img.img-fluid`;
    this.quickViewProductVariants = `${this.quickViewModalDiv} div.js-product-variants`;
    this.quickViewProductDimension = `${this.quickViewProductVariants} select#group_3`;
    this.quickViewProductSize = `${this.quickViewProductVariants} select#group_1`;
    this.quickViewProductColor = `${this.quickViewProductVariants} ul#group_2`;
    this.quickViewCloseButton = `${this.quickViewModalDiv} button.btn-close`;
  }

  /**
   * Get product details from quick view modal
   * @param page {Page} Browser tab
   * @returns {Promise<{thumbImage: string|null, price: number, taxShippingDeliveryLabel: string,
   * coverImage: string|null, name: string, shortDescription: string}>}
   */
  async getProductDetailsFromQuickViewModal(page: Page): Promise<{
    thumbImage: string | null,
    price: number,
    taxShippingDeliveryLabel: string,
    coverImage: string | null,
    name: string,
    shortDescription: string,
  }> {
    return {
      name: await this.getTextContent(page, this.quickViewProductName),
      price: parseFloat((await this.getTextContent(page, this.quickViewProductPrice)).replace('€', '')),
      taxShippingDeliveryLabel: await this.getTextContent(page, this.quickViewTaxShippingDeliveryLabel),
      shortDescription: await this.getTextContent(page, this.quickViewShortDescription),
      coverImage: await this.getAttributeContent(page, this.quickViewCoverImage, 'src'),
      thumbImage: await this.getAttributeContent(page, this.quickViewThumbImage, 'srcset'),
    };
  }

  /**
   * Returns the URL of the main image in the quickview
   * @param page {Page} Browser tab
   * @returns {Promise<string|null>}
   */
  async getQuickViewImageMain(page: Page): Promise<string | null> {
    return this.getAttributeContent(page, this.quickViewCoverImage, 'data-full-size-image-url');
  }

  /**
   * Select thumb image
   * @param page {Page} Browser tab
   * @param position {number} Position of the image
   * @returns {Promise<string>}
   */
  async selectThumbImage(page: Page, position: number): Promise<string> {
    await page.locator(this.quickViewThumbImagePosition(position)).click();
    await page.waitForTimeout(2000);

    return this.getAttributeContent(page, this.quickViewCoverImage, 'src');
  }
}

export default new QuickView();
