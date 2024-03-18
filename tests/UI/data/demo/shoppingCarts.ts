import Carriers from '@data/demo/carriers';
import ShoppingCartData from '@data/faker/shoppingCart';

import {
  // Import data
  dataCustomers,
} from '@prestashop-core/ui-testing';

export default [
  new ShoppingCartData({
    id: 1,
    orderID: 1,
    customer: dataCustomers.johnDoe,
    carrier: Carriers.myCarrier,
    online: false,
  }),
  new ShoppingCartData({
    id: 2,
    orderID: 2,
    customer: dataCustomers.johnDoe,
    carrier: Carriers.myCarrier,
    online: false,
  }),
  new ShoppingCartData({
    id: 3,
    orderID: 3,
    customer: dataCustomers.johnDoe,
    carrier: Carriers.myCarrier,
    online: false,
  }),
  new ShoppingCartData({
    id: 4,
    orderID: 4,
    customer: dataCustomers.johnDoe,
    carrier: Carriers.myCarrier,
    online: false,
  }),
  new ShoppingCartData({
    id: 5,
    orderID: 5,
    customer: dataCustomers.johnDoe,
    carrier: Carriers.myCarrier,
    online: false,
  }),
];
