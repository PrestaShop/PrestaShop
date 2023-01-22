import type CustomerData from '@data/faker/customer';
import type ProductData from '@data/faker/product';

type CartRuleCreator = {
  name?: string
  description?: string
  code?: string
  generateCode?: boolean
  highlight?: boolean
  partialUse?: boolean
  priority?: number
  status?: boolean
  customer?: CustomerData|null
  dateFrom?: string|null
  dateTo?: string|null
  minimumAmount?: CartRuleMinimalAmount
  quantity?: number
  quantityPerUser?: number
  carrierRestriction?: boolean
  countrySelection?: boolean
  countryIDToRemove?: number
  customerGroupSelection?: boolean
  freeShipping?: boolean
  discountType?: string
  discountPercent?: number
  discountAmount?: CartRuleDiscountAmount
  applyDiscountTo?: string
  product?: string|null
  excludeDiscountProducts?: boolean
  freeGift?: boolean
  freeGiftProduct?: ProductData|null
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

export {
  CartRuleCreator,
  CartRuleDiscountAmount,
  CartRuleMinimalAmount,
};
