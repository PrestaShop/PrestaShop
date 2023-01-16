require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import common tests
const {createAccountTest} = require('@commonTests/FO/createAccount');
const {deleteCustomerTest} = require('@commonTests/BO/customers/createDeleteCustomer');

// Import data
const CustomerFaker = require('@data/faker/customer');

// Import FO pages
const homePage = require('@pages/FO/home');
const loginPage = require('@pages/FO/login');
const myAccountPage = require('@pages/FO/myAccount');
const accountIdentityPage = require('@pages/FO/myAccount/identity');

const baseContext = 'functional_FO_userAccount_editInformation';

let browserContext;
let page;

const createCustomerData = new CustomerFaker();
// New customer data with empty new password
const editCustomerData1 = new CustomerFaker({password: ''});
// New customer data with repeated letters
const editCustomerData2 = new CustomerFaker({password: 'abcabcabc'});
// New customer data with password below 8
const editCustomerData3 = new CustomerFaker({password: 'presta'});
// New customer data with an old similar password
const editCustomerData4 = new CustomerFaker({password: 'testoune'});
// New customer data with simple characters password
const editCustomerData5 = new CustomerFaker({password: 'prestash'});
// New customer data with common password
const editCustomerData6 = new CustomerFaker({password: 'azerty123'});
// New customer data with top 10 common password
const editCustomerData7 = new CustomerFaker({password: '123456789'});
// New customer data with same characters
const editCustomerData8 = new CustomerFaker({password: 'aaaaaaaaa'});
// New customer data with good password
const editCustomerData9 = new CustomerFaker({password: 'test edit information'});

/*
Pre-condition:
- Create new customer account in FO
Scenario:
- Re-enter the same password and leave new password empty
- Enter a wrong password and leave new password empty
- Update password with repeated words
- Enter a new password between 5 and 8 characters
- Update password with an old similar password
- Update password with simple characters
- Update password with common password
- Update password with top 10 common password
- Update password with the same characters
- Update password with a good new password
Post condition:
- Delete the created account in BO
 */
