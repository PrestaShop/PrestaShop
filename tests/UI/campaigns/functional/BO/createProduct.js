require('module-alias/register');

// Import utils
const {createProductTest} = require('@commonTests/BO/createDeleteProduct');


const baseContext = 'functional_BO_orders_creditSlips_creditSlipOptions';

// Import faker data
const ProductFaker = require('@data/faker/product');

const product = new ProductFaker({
  name: 'New product',
  type: 'Standard product',
  taxRule: 'No tax',
  quantity: 20,
});

createProductTest(product, baseContext);
