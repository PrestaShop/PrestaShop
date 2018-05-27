const {AddProductPage} = require('../../selectors/BO/add_product_page');
const {resetModule} = require('./module');
const {ProductList} = require('../../selectors/BO/add_product_page');
const {ModulePage} = require('../../selectors/BO/module_page');
let promise = Promise.resolve();

module.exports = {
  clickOnMenuLinksAndCheckElement: function (client, mainMenu, subMenu, pageSelector, describe1 = "", describe2 = "", pause = 0, tabMenu = "") {
    let page = describe2 === "" ? describe1 : describe2;
    if (mainMenu === "") {
      test('should click on "' + describe1 + '" menu', () => client.waitForExistAndClick(subMenu));
    } else {
      test('should click on "' + describe1 + '" menu', () => client.goToSubtabMenuPage(mainMenu, subMenu));
    }
    if (tabMenu !== "") {
      test('should click on "' + describe2 + '" tab', () => client.waitForExistAndClick(tabMenu));
    }
    test('should check that the "' + page + '" page is well opened', () => {
      return promise
        .then(() => client.waitForExist(pageSelector))
        .then(() => client.isExisting(pageSelector, pause));
    });
  },
  resetWelcomeModule: function (OnBoarding) {
    scenario('Reset the module "Welcome" ', client => {
      resetModule(client, ModulePage, AddProductPage, "Welcome", "welcome");
      test('should click on "RESUME" button', () => client.waitForExistAndClick(OnBoarding.resume_button, 1000));
    }, 'common_client');
  },
  onBoardingSteps: function (OnBoarding, AddProductPage) {
    scenario('The first tutorial step : Create the first product ', () => {
      scenario('Step 1/5', client => {
        test('should click on "Start" button', () => client.waitForExistAndClick(OnBoarding.start_button));
        test('should check that the current step has started', () => client.checkAttributeValue(OnBoarding.welcomeSteps.tutorial_step.replace("%P", '0'), 'class', 'id -done'));
        test('should check the existence of the onboarding-tooltip', () => client.isExisting(OnBoarding.welcomeSteps.onboarding_tooltip, 2000));
        test('should check the first onboarding-tooltip message', () => client.checkTextValue(OnBoarding.welcomeSteps.message_value, 'Give your product a catchy name.'));
        test('should check that the step number is equal to "1"', () => client.checkTextValue(OnBoarding.welcomeSteps.tooltip_step, '1/5'));
        test('should set the "Product name" input', () => client.waitAndSetValue(AddProductPage.product_name_input, 'productTest' + date_time));
      }, 'common_client');

      scenario('Step 2/5', client => {
        test('should click on "Next" button', () => client.waitForExistAndClick(OnBoarding.welcomeSteps.next_button));
        test('should check that the step number is equal to "2"', () => client.checkTextValue(OnBoarding.welcomeSteps.tooltip_step, '2', 'contain', 2000));
        test('should check the second onboarding-tooltip message', () => client.checkTextValue(OnBoarding.welcomeSteps.message_value, 'Fill out the essential details in this tab. The other tabs are for more advanced information.'));
        test('should select "Product with combinations"', () => client.waitForExistAndClick(AddProductPage.variations_type_button));
      }, 'common_client');

      scenario('Step 3/5 ', client => {
        test('should click on "Next" button', () => client.waitForExistAndClick(OnBoarding.welcomeSteps.next_button));
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
        test('should search the "Product"', () => {
          return promise
            .then(() => client.waitAndSetValue(AddProductPage.catalogue_filter_by_name_input, 'productTest' + date_time));
        });
        test('should click on "Apply" button', () => client.waitForExistAndClick(AddProductPage.catalog_submit_filter));
        test('should check the product', () => client.checkTextValue(ProductList.product_name.replace("%ID", '1'), 'productTest' + date_time));
        test('should click on "Reset" button', () => client.waitForExistAndClick(AddProductPage.catalog_reset_filter));
      }, 'common_client');
    }, 'common_client');
    scenario(' The second Tutorial step : Give the shop an own identity', () => {
      scenario('Step 1/2', client => {
        test('should check that the current step has started', () => client.checkAttributeValue(OnBoarding.welcomeSteps.tutorial_step.replace("%P", '1'), 'class', 'id -done'));
        test('should click on "Next" button', () => client.scrollWaitForExistAndClick(OnBoarding.welcomeSteps.next_button));
        test('should check that the step number is equal to "1" ', () => client.checkTextValue(OnBoarding.welcomeSteps.tooltip_step, '1/2', 'contain', 1000));
        test('should check the first  onboarding-tooltip message', () => client.checkTextValue(OnBoarding.welcomeSteps.message_value, 'A good way to start is to add your own logo here!'));
        test('should upload the header logo', () => client.uploadPicture('image_test.jpg', OnBoarding.welcomeSteps.header_logo));
        test('should click on "Next" button', () => client.scrollWaitForExistAndClick(OnBoarding.welcomeSteps.next_button));
      }, 'common_client');

      scenario('Step 2/2', client => {
        test('should check that the step number is equal to "2"', () => client.checkTextValue(OnBoarding.welcomeSteps.tooltip_step, '2/2', 'contain', 2000));
        test('should check the second onboarding-tooltip message', () => client.checkTextValue(OnBoarding.welcomeSteps.message_value, 'If you want something really special, have a look at the theme catalog!'));
        test('should click on "Next" button', () => client.waitForExistAndClick(OnBoarding.welcomeSteps.next_button));
      }, 'common_client');
    }, 'common_client');

    scenario('The third tutorial step : Get the shop ready for payments', () => {
      scenario('Step 1/2', client => {
        test('should check that the current step has started', () => client.checkAttributeValue(OnBoarding.welcomeSteps.tutorial_step.replace("%P", '2'), 'class', 'id -done', 'equal'));
        test('should check that the step number is equal to "1"', () => client.checkTextValue(OnBoarding.welcomeSteps.tooltip_step, '1/2', 'contain', 2000));
        test('should check the first onboarding-tooltip message', () => client.checkTextValue(OnBoarding.welcomeSteps.message_value, 'These payment methods are already available to your customers.', 'equal', 2000));
        test('should click on the configure button of the check payment module', () => client.waitForExistAndClick(OnBoarding.payement_check_button.replace("%s", "check")));
        test('should click on "RESUME" button', () => client.waitForVisibleAndClick(OnBoarding.resume_button));
        test('should click on "Next" button', () => client.waitForExistAndClick(OnBoarding.welcomeSteps.next_button));
      }, 'common_client');

      scenario('Step 2/2', client => {
        test('should check that the step number is "2/2"', () => client.checkTextValue(OnBoarding.welcomeSteps.tooltip_step, '2/2', 'contain', 2000));
        test('should check the second onboarding-tooltip message', () => client.checkTextValue(OnBoarding.welcomeSteps.message_value, 'And you can choose to add other payment methods from here!'));
      }, 'common_client');
    }, 'common_client');

    scenario('The fourth tutorial step : Choose the shipping solutions', client => {
      test('should check that the current step has started', () => client.checkAttributeValue(OnBoarding.welcomeSteps.tutorial_step.replace("%P", '3'), 'class', 'id -done', 'equal'));
      test('should click on "Next" button', () => client.waitForExistAndClick(OnBoarding.welcomeSteps.next_button));
      test('should discover the types of "Carriers"', () => client.checkTextValue(OnBoarding.welcomeSteps.tooltip_step, '1/2', 'contain', 3000));
      test('should click on "Next" button', () => client.scrollWaitForExistAndClick(OnBoarding.welcomeSteps.next_button));
      test('should check that the step number is "2/2" ', () => client.checkTextValue(OnBoarding.welcomeSteps.tooltip_step, '2/2', 'contain', 2000));
      test('should check that the fifth tutorial step is done', () => client.checkAttributeValue(OnBoarding.welcomeSteps.tutorial_step.replace("%P", '4'), 'class', 'id -done', 'equal'));
    }, 'common_client');

    scenario('The fifth tutorial step, "Get the shop ready for payments"', () => {
      scenario('Step 1/2 "Get the shop ready for payments"', client => {
        test('should click on "Next" button', () => client.waitForExistAndClick(OnBoarding.welcomeSteps.next_button));
        test('should check that the step number is equal to "1"', () => client.checkTextValue(OnBoarding.welcomeSteps.tooltip_step, '1/2', 'contain', 2000));
        test('should click on "Next" button', () => client.waitForExistAndClick(OnBoarding.welcomeSteps.next_button));
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
            .then(() => client.checkTextValue(OnBoarding.externals.training_title, "PrestaShop Training", 'equal'))
            .then(() => client.switchWindow(0));
        });
        test('should click on "Video tutorial" button', () => {
          return promise
            .then(() => client.waitForExistAndClick(OnBoarding.welcomeSteps.video_tutorial_button))
            .then(() => client.switchWindow(4))
            .then(() => client.checkTextValue(OnBoarding.externals.youtube_channel_title, "PrestaShop", 'equal'))
            .then(() => client.switchWindow(0));
        });
        test('should click on "Ready" button', () => client.waitForExistAndClick(OnBoarding.ready_button, 1000));
      }, 'common_client');
    }, 'common_client', true);
  }
};
