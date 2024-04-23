import type ProductData from '@data/faker/product';

import {
  // Import data
  type FakerAddress,
  type FakerCustomer,
  type FakerOrderStatus,
  type FakerPaymentMethod,
} from '@prestashop-core/ui-testing';

type OrderCreator = {
  id?: number
  reference?: string
  newClient?: boolean
  customer?: FakerCustomer
  totalPaid?: number
  paymentMethod?: FakerPaymentMethod
  status?: FakerOrderStatus
  delivery?: string
  deliveryAddress?: FakerAddress
  invoiceAddress?: FakerAddress
  products?: OrderProduct[]
  discountPercentValue?: number
  discountGiftValue?: number
  totalPrice?: number
  deliveryOption?: OrderDeliveryOption
}

type OrderDeliveryOption = {
  name: string
  freeShipping: boolean
}

type OrderProduct = {
  product: ProductData
  quantity: number
}

type OrderHistory = {
  reference: string
  date: string
  price: string
  paymentType: string
  status: string
  invoice: string
}

type OrderHistoryMessage = {
  product: string
  message : string
}

type OrderMessage = {
  orderMessage: string
  displayToCustomer: boolean
  message : string
}

type OrderPayment = {
  date: string
  paymentMethod: string
  transactionID : number
  amount : number
  currency : string
}

type MerchandiseReturns = {
  orderReference: string
  fileName: string
  status: string
  dateIssued: string
}

type MerchandiseProductReturn = {
  quantity: number
}

export {
  MerchandiseProductReturn,
  MerchandiseReturns,
  OrderCreator,
  OrderDeliveryOption,
  OrderHistory,
  OrderHistoryMessage,
  OrderMessage,
  OrderPayment,
  OrderProduct,
};
