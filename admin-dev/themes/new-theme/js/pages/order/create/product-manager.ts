/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

import CartEditor, {CartProduct} from '@pages/order/create/cart-editor';
import createOrderMap from '@pages/order/create/create-order-map';
import eventMap from '@pages/order/create/event-map';
import {EventEmitter} from '@components/event-emitter';
import ProductRenderer from '@pages/order/create/product-renderer';
import Router from '@components/router';

const {$} = window;

/* eslint-disable */
interface SearchParams {
  currency_id?: string;
  search_phrase: string;
}
/* eslint-enable */

/**
 * Product component Object for "Create order" page
 */
export default class ProductManager {
  products: Array<Record<string, any>>;

  selectedProduct: null | Record<string, any>;

  selectedCombinationId: null | string | number;

  activeSearchRequest: JQuery.jqXHR | null;

  productRenderer: ProductRenderer;

  router: Router;

  cartEditor: CartEditor;

  constructor() {
    this.products = [];
    this.selectedProduct = null;
    this.selectedCombinationId = null;
    this.activeSearchRequest = null;

    this.productRenderer = new ProductRenderer();
    this.router = new Router();
    this.cartEditor = new CartEditor();

    this.initListeners();
  }

  addProductToCart(cartId: number): void {
    this.cartEditor.addProduct(cartId, this.getProductData());
  }

  removeProductFromCart(cartId: number, product: CartProduct): void {
    this.cartEditor.removeProductFromCart(cartId, product);
  }

  changeProductPrice(
    cartId: number,
    customerId: number,
    updatedProduct: CartProduct,
  ): void {
    this.cartEditor.changeProductPrice(cartId, customerId, updatedProduct);
  }

  changeProductQty(cartId: number, updatedProduct: CartProduct): void {
    this.cartEditor.changeProductQty(cartId, updatedProduct);
  }

  /**
   * Initializes event listeners
   *
   * @private
   */
  private initListeners(): void {
    $(createOrderMap.productSelect).on('change', (e: JQueryEventObject) => this.initProductSelect(e),
    );
    $(createOrderMap.combinationsSelect).on('change', (e: JQueryEventObject) => this.initCombinationSelect(e),
    );

    this.onProductSearch();
    this.onAddProductToCart();
    this.onRemoveProductFromCart();
    this.onProductPriceChange();
    this.onProductQtyChange();
  }

  /**
   * Listens for product search event
   *
   * @private
   */
  private onProductSearch(): void {
    EventEmitter.on(eventMap.productSearched, (response) => {
      this.products = response.products;
      this.productRenderer.renderSearchResults(this.products);
      this.selectFirstResult();
    });
  }

  /**
   * Listens for add product to cart event
   *
   * @private
   */
  private onAddProductToCart(): void {
    // on success
    EventEmitter.on(eventMap.productAddedToCart, (cartInfo) => {
      this.productRenderer.cleanCartBlockAlerts();
      this.updateStockOnProductAdd();
      EventEmitter.emit(eventMap.cartLoaded, cartInfo);
    });

    // on failure
    EventEmitter.on(eventMap.productAddToCartFailed, (errorMessage) => {
      this.productRenderer.renderCartBlockErrorAlert(errorMessage);
    });
  }

  /**
   * Listens for remove product from cart event
   *
   * @private
   */
  private onRemoveProductFromCart(): void {
    EventEmitter.on(eventMap.productRemovedFromCart, (data) => {
      this.updateStockOnProductRemove(data.product);
      EventEmitter.emit(eventMap.cartLoaded, data.cartInfo);
    });
  }

  /**
   * Listens for product price change in cart event
   *
   * @private
   */
  private onProductPriceChange(): void {
    EventEmitter.on(eventMap.productPriceChanged, (cartInfo) => {
      this.productRenderer.cleanCartBlockAlerts();
      EventEmitter.emit(eventMap.cartLoaded, cartInfo);
    });
  }

