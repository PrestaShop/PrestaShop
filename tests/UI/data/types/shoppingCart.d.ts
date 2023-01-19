import type CarrierData from '@data/faker/carrier';
import type CustomerData from '@data/faker/customer';

type ShoppingCartCreator = {
  id?: number
  orderID?: number
  customer?: CustomerData
  carrier?: CarrierData
  online?: boolean
};

export default ShoppingCartCreator;
