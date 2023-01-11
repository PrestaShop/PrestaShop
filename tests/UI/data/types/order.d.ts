import type Customer from '@data/types/customer';
import type {Product} from '@data/types/product';

type Order = {
  reference: string
  customer: Customer
  product: Product
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
