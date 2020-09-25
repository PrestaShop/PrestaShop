require('module-alias/register');
// Using chai
const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const cartRulesPage = require('@pages/BO/catalog/discounts');
const addCartRulePage = require('@pages/BO/catalog/discounts/add');

// Import data
const CartRuleFaker = require('@data/faker/cartRule');
const {DefaultAccount} = require('@data/demo/customer');

// import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_shopParameters_productSettings_CRUDCartRule';

let browserContext;
let page;

let numberOfCartRules = 0;

describe('Sort and pagination cart rules', async () => {
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

    numberOfCartRules = await cartRulesPage.getNumberOfElementInGrid(page);
  });

  // 1 - create 21 cart rules
  const creationTests = new Array(21).fill(0, 0, 21);

  creationTests.forEach((test, index) => {
    describe(`Create cart rule n°${index + 1}`, async () => {
      const cartRuleData = new CartRuleFaker({
        name: `todelete${index}`,
        customer: DefaultAccount.email,
        percent: true,
        value: 20,
      });

      it('should go to new cart rule page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToNewCartRulePage${index}`, baseContext);

        await cartRulesPage.goToAddNewCartRulesPage(page);
        const pageTitle = await addCartRulePage.getPageTitle(page);
        await expect(pageTitle).to.contains(addCartRulePage.pageTitle);
      });

      it('should create new cart rule', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createCartRule${index}`, baseContext);

        const validationMessage = await addCartRulePage.createEditCartRules(page, cartRuleData);
        await expect(validationMessage).to.contains(addCartRulePage.successfulCreationMessage);

        const numberOfCartRulesAfterCreation = await cartRulesPage.getNumberOfElementInGrid(page);
        await expect(numberOfCartRulesAfterCreation).to.be.equal(numberOfCartRules + 1 + index);
      });
    });
  });

  // 2 - Pagination
  describe('Pagination next and previous', async () => {
    it('should change the item number to 20 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo20', baseContext);

      const paginationNumber = await cartRulesPage.selectPaginationLimit(page, '20');
      expect(paginationNumber).to.equal('1');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await cartRulesPage.paginationNext(page);
      expect(paginationNumber).to.equal('2');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await cartRulesPage.paginationPrevious(page);
      expect(paginationNumber).to.equal('1');
    });

    it('should change the item number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo50', baseContext);

      const paginationNumber = await cartRulesPage.selectPaginationLimit(page, '50');
      expect(paginationNumber).to.equal('1');
    });
  });
});
