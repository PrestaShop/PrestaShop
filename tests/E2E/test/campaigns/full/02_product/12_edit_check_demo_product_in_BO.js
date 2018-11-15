const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const {AccessPageBO} = require('../../../selectors/BO/access_page');
const commonScenarios = require('../../common_scenarios/product');

let productData = [
  {
    name: 'Pack Mug + Framed poster',
    type: 'pack',
    picture: 'home_default.jpg',
    quantity: '100',
    priceHT: '35.000000',
    priceTTC: '42',
    tax_rule: 'FR Taux standard (20%)',
    summary: 'Mug The Adventure Begins + Framed poster Today is a good day 40x60cm',
    description: '',
    feature: {
      name: '',
      predefined_value: '',
      customized_value: ''
    }
  }, {
    name: 'Mug Today is a good day',
    type: 'standard',
    picture: 'home_default.jpg',
    quantity: '300',
    priceHT: '11.900000',
    priceTTC: '14.28',
    tax_rule: 'FR Taux standard (20%)',
    summary: 'White Ceramic Mug. 325ml',
    description: 'Add an optimistic touch to your morning coffee and start the day in a good mood! 8,2cm diameter / 9,5cm height / 0.43kg. Dishwasher-proof.',
    feature: {
      name: 'Color',
      predefined_value: 'White',
      customized_value: ''
    }
  }, {
    name: 'Hummingbird notebook',
    type: 'combination',
    picture: 'home_default.jpg',
    show_variation: 'Product with combinations',
    quantity: '1200',
    priceHT: '12.900000',
    priceTTC: '15.48',
    tax_rule: 'FR Taux standard (20%)',
    summary: '120 sheets notebook with hard cover made of recycled cardboard. 16x22cm',
    description: 'The Mountain fox notebook is the best option to write down your most ingenious ideas. At work, at home or when traveling, its endearing design and manufacturing quality will make you feel like writing! 90 gsm paper / double spiral binding.',
    combination: {
      exist: true
    }
  }, {
    name: 'Hummingbird - Vector graphics',
    type: 'virtual',
    picture: 'home_default.jpg',
    quantity: '300',
    priceHT: '9.000000',
    priceTTC: '10.8',
    tax_rule: 'FR Taux standard (20%)',
    summary: 'Vector graphic, format: svg. Download for personal, private and non-commercial use.',
    description: 'You have a custom printing creative project? The vector graphic Mountain fox illustration can be used for printing purpose on any support, without size limitation.',
    feature: {
      name: '',
      predefined_value: '',
      customized_value: ''
    }
  }, {
    name: 'Customizable mug',
    type: 'customizable',
    picture: 'home_default.jpg',
    quantity: '300',
    priceHT: '13.900000',
    priceTTC: '16.68',
    tax_rule: 'FR Taux standard (20%)',
    summary: 'White Ceramic Mug. 325ml',
    description: 'Customize your mug with the text of your choice. A mood, a message, a quote... It\'s up to you! Maximum number of characters:',
    feature: {
      name: 'Color',
      predefined_value: 'White',
      customized_value: ''
    }
  }
];

scenario('Check the basic information of demo product in the Back Office', () => {

  scenario('Open the browser and login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'product/product');

  for(let i = 0; i < productData.length; i++) {
    commonScenarios.checkDemoProductBO(AddProductPage, productData[i]);
  }

  scenario('Logout successfully from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'product/product');
}, 'product/product', true);
