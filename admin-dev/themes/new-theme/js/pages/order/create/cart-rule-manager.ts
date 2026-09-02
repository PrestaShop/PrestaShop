/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import CartEditor from '@pages/order/create/cart-editor';
import CartRulesRenderer from '@pages/order/create/cart-rules-renderer';
import {EventEmitter} from '@components/event-emitter';
import eventMap from '@pages/order/create/event-map';
import Router from '@components/router';
import SummaryRenderer from '@pages/order/create/summary-renderer';
import ShippingRenderer from '@pages/order/create/shipping-renderer';
import ProductRenderer from '@pages/order/create/product-renderer';

const {$} = window;

/**
 * Responsible for searching cart rules and managing cart rules search block
 */
export default class CartRuleManager {
  activeSearchRequest: null | JQuery.jqXHR;

  router: Router;

  cartRulesRenderer: CartRulesRenderer;

  cartEditor: CartEditor;

  summaryRenderer: SummaryRenderer;

  shippingRenderer: ShippingRenderer;

  productRenderer: ProductRenderer;

  constructor() {
    this.activeSearchRequest = null;

    this.router = new Router();
    this.cartRulesRenderer = new CartRulesRenderer();
    this.cartEditor = new CartEditor();
    this.summaryRenderer = new SummaryRenderer();
    this.shippingRenderer = new ShippingRenderer();
    this.productRenderer = new ProductRenderer();

    this.initListeners();
  }

  /**
   * Initiates event listeners for cart rule actions
   *
   * @private
   */
  private initListeners(): void {
    this.onCartRuleSearch();
    this.onAddCartRuleToCart();
    this.onAddCartRuleToCartFailure();
    this.onRemoveCartRuleFromCart();
  }

  /**
   * Listens for cart rule search action
   *
   * @private
   */
  private onCartRuleSearch(): void {
    EventEmitter.on(eventMap.cartRuleSearched, (cartRules) => {
      this.cartRulesRenderer.renderSearchResults(cartRules);
    });
  }

  /**
   * Listens event of add cart rule to cart action
   *
   * @private
   */
  private onAddCartRuleToCart(): void {
    EventEmitter.on(eventMap.cartRuleAdded, (cartInfo) => {
      const cartIsEmpty = cartInfo.products.length === 0;
      this.cartRulesRenderer.renderCartRulesBlock(
        cartInfo.cartRules,
        cartIsEmpty,
      );
      this.productRenderer.renderList(cartInfo.products);
      this.shippingRenderer.render(cartInfo.shipping, cartIsEmpty);
      this.summaryRenderer.render(cartInfo);
    });
  }

  /**
   * Listens event when add cart rule to cart fails
   *
   * @private
   */
  private onAddCartRuleToCartFailure(): void {
    EventEmitter.on(eventMap.cartRuleFailedToAdd, (message) => {
      this.cartRulesRenderer.displayErrorMessage(message);
    });
  }

  /**
   * Listens event for remove cart rule from cart action
   *
   * @private
   */
  private onRemoveCartRuleFromCart(): void {
    EventEmitter.on(eventMap.cartRuleRemoved, (cartInfo) => {
      const cartIsEmpty = cartInfo.products.length === 0;
      this.shippingRenderer.render(cartInfo.shipping, cartIsEmpty);
      this.cartRulesRenderer.renderCartRulesBlock(
        cartInfo.cartRules,
        cartIsEmpty,
      );
      this.summaryRenderer.render(cartInfo);
      this.productRenderer.renderList(cartInfo.products);
    });
  }

  /**
   * Searches for cart rules by search phrase
   *
   */
  search(searchPhrase: string): void {
    if (this.activeSearchRequest !== null) {
      this.activeSearchRequest.abort();
    }

    this.activeSearchRequest = $.get(
      this.router.generate('admin_cart_rules_search'),
      {
        search_phrase: searchPhrase,
      },
    );

    this.activeSearchRequest
      .then((cartRules) => {
        EventEmitter.emit(eventMap.cartRuleSearched, cartRules);
      })
      .catch((e: JQuery.jqXHR) => {
        if (e.statusText === 'abort') {
          return;
        }

        window.showErrorMessage(e.responseJSON.message);
      });
  }

  addCartRuleToCart(cartRuleId: number, cartId: number): void {
    this.cartEditor.addCartRuleToCart(cartRuleId, cartId);
  }

  stopSearching(): void {
    this.cartRulesRenderer.hideResultsDropdown();
  }

  removeCartRuleFromCart(cartRuleId: number, cartId: number): void {
    this.cartEditor.removeCartRuleFromCart(cartRuleId, cartId);
  }
}
