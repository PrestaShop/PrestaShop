const {OnBoarding} = require('../../selectors/BO/onboarding.js');

let promise = Promise.resolve();

module.exports = {
    findAndCloseWelcomeModal: function (clientType = 'order') {
        scenario('Close the onboarding modal if exist ', client => {
            test('should close the onboarding modal if exist', () => {
              return promise
                .then(() => client.isVisible(OnBoarding.welcome_modal))
                .then(() => client.closeBoarding(OnBoarding.popup_close_button))
            });
            test('should close the Onboarding on the navBar if exist', () => {
              return promise
                .then(() => client.isVisible(OnBoarding.onboarding_navbar))
                .then(() => client.closeBoarding(OnBoarding.onboarding_navbar_stop_button));
            });
        }, clientType);
    }
};
