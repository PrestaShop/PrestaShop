import type CarrierData from '@data/faker/carrier';
import type CustomerData from '@data/faker/customer';

type ShoppingCartCreator = {
  id?: number
  orderID?: number
  customer?: CustomerData
  carrier?: CarrierData
  online?: boolean
}

type ShoppingCartDetails = {
  id_cart?: number
  status?: string
  lastname?: string
  total?: string
  carrier?: string
  date?: string
  online?: string
}

export {
  ShoppingCartCreator,
  ShoppingCartDetails,
};