describe('FO - Account : Edit information', async () => {
  // Pre-condition: Create new account on FO
  createAccountTest(createCustomerData, `${baseContext}_preTest`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Edit the created account in FO', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO', baseContext);

      await homePage.goTo(page, global.FO.URL);

      const result = await homePage.isHomePage(page);
      await expect(result).to.be.true;
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPage', baseContext);

      await homePage.goToLoginPage(page);
      const pageTitle = await loginPage.getPageTitle(page);
      await expect(pageTitle).to.equal(loginPage.pageTitle);
    });

    it('should login', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginFO', baseContext);

      await loginPage.customerLogin(page, createCustomerData);

      const isCustomerConnected = await loginPage.isCustomerConnected(page);
      await expect(isCustomerConnected, 'Customer is not connected!').to.be.true;
    });

    it('should go to my account page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMyAccountPage', baseContext);

      await homePage.goToMyAccountPage(page);

      const pageTitle = await myAccountPage.getPageTitle(page);
      await expect(pageTitle).to.equal(myAccountPage.pageTitle);
    });

    it('should go account information page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAccountInformationPage', baseContext);

      await myAccountPage.goToInformationPage(page);

      const pageTitle = await accountIdentityPage.getPageTitle(page);
      await expect(pageTitle).to.equal(accountIdentityPage.pageTitle);
    });

    it('case 1 - should edit the account information ** re-enter the same password and leave new password empty',
      async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'editAccount1', baseContext);

        const textResult = await accountIdentityPage.editAccount(page, createCustomerData.password, editCustomerData1);
        await expect(textResult).to.be.equal(accountIdentityPage.successfulUpdateMessage);
      });

    it('should check that the account is still connected after update', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'connectedUpdatedAccount', baseContext);

      const isCustomerConnected = await accountIdentityPage.isCustomerConnected(page);
      await expect(isCustomerConnected).to.be.true;
    });

    it('case 2 - should edit the account information ** enter a wrong password and leave new password empty',
      async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'editAccount2', baseContext);

        const textResult = await accountIdentityPage.editAccount(page, 'wrongPass', editCustomerData1);
        await expect(textResult).to.be.equal(accountIdentityPage.errorUpdateMessage);
      });

    it('should check the error alerts \'Invalid email/password combination\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkErrorAlerts2', baseContext);

      let textResult = await accountIdentityPage.getInvalidEmailAlert(page);
      await expect(textResult, 'Invalid email alert is not visible!').to
        .equal(accountIdentityPage.invalidEmailAlertMessage);

      textResult = await accountIdentityPage.getInvalidPasswordAlert(page);
      await expect(textResult, 'Invalid password alert is not visible!').to
        .equal(accountIdentityPage.invalidEmailAlertMessage);
    });

    it.skip('Case 3 - should edit the account information ** enter a new password with repeated words',
      async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'editAccount3', baseContext);

        const textResult = await accountIdentityPage.editAccount(page, createCustomerData.password, editCustomerData2);
        await expect(textResult).to.be.equal(accountIdentityPage.errorUpdateMessage);
      });

    it.skip('should check a three error alerts on new password block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkErrorAlerts3', baseContext);

      let textResult = await accountIdentityPage.getInvalidNewPasswordAlert(page);
      await expect(textResult, 'Invalid repeated words alert is not visible!').to
        .contains(accountIdentityPage.invalidRepeatsPasswordAlertMessage);

      textResult = await accountIdentityPage.getInvalidNewPasswordAlert(page);
      await expect(textResult, 'Uncommon words alert is not visible!').to
        .contains(accountIdentityPage.invalidUncommonPasswordAlertMessage);

      textResult = await accountIdentityPage.getInvalidNewPasswordAlert(page);
      await expect(textResult, 'Avoid repeated password alert is not visible!').to
        .contains(accountIdentityPage.invalidRepeatedWordsPasswordAlertMessage);
    });

    it.skip('Case 4 - should edit the account information ** enter a new password between 5 and 8 characters',
      async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'editAccount4', baseContext);

        const textResult = await accountIdentityPage.editAccount(page, createCustomerData.password, editCustomerData3);
        await expect(textResult).to.be.equal(accountIdentityPage.errorUpdateMessage);
      });

    it.skip('should check a three error alerts on new password block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkErrorAlerts4', baseContext);

      let textResult = await accountIdentityPage.getInvalidNewPasswordAlert(page);
      await expect(textResult, 'Invalid number of characters words alert is not visible!').to
        .contains(accountIdentityPage.invalidNumberOfCharacters);

      textResult = await accountIdentityPage.getInvalidNewPasswordAlert(page);
      await expect(textResult, 'Uncommon words password alert is not visible!').to
        .contains(accountIdentityPage.invalidUncommonPasswordAlertMessage);
    });

    it.skip('Case 5 - should edit the account information ** enter a new password with an old similar password',
      async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'editAccount5', baseContext);

        const textResult = await accountIdentityPage.editAccount(page, createCustomerData.password, editCustomerData4);
        await expect(textResult).to.be.equal(accountIdentityPage.errorUpdateMessage);
      });

    it.skip('should check a two error alerts on new password block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkErrorAlerts5', baseContext);

      let textResult = await accountIdentityPage.getInvalidNewPasswordAlert(page);
      await expect(textResult, 'Similar to a common words alert is not visible!').to
        .contains(accountIdentityPage.similarPasswordAlertMessage);

      textResult = await accountIdentityPage.getInvalidNewPasswordAlert(page);
      await expect(textResult, 'Uncommon words password alert is not visible!').to
        .contains(accountIdentityPage.invalidUncommonPasswordAlertMessage);
    });

    it.skip('Case 6 - should edit the account information ** update password with simple characters',
      async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'editAccount6', baseContext);

        const textResult = await accountIdentityPage.editAccount(page, createCustomerData.password, editCustomerData5);
        await expect(textResult).to.be.equal(accountIdentityPage.errorUpdateMessage);
      });

    it.skip('should check a two error alerts on new password block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkErrorAlerts6', baseContext);

      let textResult = await accountIdentityPage.getInvalidNewPasswordAlert(page);
      await expect(textResult, 'Minimum score password alert is not visible!').to
        .contains(accountIdentityPage.minimumScoreAlertMessage);

      textResult = await accountIdentityPage.getInvalidNewPasswordAlert(page);
      await expect(textResult, 'Invalid uncommon password alert is not visible!').to
        .contains(accountIdentityPage.invalidUncommonPasswordAlertMessage);
    });

    it.skip('Case 7 - should edit the account information ** update password with common password', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editAccount7', baseContext);

      const textResult = await accountIdentityPage.editAccount(page, createCustomerData.password, editCustomerData6);
      await expect(textResult).to.be.equal(accountIdentityPage.errorUpdateMessage);
    });

    it.skip('should check a two error alerts on new password block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkErrorAlerts7', baseContext);

      let textResult = await accountIdentityPage.getInvalidNewPasswordAlert(page);
      await expect(textResult, 'Similar to a common words alert is not visible!').to
        .contains(accountIdentityPage.invalidUncommonPasswordAlertMessage);

      textResult = await accountIdentityPage.getInvalidNewPasswordAlert(page);
      await expect(textResult, 'Very common password alert is not visible!').to
        .contains(accountIdentityPage.veryCommonPasswordAlertMessage);
    });

    it.skip('Case 8 - should edit the account information ** update password with top 10 common password',
      async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'editAccount8', baseContext);

        const textResult = await accountIdentityPage.editAccount(page, createCustomerData.password, editCustomerData7);
        await expect(textResult).to.be.equal(accountIdentityPage.errorUpdateMessage);
      });

    it.skip('should check a two error alerts on new password block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkErrorAlerts8', baseContext);

      let textResult = await accountIdentityPage.getInvalidNewPasswordAlert(page);
      await expect(textResult, 'Similar to a common words alert is not visible!').to
        .contains(accountIdentityPage.invalidUncommonPasswordAlertMessage);

      textResult = await accountIdentityPage.getInvalidNewPasswordAlert(page);
      await expect(textResult, 'Top 10 common password alert is not visible!').to
        .contains(accountIdentityPage.topTenCommonPassword);
    });

    it.skip('Case 9 - should edit the account information ** update password with the same characters',
      async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'editAccount9', baseContext);

        const textResult = await accountIdentityPage.editAccount(page, createCustomerData.password, editCustomerData8);
        await expect(textResult).to.be.equal(accountIdentityPage.errorUpdateMessage);
      });

    it.skip('should check a three error alerts on new password block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkErrorAlerts9', baseContext);

      let textResult = await accountIdentityPage.getInvalidNewPasswordAlert(page);
      await expect(textResult, 'Repeats characters alert is not visible!').to
        .contains(accountIdentityPage.invalidRepeatedCharacterPasswordAlertMessage);

      textResult = await accountIdentityPage.getInvalidNewPasswordAlert(page);
      await expect(textResult, 'Invalid uncommon words alert is not visible!').to
        .contains(accountIdentityPage.invalidUncommonPasswordAlertMessage);

      textResult = await accountIdentityPage.getInvalidNewPasswordAlert(page);
      await expect(textResult, 'Avoid repeated words and characters alert is not visible!').to
        .contains(accountIdentityPage.invalidRepeatedWordsPasswordAlertMessage);
    });

    it.skip('Case 10 - should edit the account information ** update password with a good new password',
      async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'editAccount10', baseContext);

        const textResult = await accountIdentityPage.editAccount(page, createCustomerData.password, editCustomerData9);
        await expect(textResult).to.be.equal(accountIdentityPage.successfulUpdateMessage);
      });
  });

  // Post-condition: Delete the created customer account
  deleteCustomerTest(editCustomerData1, `${baseContext}_postTest`);
});
