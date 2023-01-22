import type Customer from '@data/types/customer';
import ProductData from '@data/faker/product';

type Order = {
  reference: string
  customer: Customer
  product: ProductData
  productId: number
  productQuantity: number
  paymentMethod: string

  // Discount
  giftDiscountValue: number
  percentDiscountValue: number
  atiPrice: number
}

type OrderHistory = {
  reference: string
  date: string
  price: string
  paymentType: string
  status: string
  invoice: string
}

export {Order, OrderHistory};
