const {AccessPageFO} = require('../../../selectors/FO/access_page');
const common_scenarios = require('./order');
scenario('Open the browser and access to the FO', () => {
    scenario('Open the browser and access to the FO', client => {
        test('should open the browser', () => client.open());
        test('should access to FO', () => client.accessToFO(AccessPageFO));
    }, 'order');
    common_scenarios.createOrder("create_account");
}, 'order', true);