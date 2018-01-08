const {Installation} = require('../../../selectors/BO/installation');
const {AccessPageFO} = require('../../../selectors/FO/access_page');
const common_installation = require('./common_installation');
scenario('The shop installation', client => {
    scenario('Open the browser and connect installation interface', client => {
        test('should open the browser', () => client.open());
        test('should log in install page ', () => client.localhost());
    }, 'installation');
    common_installation.prestaShopInstall(Installation, "en", "france");
    scenario('Login to the Front Office', client => {
        test('should sign in FO', () => client.signInFO(AccessPageFO));
    }, 'installation');
}, 'installation',true);
