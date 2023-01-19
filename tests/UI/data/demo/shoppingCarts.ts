import Carriers from '@data/demo/carriers';
import Customers from '@data/demo/customers';
import ShoppingCartData from '@data/faker/shoppingCart';

export default [
  new ShoppingCartData({
    id: 1,
    orderID: 1,
    customer: Customers.johnDoe,
    carrier: Carriers.myCarrier,
    online: false,
  }),
  new ShoppingCartData({
    id: 2,
    orderID: 2,
    customer: Customers.johnDoe,
    carrier: Carriers.myCarrier,
    online: false,
  }),
  new ShoppingCartData({
    id: 3,
    orderID: 3,
    customer: Customers.johnDoe,
    carrier: Carriers.myCarrier,
    online: false,
  }),
  new ShoppingCartData({
    id: 4,
    orderID: 4,
    customer: Customers.johnDoe,
    carrier: Carriers.myCarrier,
    online: false,
  }),
  new ShoppingCartData({
    id: 5,
    orderID: 5,
    customer: Customers.johnDoe,
    carrier: Carriers.myCarrier,
    online: false,
  }),
];
