import {
  // Import data
  type FakerCarrier,
  type FakerCustomer,
} from '@prestashop-core/ui-testing';

type ShoppingCartCreator = {
  id?: number
  orderID?: number
  customer?: FakerCustomer
  carrier?: FakerCarrier
  online?: boolean
}

type ShoppingCartDetails = {
  id_cart?: number
  id_order?: number
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
