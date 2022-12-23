import type Customer from '@data/types/customer';
import type {Product} from '@data/types/product';

type Order = {
  customer: Customer
  product: Product
  productId: number
  productQuantity: number
  paymentMethod: string
}

export default Order;