  /**
   * Listens for product quantity change in cart success/failure event
   *
   * @private
   */
  private onProductQtyChange(): void {
    const enableQtyInputs = () => {
      const inputsQty = <NodeListOf<HTMLInputElement>>(
        document.querySelectorAll(createOrderMap.listedProductQtyInput)
      );

      inputsQty.forEach((inputQty) => {
        // eslint-disable-next-line
        inputQty.disabled = false;
      });
    };

    // on success
    EventEmitter.on(eventMap.productQtyChanged, (data) => {
      this.productRenderer.cleanCartBlockAlerts();
      this.updateStockOnQtyChange(data.product);

      $(createOrderMap.createOrderButton).prop('disabled', false);
      EventEmitter.emit(eventMap.cartLoaded, data.cartInfo);

      enableQtyInputs();
    });

    // on failure
    EventEmitter.on(eventMap.productQtyChangeFailed, (e) => {
      this.productRenderer.renderCartBlockErrorAlert(e.responseJSON.message);
      $(createOrderMap.createOrderButton).prop('disabled', true);
      enableQtyInputs();
    });
  }

  /**
   * Initializes product select
   *
   * @param event
   *
   * @private
   */
  private initProductSelect(event: JQueryEventObject): void {
    const productId = Number(
      $(event.currentTarget)
        .find(':selected')
        .val(),
    );
    this.selectProduct(productId);
  }

  /**
   * Initializes combination select
   *
   * @param event
   *
   * @private
   */
  private initCombinationSelect(event: JQueryEventObject): void {
    const combinationId = Number(
      $(event.currentTarget)
        .find(':selected')
        .val(),
    );
    this.selectCombination(combinationId);
  }

  /**
   * Searches for product
   */
  search(searchPhrase: string): void {
    // Search only if the search phrase length is greater than 2 characters
    if (searchPhrase.length < 2) {
      return;
    }

    this.productRenderer.renderSearching();
    if (this.activeSearchRequest !== null) {
      this.activeSearchRequest.abort();
    }

    const params: SearchParams = {
      search_phrase: searchPhrase,
    };

    if (
      $(createOrderMap.cartCurrencySelect).data('selectedCurrencyId')
      !== undefined
    ) {
      params.currency_id = $(createOrderMap.cartCurrencySelect).data(
        'selectedCurrencyId',
      );
    }

    const $searchRequest = $.get(
      this.router.generate('admin_orders_products_search'),
      params,
    );
    this.activeSearchRequest = $searchRequest;

    $searchRequest
      .then((response) => {
        EventEmitter.emit(eventMap.productSearched, response);
      })
      .catch((response: JQuery.jqXHR) => {
        if (response.statusText === 'abort') {
          return;
        }

        window.showErrorMessage(response.responseJSON.message);
      });
  }

  /**
   * Initiate first result dataset after search
   *
   * @private
   */
  private selectFirstResult(): void {
    this.unsetProduct();

    if (this.products.length !== 0) {
      this.selectProduct(this.products[0].productId);
    }
  }

  /**
   * Handles use case when product is selected from search results
   *
   * @private
   *
   * @param {Number} productId
   */
  private selectProduct(productId: number): Record<string, any> {
    this.unsetCombination();

    const selectedProduct = Object.values(this.products).find(
      (product) => product.productId === productId,
    );

    if (selectedProduct) {
      this.selectedProduct = selectedProduct;
    }

    this.productRenderer.renderProductMetadata(
      <Record<string, any>> this.selectedProduct,
    );
    // if product has combinations select the first else leave it null
    if (this.selectedProduct?.combinations.length !== 0) {
      this.selectCombination(
        Object.keys(this.selectedProduct?.combinations)[0],
      );
    }

    return <Record<string, any>> this.selectedProduct;
  }

  /**
   * Handles use case when new combination is selected
   *
   * @param combinationId
   *
   * @private
   */
  private selectCombination(
    combinationId: number | string,
  ): Record<string, any> {
    const combination = this.selectedProduct?.combinations[combinationId];

    this.selectedCombinationId = combinationId;
    this.productRenderer.renderStock(
      $(createOrderMap.inStockCounter),
      $(createOrderMap.quantityInput),
      combination.stock,
      this.selectedProduct?.availableOutOfStock || combination.stock <= 0,
    );

    return combination;
  }

