// Import FO Pages
import FOBasePage from '@pages/FO/FObasePage';

// Import data
import {ProductAttribute} from '@data/types/product';

import type {Page} from 'playwright';

/**
 * Quick view modal, contains functions that can be used on the modal
 * @class
 * @extends FOBasePage
 */
class QuickViewModal extends FOBasePage {
  public quickViewModalDiv: string;

  protected quickViewCloseButton: string;

  protected quickViewProductName: string;

  protected quickViewRegularPrice: string;

  protected quickViewProductPrice: string;

  protected quickViewDiscountPercentage: string;

  protected quickViewTaxShippingDeliveryLabel: string;

  protected quickViewShortDescription: string;

  protected quickViewProductVariants: string;

  protected quickViewProductSize: string;

  protected quickViewProductColor: string;

  protected quickViewProductDimension: string;

  private readonly productAvailability: string;

  protected quickViewCoverImage: string;

  protected quickViewThumbImage: string;

  private readonly quickViewQuantityWantedInput: string;

  private readonly quickViewFacebookSocialSharing: string;

  private readonly quickViewTwitterSocialSharing: string;

  private readonly quickViewPinterestSocialSharing: string;

  private readonly addToCartButton: string;

  protected quickViewModalProductImageCover: string;

  protected productRowQuantityUpDownButton: (direction: string) => string;

  protected quickViewThumbImagePosition: (position: number) => string;

  /**
   * @constructs
   * Setting up texts and selectors to use on home page
   */
  constructor(theme: string = 'classic') {
    super(theme);

    // Quick View modal
    this.quickViewModalDiv = 'div[id*="quickview-modal"]';
    this.quickViewCloseButton = `${this.quickViewModalDiv} button.close`;
    this.productAvailability = '#product-availability';
    this.quickViewProductName = `${this.quickViewModalDiv} h1`;
    this.quickViewRegularPrice = `${this.quickViewModalDiv} span.regular-price`;
    this.quickViewProductPrice = `${this.quickViewModalDiv} div.current-price span.current-price-value`;
    this.quickViewDiscountPercentage = `${this.quickViewModalDiv} div.current-price span.discount-percentage`;
    this.quickViewTaxShippingDeliveryLabel = `${this.quickViewModalDiv} div.tax-shipping-delivery-label`;
    this.quickViewShortDescription = `${this.quickViewModalDiv} div#product-description-short`;
    this.quickViewProductVariants = `${this.quickViewModalDiv} div.product-variants`;
    this.quickViewProductSize = `${this.quickViewProductVariants} select#group_1`;
    this.quickViewProductColor = `${this.quickViewProductVariants} ul#group_2`;
    this.quickViewProductDimension = `${this.quickViewProductVariants} select#group_3`;
    this.quickViewCoverImage = `${this.quickViewModalDiv} img.js-qv-product-cover`;
    this.quickViewThumbImage = `${this.quickViewModalDiv} img.js-thumb.selected`;
    this.quickViewQuantityWantedInput = `${this.quickViewModalDiv} input#quantity_wanted`;
    this.quickViewFacebookSocialSharing = `${this.quickViewModalDiv} .facebook a`;
    this.quickViewTwitterSocialSharing = `${this.quickViewModalDiv} .twitter a`;
    this.quickViewPinterestSocialSharing = `${this.quickViewModalDiv} .pinterest a`;
    this.quickViewModalProductImageCover = `${this.quickViewModalDiv} div.product-cover picture`;
    this.addToCartButton = `${this.quickViewModalDiv} button[data-button-action='add-to-cart']`;
    this.productRowQuantityUpDownButton = (direction: string) => 'span.input-group-btn-vertical'
      + ` button.bootstrap-touchspin-${direction}`;
    this.quickViewThumbImagePosition = (position: number) => `${this.quickViewModalDiv} li:nth-child(${position}) img.js-thumb`;
  }

  /**
   * Is quick view product modal visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async isQuickViewProductModalVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.quickViewModalDiv, 2000);
  }

  /**
   * Change product quantity
   * @param page {Page} Browser tab
   * @param quantity {number} The product quantity to change
   * @returns {Promise<void>}
   */
  async setQuantity(page: Page, quantity: number | string): Promise<void> {
    await this.setValue(page, this.quickViewQuantityWantedInput, quantity);
  }

