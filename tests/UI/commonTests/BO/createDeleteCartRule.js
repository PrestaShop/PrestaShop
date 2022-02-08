require('module-alias/register');
// Import utils
const helper = require('@utils/helpers');

// Import login test
const loginCommon = require('@commonTests/loginBO');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const cartRulesPage = require('@pages/BO/catalog/discounts');

// Import test context
const testContext = require('@utils/testContext');

// Import expect from chai
const {expect} = require('chai');

let browserContext;
let page;
let numberOfCartRules;

/**
 * Function to delete cart rule
 * @param cartRuleName {string} Cart rule name to delete
 * @param baseContext {string} String to identify the test
 */
function deleteCartRuleTest(cartRuleName, baseContext = 'commonTests-deleteCartRuleTest') {
  describe('POST-TEST: Delete cart rule', async () => {
    // before and after functions
    before(async function () {
      browserContext = await helper.createBrowserContext(this.browser);
      page = await helper.newTab(browserContext);
    });

    after(async () => {
      await helper.closeBrowserContext(browserContext);
    });

    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Catalog > Discounts\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDiscountsPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.catalogParentLink,
        dashboardPage.discountsLink,
      );

      const pageTitle = await cartRulesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(cartRulesPage.pageTitle);
    });

    it('should reset and get number of cart rules', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);

      numberOfCartRules = await cartRulesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfCartRules).to.be.at.least(0);
    });

    it('should delete cart rule', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCartRule', baseContext);

      const validationMessage = await cartRulesPage.deleteCartRule(page, 1, cartRuleName);
      await expect(validationMessage).to.contains(cartRulesPage.successfulDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterBulkDelete', baseContext);

      const numberOfCartRulesAfterDelete = await cartRulesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfCartRulesAfterDelete).to.equal(numberOfCartRules - 1);
    });
  });
}

/**
 * Function to bulk delete cart rule
 * @param baseContext {string} String to identify the test
 */
function bulkDeleteCartRuleTest(baseContext = 'commonTests-bulkDeleteCartRuleTest') {
  describe('POST-TEST: BUlk delete cart rule', async () => {
    // before and after functions
    before(async function () {
      browserContext = await helper.createBrowserContext(this.browser);
      page = await helper.newTab(browserContext);
    });

    after(async () => {
      await helper.closeBrowserContext(browserContext);
    });

    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Catalog > Discounts\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDiscountsPage3', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.catalogParentLink,
        dashboardPage.discountsLink,
      );

      const pageTitle = await cartRulesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(cartRulesPage.pageTitle);
    });

    it('should reset and get number of cart rules', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);

      numberOfCartRules = await cartRulesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfCartRules).to.be.at.least(0);
    });

    it('should bulk delete cart rules', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteCartRules', baseContext);

      const deleteTextResult = await cartRulesPage.bulkDeleteCartRules(page);
      await expect(deleteTextResult).to.be.contains(cartRulesPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterBulkDelete', baseContext);

      const numberOfCartRulesAfterDelete = await cartRulesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfCartRulesAfterDelete).to.equal(0);
    });
  });
}

module.exports = {deleteCartRuleTest, bulkDeleteCartRuleTest};