  /**
   * Sets the selected combination id to null
   *
   * @private
   */
  private unsetCombination(): void {
    this.selectedCombinationId = null;
  }

  /**
   * Sets the selected product to null
   *
   * @private
   */
  private unsetProduct(): void {
    this.selectedProduct = null;
  }

  /**
   * Retrieves product data from product search result block fields
   *
   * @returns {Object}
   *
   * @private
   */
  private getProductData(): Record<string, any> {
    const $fileInputs = $(createOrderMap.productCustomizationContainer).find(
      'input[type="file"]',
    );
    const formData = new FormData(
      <HTMLFormElement>document.querySelector(createOrderMap.productAddForm),
    );
    const fileSizes: Record<string, any> = {};

    // adds key value pairs {input name: file size} of each file in separate object
    // in case formData size exceeds server settings.
    $.each($fileInputs, (key: number, input: any) => {
      if (input.files.length !== 0) {
        fileSizes[<string>$(input).data('customization-field-id')] = input.files[0].size;
      }
    });

    return {
      product: formData,
      fileSizes,
    };
  }

  /**
   * Updates the stock when the product is added to cart in "create new order" page
   *
   * @private
   */
  private updateStockOnProductAdd(): void {
    const {productId} = <Record<string, any>> this.selectedProduct;
    const attributeId = this.selectedCombinationId;
    const qty = -Number($(createOrderMap.quantityInput).val());

    this.updateStock(productId, <string>attributeId, qty);
  }

  /**
   * Updates the stock when the product is removed from cart in Orders/"create new order page"
   *
   * @private
   */
  private updateStockOnProductRemove(product: Record<string, any>): void {
    const {productId, attributeId, qtyToRemove} = product;
    const qty = qtyToRemove;

    this.updateStock(productId, attributeId, qty);
  }

  /**
   * Updates the stock when the quantity of product is changed from cart in Orders/"create new order page"
   *
   * @private
   */
  private updateStockOnQtyChange(product: Record<string, any>): void {
    const {
      productId, attributeId, prevQty, newQty,
    } = product;
    const qty = prevQty - newQty;

    this.updateStock(productId, attributeId, qty);
  }

  /**
   * Updates the stock in products object and renders the new stock
   *
   * @private
   */
  private updateStock(
    productId: number,
    attributeId: string | number,
    qty: number,
  ): void {
    const productKeys = Object.keys(this.products);
    const productValues = Object.values(this.products);

    for (let i = 0; i < productKeys.length; i += 1) {
      if (productValues[i].productId === productId) {
        const $template = this.productRenderer.cloneProductTemplate(
          productValues[i],
        );
        // Update the stock value  in products object
        productValues[i].stock += qty;

        // Update the stock also for combination */
        if (attributeId && attributeId > 0) {
          productValues[i].combinations[attributeId].stock += qty;
        }

        // Render the new stock value
        if (this.selectedProduct?.productId === productId) {
          if (this.selectedProduct.combinations.length === 0) {
            this.productRenderer.renderStock(
              $template.find(createOrderMap.listedProductQtyStock),
              $template.find(createOrderMap.listedProductQtyInput),
              productValues[i].stock,
              productValues[i].availableOutOfStock
                || productValues[i].availableStock <= 0,
            );
          } else if (
            attributeId
            && Number(this.selectedCombinationId) === Number(attributeId)
          ) {
            this.productRenderer.renderStock(
              $template.find(createOrderMap.listedProductQtyStock),
              $template.find(createOrderMap.listedProductQtyInput),
              productValues[i].combinations[attributeId].stock,
              productValues[i].availableOutOfStock
                || productValues[i].availableStock <= 0,
            );
          }
        }
        break;
      }
    }
  }
}