  /**
   * Change product attributes
   * @param page {Page} Browser tab
   * @param attributes {ProductAttribute} The attributes data (size, color, dimension)
   * @returns {Promise<void>}
   */
  async setAttribute(page: Page, attributes: ProductAttribute): Promise<void> {
    switch (attributes.name) {
      case 'color':
        await this.waitForSelectorAndClick(page, `${this.quickViewProductColor} input[title='${attributes.value}']`);
        await this.waitForVisibleSelector(
          page,
          `${this.quickViewProductColor} input[title='${attributes.value}'][checked]`,
        );
        break;
      case 'dimension':
        await Promise.all([
          page.waitForResponse((response) => response.url().includes('product&token=')),
          this.selectByVisibleText(page, this.quickViewProductDimension, attributes.value),
        ]);
        break;
      case 'size':
        await this.selectByVisibleText(page, this.quickViewProductSize, attributes.value);
        break;
      default:
        throw new Error(`${attributes.name} has not being in defined in "changeAttributes"`);
    }
  }

  /**
   * Click on add to cart button from quick view modal
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async addToCartByQuickView(page: Page): Promise<void> {
    await this.waitForSelectorAndClick(page, this.addToCartButton);
  }

  /**
   * Add product to cart with Quick view
   * @param page {Page} Browser tab
   * @param quantityWanted {number | string} Quantity to order
   * @return {Promise<void>}
   */
  async setQuantityAndAddToCart(page: Page, quantityWanted: number | string = 1): Promise<void> {
    await this.setQuantity(page, quantityWanted);
    await this.addToCartByQuickView(page);
  }

  /**
   * Change attributes and add to cart
   * @param page {Page} Browser tab
   * @param attributes {ProductAttribute[]} The attributes data (size, color)
   * @returns {Promise<void>}
   */
  async setAttributes(page: Page, attributes: ProductAttribute[]): Promise<void> {
    for (let i: number = 0; i < attributes.length; i++) {
      await this.setAttribute(page, attributes[i]);
    }
  }

  /**
   * Change attributes and add to cart
   * @param page {Page} Browser tab
   * @param attributes {ProductAttribute[]} The attributes data (size, color, quantity)
   * @param quantity {number} The attributes data (size, color, quantity)
   * @returns {Promise<void>}
   */
  async setAttributesAndAddToCart(page: Page, attributes: ProductAttribute[], quantity: number): Promise<void> {
    for (let i: number = 0; i < attributes.length; i++) {
      await this.setAttribute(page, attributes[i]);
    }
    await this.setQuantityAndAddToCart(page, quantity);
  }

  /**
   * Update quantity value arrow up down in quick view modal
   * @param page {Page} Browser tab
   * @param quantityWanted {number} Value to add/subtract from quantity
   * @param direction {string} Direction to click on
   * @returns {Promise<string>}
   */
  async setQuantityByArrowUpDown(page: Page, quantityWanted: number, direction: string): Promise<void> {
    const inputValue = await this.getProductQuantityFromQuickViewModal(page);
    const nbClick: number = Math.abs(inputValue - quantityWanted);

    for (let i = 0; i < nbClick; i++) {
      await page.locator(this.productRowQuantityUpDownButton(direction)).click();
    }
  }

