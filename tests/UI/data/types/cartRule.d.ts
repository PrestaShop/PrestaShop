import type ProductData from '@data/faker/product';

import {
  // Import data
  type FakerCustomer,
} from '@prestashop-core/ui-testing';

type CartRuleCreator = {
  name?: string
  description?: string
  code?: string
  generateCode?: boolean
  highlight?: boolean
  partialUse?: boolean
  priority?: number
  status?: boolean
  customer?: FakerCustomer | null
  dateFrom?: string | null
  dateTo?: string | null
  minimumAmount?: CartRuleMinimalAmount
  quantity?: number
  quantityPerUser?: number
  carrierRestriction?: boolean
  countrySelection?: boolean
  countryIDToRemove?: number
  customerGroupSelection?: boolean
  productSelection?: boolean
  productSelectionNumber?: number
  productRestriction?: CartRuleProductSelection[]
  freeShipping?: boolean
  discountType?: string
  discountPercent?: number
  discountAmount?: CartRuleDiscountAmount
  applyDiscountTo?: string
  product?: string | null
  excludeDiscountProducts?: boolean
  freeGift?: boolean
  freeGiftProduct?: ProductData | null
};

type CartRuleDiscountAmount = {
  value: number,
  currency: string,
  tax: string,
}
type CartRuleMinimalAmount = {
  value: number,
  currency: string,
  tax: string,
  shipping: string,
}

type CartRuleProductSelection = {
  quantity: number,
  ruleType: string,
  value: number,
}

export {
  CartRuleCreator,
  CartRuleDiscountAmount,
  CartRuleMinimalAmount,
  CartRuleProductSelection,
};
