import {Page} from 'playwright';
// Import FO Pages
import {BlockCartModal} from '@pages/FO/classic/modal/blockCart';

/**
 * Block cart modal, contains functions that can be used on the modal
 * @class
 * @extends BlockCartModal
 */
class BlockCart extends BlockCartModal {
  private readonly blockCartModalSummary: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on home page
   */
  constructor() {
    super('hummingbird');

    // Block Cart Modal
    this.cartModalCheckoutLink = `${this.blockCartModalDiv} div.cart-footer-actions a`;
    this.continueShoppingButton = `${this.blockCartModalDiv} div.cart-footer-actions button`;
    this.blockCartModalCloseButton = `${this.blockCartModalDiv} button.btn-close`;
    this.blockCartModalSummary = '.blockcart-modal__summery';
    this.cartModalProductsCountBlock = `${this.blockCartModalSummary} p`;
    this.cartModalSubtotalBlock = `${this.blockCartModalSummary} .product-subtotal .subtotals.value`;
    this.cartModalShippingBlock = `${this.blockCartModalSummary} .product-shipping .shipping.value`;
    this.cartModalProductTaxInclBlock = `${this.blockCartModalSummary} .product-total .value`;
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

export default new BlockCart();
