const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {OnBoarding} = require('../../../selectors/BO/onboarding.js');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const common = require('../../common_scenarios/shop_parameters');
let promise = Promise.resolve();

scenario('Welcome Module', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');

  scenario('Start tutorial', client => {
    test('should click on "Resume" button or "Start" button', () => {
      return promise
        .then(() => client.checkResumeAndStartButton(OnBoarding.start_button, OnBoarding.resume_button))
        .then(() => {
          if (!global.startOnboarding) {
            common.resetWelcomeModule(OnBoarding);
          }
        })
        .then(() => common.onBoardingSteps(OnBoarding, AddProductPage));
    });
  }, 'onboarding');
}, 'common_client');