  /**
   * Get product quantity from quick view modal
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getProductQuantityFromQuickViewModal(page: Page): Promise<number> {
    return parseInt(await page.locator(this.quickViewQuantityWantedInput).evaluate((node: HTMLSelectElement) => node.value), 10);
  }

  /**
   * Get product with discount details from quick view modal
   * @param page {Page} Browser tab
   * @returns {Promise<{discountPercentage: string, thumbImage: string|null, price: number, taxShippingDeliveryLabel: string,
   * regularPrice: number, coverImage: string|null, name: string, shortDescription: string}>}
   */
  async getProductWithDiscountDetailsFromQuickViewModal(page: Page): Promise<{
    discountPercentage: string,
    thumbImage: string | null,
    price: number,
    taxShippingDeliveryLabel: string,
    regularPrice: number,
    coverImage: string | null,
    name: string,
    shortDescription: string,
  }> {
    return {
      name: await this.getTextContent(page, this.quickViewProductName),
      regularPrice: parseFloat((await this.getTextContent(page, this.quickViewRegularPrice)).replace('€', '')),
      price: parseFloat((await this.getTextContent(page, this.quickViewProductPrice)).replace('€', '')),
      discountPercentage: await this.getTextContent(page, this.quickViewDiscountPercentage),
      taxShippingDeliveryLabel: await this.getTextContent(page, this.quickViewTaxShippingDeliveryLabel),
      shortDescription: await this.getTextContent(page, this.quickViewShortDescription),
      coverImage: await this.getAttributeContent(page, this.quickViewCoverImage, 'src'),
      thumbImage: await this.getAttributeContent(page, this.quickViewThumbImage, 'src'),
    };
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
      thumbImage: await this.getAttributeContent(page, this.quickViewThumbImage, 'src'),
    };
  }

  /**
   * Get selected attribute from quick view
   * @param page {Page} Browser tab
   * @param attribute {ProductAttribute} Attribute to get value
   * @returns {Promise<ProductAttribute[]>}
   */
  async getSelectedAttributesFromQuickViewModal(
    page: Page,
    attribute: ProductAttribute,
  ): Promise<ProductAttribute[]> {
    const attributes: ProductAttribute[] = [];

    if ('color' in attribute && 'size' in attribute) {
      attributes.push({
        name: 'size',
        value: await this.getAttributeContent(page, `${this.quickViewProductSize} option[selected]`, 'title'),
      });
      attributes.push({
        name: 'color',
        value: await this.getAttributeContent(page, `${this.quickViewProductColor} input[checked='checked']`, 'title'),
      });
    } else {
      attributes.push({
        name: 'dimension',
        value: await this.getAttributeContent(page, `${this.quickViewProductDimension} option[selected]`, 'title'),
      });
    }
    return attributes;
  }

  async getSelectedAttributes(page: Page): Promise<ProductAttribute[]> {
    return [
      {
        name: 'size',
        value: await this.getAttributeContent(page, `${this.quickViewProductSize} option[selected]`, 'title'),
      },
      {
        name: 'color',
        value: await this.getAttributeContent(page, `${this.quickViewProductColor} input[checked='checked']`, 'title'),
      },
    ];
  }

  /**
   * Get product attributes from quick view modal
   * @param page {Page} Browser tab
   * @returns {Promise<ProductAttribute[]>}
   */
  async getProductAttributesFromQuickViewModal(page: Page): Promise<ProductAttribute[]> {
    if (await this.elementVisible(page, this.quickViewProductSize, 1000)) {
      return [
        {
          name: 'size',
          value: await this.getTextContent(page, this.quickViewProductSize),
        },
        {
          name: 'color',
          value: await this.getTextContent(page, this.quickViewProductColor, false),
        },
      ];
    }
    return [
      {
        name: 'dimension',
        value: await this.getTextContent(page, this.quickViewProductDimension),
      },
    ];
  }

  /**
   * Get product availability text
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getProductAvailabilityText(page: Page): Promise<string> {
    return this.getTextContent(page, this.productAvailability);
  }

  /**
   * Is add to cart button disabled
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async isAddToCartButtonDisabled(page: Page): Promise<boolean> {
    return this.elementVisible(page, `${this.addToCartButton}[disabled]`, 1000);
  }

  /**
   * Is add to cart button enabled
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async isAddToCartButtonEnabled(page: Page): Promise<boolean> {
    return !await this.elementVisible(page, `${this.addToCartButton}[disabled]`, 1000);
  }

  /**
   * Returns the URL of the main image in the quickview
   * @param page {Page} Browser tab
   * @returns {Promise<string|null>}
   */
  async getQuickViewImageMain(page: Page): Promise<string | null> {
    return this.getAttributeContent(page, `${this.quickViewModalProductImageCover} source`, 'srcset');
  }

  /**
   * Select thumb image
   * @param page {Page} Browser tab
   * @param position {number} Position of the image
   * @returns {Promise<string>}
   */
  async selectThumbImage(page: Page, position: number): Promise<string> {
    await page.locator(this.quickViewThumbImagePosition(position)).click();
    await this.waitForVisibleSelector(page, `${this.quickViewThumbImagePosition(position)}.selected`);

    return this.getAttributeContent(page, this.quickViewCoverImage, 'src');
  }

  /**
   * Close quick view modal
   * @param page {Page} Browser tab
   * @param clickOutside {boolean} True if we need to click outside to close the modal
   * @returns {Promise<boolean>}
   */
  async closeQuickViewModal(page: Page, clickOutside: boolean = false): Promise<boolean> {
    if (clickOutside) {
      await page.mouse.click(5, 5);
    } else {
      await this.waitForSelectorAndClick(page, this.quickViewCloseButton);
    }

    return this.elementNotVisible(page, this.quickViewModalDiv, 1000);
  }

  /**
   * Go to social sharing link
   * @param page {Page} Browser tab
   * @param socialSharing {string} The social network name
   * @returns {Promise<string>}
   */
  async getSocialSharingLink(page: Page, socialSharing: string): Promise<string> {
    let selector;

    switch (socialSharing) {
      case 'Facebook':
        selector = this.quickViewFacebookSocialSharing;
        break;

      case 'Twitter':
        selector = this.quickViewTwitterSocialSharing;
        break;

      case 'Pinterest':
        selector = this.quickViewPinterestSocialSharing;
        break;

      default:
        throw new Error(`${socialSharing} was not found`);
    }

    return this.getAttributeContent(page, selector, 'href');
  }
}

const quickViewModal = new QuickViewModal();
export {quickViewModal, QuickViewModal};
