import Products from '@data/demo/products';
import ProductData from '@data/faker/product';
import type {
  CartRuleCreator,
  CartRuleDiscountAmount,
  CartRuleMinimalAmount,
  CartRuleProductSelection,
} from '@data/types/cartRule';

import {
  // Import data
  FakerCustomer,
} from '@prestashop-core/ui-testing';

import {faker} from '@faker-js/faker';

const productsNames: string[] = Object.values(Products).map((product: ProductData) => product.name);

/**
 * Create new cart rule to use on creation cart rule form on BO
 * @class
 */
export default class CartRuleData {
  public readonly name: string;

  public readonly description: string;

  public readonly code: string;

  public readonly generateCode: boolean;

  public readonly highlight: boolean;

  public readonly partialUse: boolean;

  public readonly priority: number;

  public readonly status: boolean;

  public readonly customer: FakerCustomer | null;

  public readonly dateFrom: string | null;

  public readonly dateTo: string | null;

  public readonly minimumAmount: CartRuleMinimalAmount;

  public readonly quantity: number;

  public readonly quantityPerUser: number;

  public readonly carrierRestriction: boolean;

  public readonly countrySelection: boolean;

  public readonly countryIDToRemove: number;

  public readonly customerGroupSelection: boolean;

  public readonly productSelection: boolean;

  public readonly productSelectionNumber: number;

  public readonly productRestriction: CartRuleProductSelection[];

  public readonly freeShipping: boolean;

  public readonly discountType: string;

  public readonly discountPercent: number | null;

  public readonly discountAmount: CartRuleDiscountAmount | null;

  public readonly applyDiscountTo: string;

  public readonly product: string | null;

  public readonly excludeDiscountProducts: boolean;

  public readonly freeGift: boolean;

  public readonly freeGiftProduct: ProductData | null;

  /**
   * Constructor for class CartRuleData
   * @param cartRuleToCreate {Object} Could be used to force the value of some members
   */
  constructor(cartRuleToCreate: CartRuleCreator = {}) {
    // Information
    /** @type {string} Name of the cart rule */
    this.name = cartRuleToCreate.name || faker.commerce.department();

    /** @type {string} Name of the cart rule */
    this.description = faker.lorem.sentence();

    /** @type {string} Code to apply the cart rule */
    this.code = cartRuleToCreate.code || '';

    /** @type {boolean} True to generate code */
    this.generateCode = cartRuleToCreate.generateCode || false;

    /** @type {boolean} True to display cart rule highlight */
    this.highlight = cartRuleToCreate.highlight === undefined ? false : cartRuleToCreate.highlight;

    /** @type {boolean} True to enable partial use */
    this.partialUse = cartRuleToCreate.partialUse === undefined ? true : cartRuleToCreate.partialUse;

    /** @type {number} Priority of the cart rule */
    this.priority = cartRuleToCreate.priority || 1;

    /** @type {boolean} Status of the cart rule */
    this.status = cartRuleToCreate.status === undefined ? true : cartRuleToCreate.status;

    // Conditions
    /** @type {CustomerData|null} Specific customer for the cart rule or null to disable it */
    this.customer = cartRuleToCreate.customer || null;

    /** @type {string|null} Starting date for the cart rule or null to disable it */
    this.dateFrom = cartRuleToCreate.dateFrom || null;

    /** @type {string|null} Ending date for the cart rule or null to disable it */
    this.dateTo = cartRuleToCreate.dateTo || null;

    /** @type {CartRuleMinimalAmount} Minimum amount parameters */
    this.minimumAmount = cartRuleToCreate.minimumAmount || {
      value: 0,
      currency: 'EUR',
      tax: 'Tax included',
      shipping: 'Shipping included',
    };

    /** @type {number} Amount of times that cart rule could be used */
    this.quantity = cartRuleToCreate.quantity || 1;

    /** @type {number} Amount of times a user can use the cart rule */
    this.quantityPerUser = cartRuleToCreate.quantityPerUser || 1;

    /** @type {boolean} True to enable customer group on the cart rule */
    this.countrySelection = cartRuleToCreate.countrySelection || false;

    /** @type {number} Country id to remove in country selection */
    this.countryIDToRemove = cartRuleToCreate.countryIDToRemove || 8;

    /** @type {boolean} True to enable carrier restriction on the cart rule */
    this.carrierRestriction = cartRuleToCreate.carrierRestriction || false;

    /** @type {boolean} True to enable group selection on the cart rule */
    this.customerGroupSelection = cartRuleToCreate.customerGroupSelection || false;

    /** @type {boolean} True to enable product selection on the cart rule */
    this.productSelection = cartRuleToCreate.productSelection || false;

    /** @type {number} Number of product selection */
    this.productSelectionNumber = cartRuleToCreate.productSelectionNumber || 1;

    /** @type {CartRuleProductSelection[]} Values of product restriction */
    this.productRestriction = cartRuleToCreate.productRestriction || [];

    // Actions
    /** @type {boolean} True to enable free shipping on the cart rule */
    this.freeShipping = cartRuleToCreate.freeShipping === undefined ? false : cartRuleToCreate.freeShipping;

    /** @type {string} Discount type of the cart rule */
    this.discountType = cartRuleToCreate.discountType || 'None';

    /** @type {number|null} Discount percent for the cart rule */
    this.discountPercent = null;

    /** @type {CartRuleDiscountAmount|null} Discount amount values for the cart rule */
    this.discountAmount = null;

    if (this.discountType === 'Percent') {
      this.discountPercent = cartRuleToCreate.discountPercent || faker.number.int({min: 10, max: 80});
    } else if (this.discountType === 'Amount') {
      this.discountAmount = cartRuleToCreate.discountAmount || {
        value: 0,
        currency: 'EUR',
        tax: 'Tax included',
      };
    }

    /** @type {string} Object to apply discount on it */
    this.applyDiscountTo = cartRuleToCreate.applyDiscountTo || 'Order';

    /** @type {string|null} Name of the product to apply cart rule */
    this.product = null;
    if (this.applyDiscountTo === 'Specific product') {
      this.product = cartRuleToCreate.product || faker.helpers.arrayElement(productsNames);
    }

    /** @type {boolean} True to exclude discount of specific products */
    this.excludeDiscountProducts = cartRuleToCreate.excludeDiscountProducts === undefined
      ? false : cartRuleToCreate.excludeDiscountProducts;

    /** @type {boolean} True to enable free gift */
    this.freeGift = cartRuleToCreate.freeGift === undefined ? false : cartRuleToCreate.freeGift;

    /** @type {ProductData|null} Product to set for the free gift */
    this.freeGiftProduct = null;
    if (this.freeGift) {
      this.freeGiftProduct = cartRuleToCreate.freeGiftProduct || null;
    }
  }
}
