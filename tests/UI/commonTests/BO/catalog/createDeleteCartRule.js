require('module-alias/register');
// Import utils
const helper = require('@utils/helpers');

// Import login test
const loginCommon = require('@commonTests/BO/loginBO');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const cartRulesPage = require('@pages/BO/catalog/discounts');
const addCartRulePage = require('@pages/BO/catalog/discounts/add');

// Import test context
const testContext = require('@utils/testContext');

// Import expect from chai
const {expect} = require('chai');

let browserContext;
let page;
let numberOfCartRules;

/**
 * Function to create cart rule
 * @param cartRuleData {CartRuleData} Cart rule data to create
 * @param baseContext {string} String to identify the test
 */
function createCartRuleTest(cartRuleData, baseContext = 'commonTests-createCartRuleTest') {
  describe('PRE-TEST: Create cart rule', async () => {
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

    it('should go to new cart rule page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewCartRulePage', baseContext);

      await cartRulesPage.goToAddNewCartRulesPage(page);

      const pageTitle = await addCartRulePage.getPageTitle(page);
      await expect(pageTitle).to.contains(addCartRulePage.pageTitle);
    });

    it('should create new cart rule', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCartRule', baseContext);

      const validationMessage = await addCartRulePage.createEditCartRules(page, cartRuleData);
      await expect(validationMessage).to.contains(addCartRulePage.successfulCreationMessage);
    });
  });
}

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
  describe('POST-TEST: Bulk delete cart rule', async () => {
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

module.exports = {createCartRuleTest, deleteCartRuleTest, bulkDeleteCartRuleTest};
