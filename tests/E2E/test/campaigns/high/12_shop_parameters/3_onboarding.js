const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {OnBoarding} = require('../../../selectors/BO/onboarding.js');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const common = require('../../common_scenarios/shop_parameters');
const {ProductList} = require('../../../selectors/BO/add_product_page');
const {Menu} = require('../../../selectors/BO/menu.js');

let promise = Promise.resolve();

scenario('Welcome Module', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');

  scenario('Start tutorial', client => {
    test('should click on "Resume" button or "Start" button', () => client.checkResumeAndStartButton(OnBoarding.start_button, OnBoarding.resume_button));
    test('should check "MBO" module', () =>
      common.checkMboModule(client)
    );
    test('should reset "Welcome" module', () =>
      common.resetWelcomeModule(client)
    );
  }, 'onboarding');

  scenario('The first tutorial step : Create the first product ', () => {
    scenario('Step 1/5', client => {
      test('should click on "Start" or "Resume" button', () => {
        return promise
          .then(() => client.isVisible(OnBoarding.start_button, 1000))
          .then(() => {
            if (isVisible) {
              client.waitForExistAndClick(OnBoarding.start_button, 1000)
            } else {
              client.waitForExistAndClick(Menu.dashboard_menu, 3000);
              client.waitForExistAndClick(OnBoarding.resume_button, 2000);
              client.waitForExistAndClick(OnBoarding.start_button, 1000)
            }
          });
      });
      test('should check that the current step has started', () => client.checkAttributeValue(OnBoarding.welcomeSteps.tutorial_step.replace("%P", '0'), 'class', 'id -done'));
      test('should check the existence of the onboarding-tooltip', () => client.isExisting(OnBoarding.welcomeSteps.onboarding_tooltip, 4000));
      test('should check the first onboarding-tooltip message', () => client.checkTextValue(OnBoarding.welcomeSteps.message_value, 'Give your product a catchy name.'));
      test('should check that the step number is equal to "1"', () => client.checkTextValue(OnBoarding.welcomeSteps.tooltip_step, '1/5'));
      test('should set the "Product name" input', () => client.waitAndSetValue(AddProductPage.product_name_input, 'productTest' + date_time));
    }, 'common_client');

    scenario('Step 2/5', client => {
      test('should click on "Next" button', () => client.waitForExistAndClick(OnBoarding.welcomeSteps.next_button, 2000));
      test('should check that the step number is equal to "2"', () => client.checkTextValue(OnBoarding.welcomeSteps.tooltip_step, '2', 'contain', 2000));
      test('should check the second onboarding-tooltip message', () => client.checkTextValue(OnBoarding.welcomeSteps.message_value, 'Fill out the essential details in this tab. The other tabs are for more advanced information.'));
      test('should select "Product with combinations"', () => client.waitForExistAndClick(AddProductPage.variations_type_button));
    }, 'common_client');

    scenario('Step 3/5 ', client => {
      test('should click on "Next" button', () => client.waitForExistAndClick(OnBoarding.welcomeSteps.next_button, 2000));
      test('should check that the step number is equal to "3"', () => client.checkTextValue(OnBoarding.welcomeSteps.tooltip_step, '3', 'contain', 2000));
      test('should check the third  onboarding-tooltip message', () => client.checkTextValue(OnBoarding.welcomeSteps.message_value, 'Add one or more pictures so your product looks tempting!'));
      test('should upload the picture of product', () => client.uploadPicture('image_test.jpg', AddProductPage.picture));
    }, 'common_client');

    scenario('Step 4/5', client => {
      test('should click on "Next" button', () => client.scrollWaitForExistAndClick(OnBoarding.welcomeSteps.next_button));
      test('should check that the step number is equal to "4"', () => client.checkTextValue(OnBoarding.welcomeSteps.tooltip_step, '4', 'contain', 2000));
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
      test('should search for the product"' + 'productTest' + date_time + '"', () => {
        return promise
          .then(() => client.waitAndSetValue(AddProductPage.catalogue_filter_by_name_input, 'productTest' + date_time))
          .then(() => client.waitForExistAndClick(AddProductPage.catalogue_submit_filter_button));
      });
      test('should check the product', () => client.checkTextValue(ProductList.product_name.replace("%ID", '1'), 'productTest' + date_time));
      test('should click on "Reset" button', () => client.waitForExistAndClick(AddProductPage.catalog_reset_filter));
    }, 'common_client');
  }, 'common_client');
  scenario(' The second Tutorial step : Give the shop an own identity', () => {
    scenario('Step 1/2', client => {
      test('should check that the current step has started', () => client.checkAttributeValue(OnBoarding.welcomeSteps.tutorial_step.replace("%P", '1'), 'class', 'id -done'));
      test('should click on "Next" button', () => client.scrollWaitForExistAndClick(OnBoarding.welcomeSteps.next_button));
      test('should check that the step number is equal to "1" ', () => client.checkTextValue(OnBoarding.welcomeSteps.tooltip_step, '1/2', 'contain', 4000));
      test('should check the first onboarding-tooltip message', () => client.checkTextValue(OnBoarding.welcomeSteps.message_value, 'A good way to start is to add your own logo here!'));
      test('should upload the header logo', () => client.uploadPicture('image_test.jpg', OnBoarding.welcomeSteps.header_logo));
      test('should click on "Next" button', () => client.scrollWaitForExistAndClick(OnBoarding.welcomeSteps.next_button));
    }, 'common_client');

    scenario('Step 2/2', client => {
      test('should check that the step number is equal to "2"', () => client.checkTextValue(OnBoarding.welcomeSteps.tooltip_step, '2/2', 'contain', 4000));
      test('should check the second onboarding-tooltip message', () => client.checkTextValue(OnBoarding.welcomeSteps.message_value, 'If you want something really special, have a look at the theme catalog!'));
      test('should click on "Next" button', () => client.waitForExistAndClick(OnBoarding.welcomeSteps.next_button));
    }, 'common_client');
  }, 'common_client');

  scenario('The third tutorial step : Get the shop ready for payments', client => {
    test('The third tutorial steps', () => common.paymentSteps(client));
  }, 'common_client');

  scenario('The fourth tutorial step : Choose the shipping solutions', client => {
    test('The fourth tutorial steps', () => common.carriersSteps(client));
  }, 'common_client');

  scenario('The fifth tutorial steps', () => {
    scenario('Step 1/2 : Discover the module selection', client => {
      test('should check that the current step has started', () => client.checkAttributeValue(OnBoarding.welcomeSteps.tutorial_step.replace("%P", '4'), 'class', 'id -done', 'equal'));
      test('should check that the step number is equal to "1"', () => client.checkTextValue(OnBoarding.welcomeSteps.tooltip_step, '1/2', 'contain', 4000));
      test('should check the first onboarding-tooltip message', () => client.checkTextValue(OnBoarding.welcomeSteps.message_value, 'Discover our module selection in the first tab. Manage your modules on the second one and be aware of notifications in the third tab.', 'equal', 2000));
      test('should click on "Next" button', () => client.waitForExistAndClick(OnBoarding.welcomeSteps.next_button, 4000));
    }, 'common_client');
    scenario('Step 2/2 : Get the shop ready for payments', client => {
      test('should click on "Starter Guide" button', () => {
        return promise
          .then(() => client.waitForExistAndClick(OnBoarding.welcomeSteps.starter_guide_button))
          .then(() => client.switchWindow(1))
          .then(() => client.checkTextValue(OnBoarding.externals.documentation_title, "English documentation", 'equal'))
          .then(() => client.switchWindow(0));
      });
      test('should click on "Forum" button', () => {
        return promise
          .then(() => client.waitForExistAndClick(OnBoarding.welcomeSteps.forum_button))
          .then(() => client.switchWindow(2))
          .then(() => client.checkTextValue(OnBoarding.externals.forum_title, "Forums", 'equal'))
          .then(() => client.switchWindow(0));
      });
      test('should click on "Training" button', () => {
        return promise
          .then(() => client.waitForExistAndClick(OnBoarding.welcomeSteps.training_button))
          .then(() => client.switchWindow(3))
          .then(() => client.isExisting(OnBoarding.externals.discover_training_button, 5000))
          .then(() => client.switchWindow(0));
      });
      test('should click on "Video tutorial" button', () => {
        return promise
          .then(() => client.waitForExist(OnBoarding.welcomeSteps.video_tutorial_button))
          .then(() => client.switchWindow(4))
          .then(() => client.checkTextValue(OnBoarding.externals.youtube_channel_title, "PrestaShop", 'equal', 5000))
          .then(() => client.switchWindow(0));
      });
      test('should click on "Ready" button', () => client.waitForExistAndClick(OnBoarding.ready_button, 1000));
    }, 'common_client');
  }, 'common_client');
}, 'common_client', true);
