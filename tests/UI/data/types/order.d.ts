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
  atiPrice:number
}

export default Order;
