const {Installation} = require('../../selectors/BO/installation');
const {AccessPageFO} = require('../../selectors/FO/access_page');
const commonInstallation = require('../common_scenarios/common_installation');
require('../../globals.js');

scenario('The shop installation', () => {
  scenario('Open the browser and connect installation interface', client => {
    test('should open the browser', async () => {
      await client.open();
      await client.startTracing('installShop');
    });
    test('should go to install page ', () => client.localhost(URL, install_directory));
  }, 'installation');

  commonInstallation.prestaShopInstall(Installation, install_language, install_country);

  scenario('Login to the Front Office', client => {
    test('should sign in FO', () => client.signInFO(AccessPageFO));
  }, 'installation');
}, 'installation', true);