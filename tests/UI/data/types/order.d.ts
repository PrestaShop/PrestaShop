import Customer from '@data/types/customer';

type Order = {
  customer: Customer
  product: number
  productQuantity: number
  paymentMethod: string
}

export default Order;
