const {Installation} = require('../../../selectors/BO/installation');
const {AccessPageFO} = require('../../../selectors/FO/access_page');
const common_installation = require('./common_installation');
scenario('The shop installation', client => {
    test('should open the browser', () => client.open());
    test('should log in install page ', () => client.localhost());

    common_installation.choosingLanguage(Installation, "en");
    common_installation.licenceAgreements(Installation);
    common_installation.systemCompatibility(Installation);
    common_installation.shopInformation(Installation, "france");
    common_installation.db_configuration(Installation);
    common_installation.installationCheck(Installation);
    common_installation.checkFinishedInstallation(Installation);

    scenario('Login to the Front Office', client => {
        test('should sign in FO', () => client.signInFO(AccessPageFO));
    }, 'installation');

}, 'installation',true);
