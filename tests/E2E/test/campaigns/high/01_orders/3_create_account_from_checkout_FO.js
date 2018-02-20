const {AccessPageFO} = require('../../../selectors/FO/access_page');
const common_scenarios = require('./order');

scenario('Create account from checkout in Front Office', () => {
  scenario('Open the browser and access to the Front Office', client => {
    test('should open the browser', () => client.open());
    test('should access to the Front Office', () => client.accessToFO(AccessPageFO));
  }, 'order');
  common_scenarios.createOrder("create_account");
}, 'order', true);
