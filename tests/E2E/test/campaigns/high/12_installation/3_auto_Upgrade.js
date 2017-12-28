const {Installation} = require('../../../selectors/BO/installation');
const {AccessPageFO} = require('../../../selectors/FO/access_page');
const common_installation = require('./common_installation');
scenario('The shop installation', client => {
    scenario('Open the browser and connect to the BO', client => {
        test('should open the browser', () => client.open());
        test('should log in successfully in BO', () => client.signInBO(AccessPageBO));
    }, 'installation');






    scenario('Logout from the back office', client => {
        test('should logout successfully from the Back Office', () => client.signOutBO());
    }, 'product/product');

}, 'installation',true);
