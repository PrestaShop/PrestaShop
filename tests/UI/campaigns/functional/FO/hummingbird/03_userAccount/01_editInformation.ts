import testContext from '@utils/testContext';
import {expect} from 'chai';

// Import commonTests
import {deleteCustomerTest} from '@commonTests/BO/customers/customer';
import {createAccountTest} from '@commonTests/FO/classic/account';
import {enableTheme, disableTheme} from '@commonTests/BO/design/hummingbird';

import {
  type BrowserContext,
  FakerCustomer,
  foHummingbirdHomePage,
  foHummingbirdLoginPage,
  foHummingbirdMyAccountPage,
  foHummingbirdMyInformationsPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_hummingbird_userAccount_editInformation';

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
  let browserContext: BrowserContext;
  let page: Page;

  const createCustomerData: FakerCustomer = new FakerCustomer();
  // New customer data with empty new password
  const editCustomerData1: FakerCustomer = new FakerCustomer({password: ''});
  // New customer data with repeated letters
  const editCustomerData2: FakerCustomer = new FakerCustomer({password: 'abcabcabc'});
  // New customer data with password below 8
  const editCustomerData3: FakerCustomer = new FakerCustomer({password: 'presta'});
  // New customer data with an old similar password
  const editCustomerData4: FakerCustomer = new FakerCustomer({password: 'testoune'});
  // New customer data with simple characters password
  const editCustomerData5: FakerCustomer = new FakerCustomer({password: 'prestash'});
  // New customer data with common password
  const editCustomerData6: FakerCustomer = new FakerCustomer({password: 'azerty123'});
  // New customer data with top 10 common password
  const editCustomerData7: FakerCustomer = new FakerCustomer({password: '123456789'});
  // New customer data with same characters
  const editCustomerData8: FakerCustomer = new FakerCustomer({password: 'aaaaaaaaa'});
  // New customer data with good password
  const editCustomerData9: FakerCustomer = new FakerCustomer({password: 'test edit information'});

  // Pre-condition: Create new account on FO
  createAccountTest(createCustomerData, `${baseContext}_preTest_0`);

  // Pre-condition : Install Hummingbird
  enableTheme('hummingbird', `${baseContext}_preTest_1`);

  describe('Edit the created account in FO', async () => {
    before(async function () {
      browserContext = await utilsPlaywright.createBrowserContext(this.browser);
      page = await utilsPlaywright.newTab(browserContext);
    });

    after(async () => {
      await utilsPlaywright.closeBrowserContext(browserContext);
    });

    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO', baseContext);

      await foHummingbirdHomePage.goTo(page, global.FO.URL);

      const result = await foHummingbirdHomePage.isHomePage(page);
      expect(result).to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPage', baseContext);

      await foHummingbirdHomePage.goToLoginPage(page);

      const pageTitle = await foHummingbirdLoginPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdLoginPage.pageTitle);
    });

    it('should login', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginFO', baseContext);

      await foHummingbirdLoginPage.customerLogin(page, createCustomerData);

      const isCustomerConnected = await foHummingbirdLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected!').to.eq(true);
    });

    it('should go to my account page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMyAccountPage', baseContext);

      await foHummingbirdHomePage.goToMyAccountPage(page);

      const pageTitle = await foHummingbirdMyAccountPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdMyAccountPage.pageTitle);
    });

    it('should go account information page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAccountInformationPage', baseContext);

      await foHummingbirdMyAccountPage.goToInformationPage(page);

      const pageTitle = await foHummingbirdMyInformationsPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdMyInformationsPage.pageTitle);
    });

    it('case 1 - should edit the account information ** re-enter the same password and leave new password empty',
      async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'editAccount1', baseContext);

        const textResult = await foHummingbirdMyInformationsPage.editAccount(
          page,
          createCustomerData.password,
          editCustomerData1,
        );
        expect(textResult).to.be.equal(foHummingbirdMyInformationsPage.successfulUpdateMessage);
      });

    it('should check that the account is still connected after update', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'connectedUpdatedAccount', baseContext);

      const isCustomerConnected = await foHummingbirdMyInformationsPage.isCustomerConnected(page);
      expect(isCustomerConnected).to.eq(true);
    });

    it('case 2 - should edit the account information ** enter a wrong password and leave new password empty',
      async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'editAccount2', baseContext);

        const textResult = await foHummingbirdMyInformationsPage.editAccount(page, 'wrongPass', editCustomerData1);
        expect(textResult).to.be.equal(foHummingbirdMyInformationsPage.errorUpdateMessage);
      });

    it('should check the error alerts \'Invalid email/password combination\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkErrorAlerts2', baseContext);

      let textResult = await foHummingbirdMyInformationsPage.getInvalidEmailAlert(page);
      expect(textResult, 'Invalid email/password alert is not visible!').to
        .equal(foHummingbirdMyInformationsPage.invalidEmailAlertMessage);

      textResult = await foHummingbirdMyInformationsPage.getInvalidPasswordAlert(page);
      expect(textResult, 'Invalid email/password alert is not visible!').to
        .equal(foHummingbirdMyInformationsPage.invalidEmailAlertMessage);
    });

    it('Case 3 - should edit the account information ** enter a new password with repeated words',
      async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'editAccount3', baseContext);

        const textResult = await foHummingbirdMyInformationsPage.editAccount(
          page,
          createCustomerData.password,
          editCustomerData2,
        );
        expect(textResult).to.be.equal(foHummingbirdMyInformationsPage.errorUpdateMessage);
      });

    it('should check the minimum score alert on new password block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkErrorAlerts3', baseContext);

      const textResult = await foHummingbirdMyInformationsPage.getInvalidNewPasswordAlert(page);
      expect(textResult).to.contains(foHummingbirdMyInformationsPage.noRepeatMessage);
      expect(textResult).to.contains(foHummingbirdMyInformationsPage.addAnotherMessage);
      expect(textResult).to.contains(foHummingbirdMyInformationsPage.noCommonWordsMessage);
      expect(textResult).to.contains(foHummingbirdMyInformationsPage.noRepeatedWordsMessage);
    });

    it('Case 4 - should edit the account information ** enter a new password between 5 and 8 characters',
      async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'editAccount4', baseContext);

        const textResult = await foHummingbirdMyInformationsPage.editAccount(
          page,
          createCustomerData.password,
          editCustomerData3,
        );
        expect(textResult).to.be.equal(foHummingbirdMyInformationsPage.errorUpdateMessage);
      });

    it('should check the error alerts on new password block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkErrorAlerts4', baseContext);

      const textResult = await foHummingbirdMyInformationsPage.getInvalidNewPasswordAlert(page);
      expect(textResult).to.contains(foHummingbirdMyInformationsPage.invalidNumberOfCharacters);
      expect(textResult).to.contains(foHummingbirdMyInformationsPage.minimumScoreAlertMessage);
    });

    it('Case 5 - should edit the account information ** enter a new password with an old similar password',
      async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'editAccount5', baseContext);

        const textResult = await foHummingbirdMyInformationsPage.editAccount(
          page,
          createCustomerData.password,
          editCustomerData4,
        );
        expect(textResult).to.be.equal(foHummingbirdMyInformationsPage.errorUpdateMessage);
      });

    it('should check the error alert on new password block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkErrorAlerts5', baseContext);

      const textResult = await foHummingbirdMyInformationsPage.getInvalidNewPasswordAlert(page);
      expect(textResult).to.contains(foHummingbirdMyInformationsPage.commonUsedPasswordMessage);
      expect(textResult).to.contains(foHummingbirdMyInformationsPage.changeCommonPasswordMessage);
    });

    it('Case 6 - should edit the account information ** update password with simple characters',
      async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'editAccount6', baseContext);

        const textResult = await foHummingbirdMyInformationsPage.editAccount(
          page,
          createCustomerData.password,
          editCustomerData5,
        );
        expect(textResult).to.be.equal(foHummingbirdMyInformationsPage.errorUpdateMessage);
      });

    it('should check the error alert on new password block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkErrorAlerts6', baseContext);

      const textResult = await foHummingbirdMyInformationsPage.getInvalidNewPasswordAlert(page);
      expect(textResult, 'Minimum score password alert is not visible!').to
        .contains(foHummingbirdMyInformationsPage.minimumScoreAlertMessage);
    });

    it('Case 7 - should edit the account information ** update password with common password', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editAccount7', baseContext);

      const textResult = await foHummingbirdMyInformationsPage.editAccount(
        page,
        createCustomerData.password,
        editCustomerData6,
      );
      expect(textResult).to.be.equal(foHummingbirdMyInformationsPage.errorUpdateMessage);
    });

    it('should check the error alert on new password block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkErrorAlerts7', baseContext);

      const textResult = await foHummingbirdMyInformationsPage.getInvalidNewPasswordAlert(page);
      expect(textResult).to.contains(foHummingbirdMyInformationsPage.veryCommonPasswordMessage);
      expect(textResult).to.contains(foHummingbirdMyInformationsPage.changeCommonPasswordMessage);
    });

    it('Case 8 - should edit the account information ** update password with top 10 common password',
      async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'editAccount8', baseContext);

        const textResult = await foHummingbirdMyInformationsPage.editAccount(
          page,
          createCustomerData.password,
          editCustomerData7,
        );
        expect(textResult).to.be.equal(foHummingbirdMyInformationsPage.errorUpdateMessage);
      });

    it('should check the error alert on new password block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkErrorAlerts8', baseContext);

      const textResult = await foHummingbirdMyInformationsPage.getInvalidNewPasswordAlert(page);
      expect(textResult).to.contains(foHummingbirdMyInformationsPage.top10CommonPasswordMessage);
      expect(textResult).to.contains(foHummingbirdMyInformationsPage.changeCommonPasswordMessage);
    });

    it('Case 9 - should edit the account information ** update password with the same characters',
      async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'editAccount9', baseContext);

        const textResult = await foHummingbirdMyInformationsPage.editAccount(
          page,
          createCustomerData.password,
          editCustomerData8,
        );
        expect(textResult).to.be.equal(foHummingbirdMyInformationsPage.errorUpdateMessage);
      });

    it('should check the error alert on new password block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkErrorAlerts9', baseContext);

      const textResult = await foHummingbirdMyInformationsPage.getInvalidNewPasswordAlert(page);
      expect(textResult).to.contains(foHummingbirdMyInformationsPage.repeatGuessMessage);
      expect(textResult).to.contains(foHummingbirdMyInformationsPage.changeCommonPasswordMessage);
      expect(textResult).to.contains(foHummingbirdMyInformationsPage.noRepeatedWordsMessage);
    });

    it('Case 10 - should edit the account information ** update password with a good new password',
      async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'editAccount10', baseContext);

        const textResult = await foHummingbirdMyInformationsPage.editAccount(
          page,
          createCustomerData.password,
          editCustomerData9,
        );
        expect(textResult).to.be.equal(foHummingbirdMyInformationsPage.successfulUpdateMessage);
      });
  });

  // Post-condition: Delete the created customer account
  deleteCustomerTest(editCustomerData9, `${baseContext}_postTest_0`);

  // Post-condition : Uninstall Hummingbird
  disableTheme('hummingbird', `${baseContext}_postTest_1`);
});
