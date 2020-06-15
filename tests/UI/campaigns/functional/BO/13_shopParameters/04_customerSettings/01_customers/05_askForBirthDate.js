/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const CustomerSettingsPage = require('@pages/BO/shopParameters/customerSettings');
const {options} = require('@pages/BO/shopParameters/customerSettings/options');
const FOHomePage = require('@pages/FO/home');
const LoginFOPage = require('@pages/FO/login');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_shopParameters_customerSettings_customers_askForBirthDate';

let browserContext;
let page;

// Init objects needed
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    customerSettingsPage: new CustomerSettingsPage(page),
    foHomePage: new FOHomePage(page),
    loginFOPage: new LoginFOPage(page),
  };
};

describe('Enable ask for birth date', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    this.pageObjects = await init();
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // Login into BO and go to customer settings page
  loginCommon.loginBO();

  it('should go to \'Shop parameters > Customer Settings\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCustomerSettingsPage', baseContext);

    await this.pageObjects.dashboardPage.goToSubMenu(
      this.pageObjects.dashboardPage.shopParametersParentLink,
      this.pageObjects.dashboardPage.customerSettingsLink,
    );

    await this.pageObjects.customerSettingsPage.closeSfToolBar();

    const pageTitle = await this.pageObjects.customerSettingsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.customerSettingsPage.pageTitle);
  });

  const tests = [
    {args: {action: 'disable', enable: false}},
    {args: {action: 'enable', enable: true}},
  ];

  tests.forEach((test) => {
    it(`should ${test.args.action} ask for birth date`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}AskForBirthDate`, baseContext);

      const result = await this.pageObjects.customerSettingsPage.setOptionStatus(
        options.OPTION_BIRTH_DATE,
        test.args.enable,
      );

      await expect(result).to.contains(this.pageObjects.customerSettingsPage.successfulUpdateMessage);
    });

    it('should go to customer account in FO and check birth day input', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        `checkIsBirthDate${this.pageObjects.customerSettingsPage.uppercaseFirstCharacter(test.args.action)}`,
        baseContext,
      );

      // Go to FO
      page = await this.pageObjects.customerSettingsPage.viewMyShop();
      this.pageObjects = await init();

      // Change language in FO
      await this.pageObjects.foHomePage.changeLanguage('en');

      // Go to create account page
      await this.pageObjects.foHomePage.goToLoginPage();
      await this.pageObjects.loginFOPage.goToCreateAccountPage();

      // Check birthday
      const isBirthDateInputVisible = await this.pageObjects.loginFOPage.isBirthDateVisible();
      await expect(isBirthDateInputVisible).to.be.equal(test.args.enable);

      // Go back to BO
      page = await this.pageObjects.loginFOPage.closePage(browserContext, 0);
      this.pageObjects = await init();
    });
  });
});
