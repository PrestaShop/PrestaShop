const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {OnBoarding} = require('../../../selectors/BO/onboarding.js');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
let promise = Promise.resolve();

scenario('Welcome Module', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');

  scenario('First tutorial step : Create the first product ', () => {
    scenario('Step 1/5', client => {
      test('should check the existing of the onboarding tab ', () => {
        return promise
          .then(() => client.isVisible(OnBoarding.welcome_modal))
          .then(() => client.clickOnResumeButton(OnBoarding.resume_button))

      });
      test('should click on "Start" button', () => client.waitForExistAndClick(OnBoarding.start_button));
      test('should check that the current step has started', () => client.checkAttributeValue(OnBoarding.welcomeSteps.tutorial_step.replace("%P", '0'), 'class', 'id -done'));
      test('should check the existence of the onboarding-tooltip', () => client.isExisting(OnBoarding.welcomeSteps.onboarding_tooltip, 2000));
      test('should check the first onboarding-tooltip message', () => client.checkTextValue(OnBoarding.welcomeSteps.message_value, 'Give your product a catchy name.'));
      test('should check that the step number is equal to "1" ', () => client.checkTextValue(OnBoarding.welcomeSteps.tooltip_step, '1/5'));
      test('should set the "Product name" input', () => client.waitAndSetValue(AddProductPage.product_name_input, 'productTest'));
    }, 'common_client');

    scenario('Step 2/5', client => {
      test('should click on "Next" button', () => client.waitForExistAndClick(OnBoarding.welcomeSteps.next_button));
      test('should check that the step number is equal to "2"', () => client.checkTextValue(OnBoarding.welcomeSteps.tooltip_step, '2', 'contain', 2000));
      test('should check the second onboarding-tooltip message', () => client.checkTextValue(OnBoarding.welcomeSteps.message_value, 'Fill out the essential details in this tab. The other tabs are for more advanced information.'));
      test('should select "Product with combinations"', () => client.waitForExistAndClick(AddProductPage.variations_type_button));
    }, 'common_client');

    scenario('Step 3/5 ', client => {
      test('should click on "Next" button', () => client.waitForExistAndClick(OnBoarding.welcomeSteps.next_button));
      test('should check that the step number is equal to "3" ', () => client.checkTextValue(OnBoarding.welcomeSteps.tooltip_step, '3', 'contain', 2000));
      test('should check the third  onboarding-tooltip message', () => client.checkTextValue(OnBoarding.welcomeSteps.message_value, 'Add one or more pictures so your product looks tempting!'));
      test('should upload the picture of product', () => client.uploadPicture('image_test.jpg', AddProductPage.picture));
    }, 'common_client');

    scenario('Step 4/5', client => {
      test('should click on "Next" button', () => client.scrollWaitForExistAndClick(OnBoarding.welcomeSteps.next_button));
      test('should check that the step number is equal to "4" ', () => client.checkTextValue(OnBoarding.welcomeSteps.tooltip_step, '4', 'contain', 2000));
      test('should check the fourth  onboarding-tooltip message', () => client.checkTextValue(OnBoarding.welcomeSteps.message_value, 'How much do you want to sell it for?'));
      test('should set the "Tax exclude" price', () => {
        return promise
          .then(() => client.scrollTo(AddProductPage.priceTE_shortcut, 50))
          .then(() => client.waitAndSetValue(AddProductPage.priceTE_shortcut, '50'));
      });
    }, 'common_client');
    scenario('Step 5/5', client => {
      test('should click on "Next" button', () => client.waitForExistAndClick(OnBoarding.welcomeSteps.next_button));
      test('should check that the step number is equal to "5"', () => client.checkTextValue(OnBoarding.welcomeSteps.tooltip_step, '5', 'contain', 3000));
      test('should check the fifth onboarding-tooltip message', () => client.checkTextValue(OnBoarding.welcomeSteps.message_value, 'Yay! You just created your first product. Looks good, right?'));
    }, 'common_client');
  }, 'common_client');

  scenario(' Second Tutorial step : Give the shop an own identity', () => {
    scenario('Step 1/2', client => {
      test('should check that the current step has started', () => client.checkAttributeValue(OnBoarding.welcomeSteps.tutorial_step.replace("%P", '1'), 'class', 'id -done'));
      test('should click on "Next" button', () => client.scrollWaitForExistAndClick(OnBoarding.welcomeSteps.next_button));
      test('should check that the step number is equal to "1" ', () => client.checkTextValue(OnBoarding.welcomeSteps.tooltip_step, '1/2', 'contain', 1000));
      test('should check the first  onboarding-tooltip message', () => client.checkTextValue(OnBoarding.welcomeSteps.message_value, 'A good way to start is to add your own logo here!'));
      test('should upload the header logo', () => client.uploadPicture('image_test.jpg', OnBoarding.welcomeSteps.header_logo));
      test('should click on "Next" button', () => client.scrollWaitForExistAndClick(OnBoarding.welcomeSteps.next_button));
    }, 'common_client');

    scenario('Step 2/2 ', client => {
      test('should check that the step number is equal to "2" ', () => client.checkTextValue(OnBoarding.welcomeSteps.tooltip_step, '2/2', 'contain', 2000));
      test('should check the second onboarding-tooltip message', () => client.checkTextValue(OnBoarding.welcomeSteps.message_value, 'If you want something really special, have a look at the theme catalog!'));
      test('should click on "Next" button', () => client.waitForExistAndClick(OnBoarding.welcomeSteps.next_button));
    }, 'common_client');
  }, 'common_client');

  scenario('Third tutorial step : Get the shop ready for payments', () => {
    scenario('Step 1/2 ', client => {
      test('should check that the current step has started', () => client.checkAttributeValue(OnBoarding.welcomeSteps.tutorial_step.replace("%P", '2'), 'class', 'id -done', 'equal'));
      test('should check that the step number is equal to "1"', () => client.checkTextValue(OnBoarding.welcomeSteps.tooltip_step, '1/2', 'contain', 2000));
      test('should check the first onboarding-tooltip message', () => client.checkTextValue(OnBoarding.welcomeSteps.message_value, 'These payment methods are already available to your customers.', 'equal', 2000));
      test('should click on "Next" button', () => client.waitForExistAndClick(OnBoarding.welcomeSteps.next_button));
    }, 'common_client');

    scenario('Step 2/2 ', client => {
      test('should check that the step number is " 2/2"', () => client.checkTextValue(OnBoarding.welcomeSteps.tooltip_step, '2/2', 'contain', 2000));
      test('should check the second onboarding-tooltip message', () => client.checkTextValue(OnBoarding.welcomeSteps.message_value, 'And you can choose to add other payment methods from here!'));
    }, 'common_client');
  }, 'common_client');

  scenario('Fourth tutorial step : Choose the shipping solutions', client => {
    test('should check that the current step has started', () => client.checkAttributeValue(OnBoarding.welcomeSteps.tutorial_step.replace("%P", '3'), 'class', 'id -done', 'equal'));
    test('should click on "Next" button', () => client.waitForExistAndClick(OnBoarding.welcomeSteps.next_button));
    test('should discover the types of "Carriers" ', () => client.checkTextValue(OnBoarding.welcomeSteps.tooltip_step, '1/2', 'contain', 3000));
    test('should click on "Next" button', () => client.scrollWaitForExistAndClick(OnBoarding.welcomeSteps.next_button));
    test('should check that the step number is "2/2" ', () => client.checkTextValue(OnBoarding.welcomeSteps.tooltip_step, '2/2', 'contain', 2000));
    test('should check that the fifth tutorial step is done', () => client.checkAttributeValue(OnBoarding.welcomeSteps.tutorial_step.replace("%P", '4'), 'class', 'id -done', 'equal'));
  }, 'common_client');

  scenario('The fifth tutorial step ," Get the shop ready for payments" ', () => {
    scenario('Step 1/2 " Get the shop ready for payments" ', client => {
      test('should click on "Next" button', () => client.waitForExistAndClick(OnBoarding.welcomeSteps.next_button));
      test('should check that the step number is equal to "1" ', () => client.checkTextValue(OnBoarding.welcomeSteps.tooltip_step, '1/2', 'contain', 2000));
      test('should click on "Next" button', () => client.waitForExistAndClick(OnBoarding.welcomeSteps.next_button));
      test('should click on "Ready" button', () => client.waitForExistAndClick(OnBoarding.ready_button, 1000));
    }, 'common_client');
  }, 'common_client');
}, 'common_client', true);
