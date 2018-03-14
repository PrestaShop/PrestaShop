const {AccessPageBO} = require('../../../selectors/BO/access_page');
const common_scenarios = require('../../common_scenarios/discount');

let cartRuleData = [
  {
    name: 'Percent',
    customer_email: 'pub@prestashop.com',
    minimum_amount: 20,
    type: 'percent',
    reduction: 50
  },
  {
    name: 'Amount',
    customer_email: 'pub@prestashop.com',
    minimum_amount: 20,
    type: 'amount',
    reduction: 20
  }
];

scenario('Create, edit, check and delete "Cart Rule" in the Back Office', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'discount');
  for (let i = 0; i < cartRuleData.length; i++) {
    common_scenarios.createCartRule(cartRuleData[i], 'code' + (i+1));
    common_scenarios.checkCartRule(cartRuleData[i], 'code' + (i+1));
    common_scenarios.editCartRule(cartRuleData[i]);
    common_scenarios.checkCartRule(cartRuleData[i], 'code' + (i+1));
    common_scenarios.deleteCartRule(cartRuleData[i].name);
  }
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'discount');
}, 'discount', true);