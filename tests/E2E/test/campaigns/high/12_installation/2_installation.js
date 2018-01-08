const {Installation} = require('../../../selectors/BO/installation');
const {AccessPageFO} = require('../../../selectors/FO/access_page');
const common_installation = require('./common_installation');
scenario('The shop installation', client => {
    test('should open the browser', () => client.open());
    test('should log in install page ', () => client.localhost());
    common_installation.prestaShopInstall(Installation, "en", "United Kingdom");
    scenario('Login to the Front Office', client => {
        test('should sign in FO', () => client.signInFO(AccessPageFO));
    }, 'installation');

}, 'installation', true);
