// Import FO Pages
import FOBasePage from '@pages/FO/FObasePage';

// Import data
import {ProductAttribute} from '@data/types/product';

import {
  type CartProductDetails,
} from '@prestashop-core/ui-testing';

import type {Page} from 'playwright';

/**
 * Block cart modal, contains functions that can be used on the modal
 * @class
 * @extends FOBasePage
 */
class BlockCartModal extends FOBasePage {
  private readonly blockCartLabel: string;

  protected readonly blockCartModalDiv: string;

  protected blockCartModalCloseButton: string;

  private readonly cartModalProductNameBlock: string;

  private readonly cartModalProductPriceBlock: string;

  private readonly cartModalProductSizeBlock: string;

  private readonly cartModalProductColorBlock: string;

  private readonly cartModalProductQuantityBlock: string;

  private readonly cartContentBlock: string;

  protected cartModalProductsCountBlock: string;

  protected cartModalShippingBlock: string;

  protected cartModalSubtotalBlock: string;

  protected cartModalProductTaxInclBlock: string;

  protected cartModalCheckoutLink: string;

  protected continueShoppingButton: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on home page
   */
  constructor(theme: string = 'classic') {
    super(theme);

    this.blockCartModalDiv = '#blockcart-modal';
    this.blockCartLabel = '#myModalLabel';
    this.blockCartModalCloseButton = `${this.blockCartModalDiv} button.close`;
    this.cartModalProductNameBlock = `${this.blockCartModalDiv} .product-name`;
    this.cartModalProductPriceBlock = `${this.blockCartModalDiv} .product-price`;
    this.cartModalProductSizeBlock = `${this.blockCartModalDiv} .size strong`;
    this.cartModalProductColorBlock = `${this.blockCartModalDiv} .color strong`;
    this.cartModalProductQuantityBlock = `${this.blockCartModalDiv} .product-quantity`;
    this.cartContentBlock = `${this.blockCartModalDiv} .cart-content`;
    this.cartModalProductsCountBlock = `${this.cartContentBlock} .cart-products-count`;
    this.cartModalShippingBlock = `${this.cartContentBlock} .shipping.value`;
    this.cartModalSubtotalBlock = `${this.cartContentBlock} .subtotal.value`;
    this.cartModalProductTaxInclBlock = `${this.cartContentBlock} .product-total .value`;
    this.cartModalCheckoutLink = `${this.blockCartModalDiv} div.cart-content-btn a`;
    this.continueShoppingButton = `${this.blockCartModalDiv} div.cart-content-btn button.btn-secondary`;
  }

  /**
   * Get block cart modal title
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getBlockCartModalTitle(page: Page): Promise<string> {
    return this.getTextContent(page, this.blockCartLabel);
  }

  /**
   * Is block cart modal visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async isBlockCartModalVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.blockCartModalDiv, 2000);
  }

  /**
   * Get product details from blockCart modal
   * @param page {Page} Browser tab
   * @returns {Promise<CartProductDetails>}
   */
  async getProductDetailsFromBlockCartModal(page: Page): Promise<CartProductDetails> {
    return {
      name: await this.getTextContent(page, this.cartModalProductNameBlock),
      price: parseFloat((await this.getTextContent(page, this.cartModalProductPriceBlock)).replace('€', '')),
      quantity: await this.getNumberFromText(page, this.cartModalProductQuantityBlock),
      cartProductsCount: await this.getNumberFromText(page, this.cartModalProductsCountBlock),
      cartSubtotal: parseFloat((await this.getTextContent(page, this.cartModalSubtotalBlock)).replace('€', '')),
      cartShipping: await this.getTextContent(page, this.cartModalShippingBlock),
      totalTaxIncl: parseFloat((await this.getTextContent(page, this.cartModalProductTaxInclBlock)).replace('€', '')),
    };
  }

  /**
   * Get product attributes from block cart modal
   * @param page {Page} Browser tab
   * @returns {Promise<ProductAttribute[]>}
   */
  async getProductAttributesFromBlockCartModal(page: Page): Promise<ProductAttribute[]> {
    return [
      {
        name: 'size',
        value: await this.getTextContent(page, this.cartModalProductSizeBlock),
      },
      {
        name: 'color',
        value: await this.getTextContent(page, this.cartModalProductColorBlock),
      },
    ];
  }

  /**
   * Click on proceed to checkout after adding product to cart (in modal homePage)
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async proceedToCheckout(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.cartModalCheckoutLink);
    await page.waitForLoadState('domcontentloaded');
  }

  /**
   * Click on continue shopping
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async continueShopping(page: Page): Promise<boolean> {
    await this.waitForSelectorAndClick(page, this.continueShoppingButton);
    return this.elementNotVisible(page, this.blockCartModalDiv, 2000);
  }

  /**
   * Close block cart modal
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async closeBlockCartModal(page: Page): Promise<boolean> {
    await this.waitForSelectorAndClick(page, this.blockCartModalCloseButton);

    return this.elementNotVisible(page, this.blockCartModalDiv, 1000);
  }
}

const blockCartModal = new BlockCartModal();
export {blockCartModal, BlockCartModal};
