const {AccessPageBO} = require('../../../selectors/BO/access_page');
const commonScenarios = require('../../common_scenarios/discount');

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
    commonScenarios.createCartRule(cartRuleData[i], 'code' + (i+1));
    commonScenarios.checkCartRule(cartRuleData[i], 'code' + (i+1));
    commonScenarios.editCartRule(cartRuleData[i]);
    commonScenarios.checkCartRule(cartRuleData[i], 'code' + (i+1));
    commonScenarios.deleteCartRule(cartRuleData[i].name);
  }
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'discount');
}, 'discount', true);