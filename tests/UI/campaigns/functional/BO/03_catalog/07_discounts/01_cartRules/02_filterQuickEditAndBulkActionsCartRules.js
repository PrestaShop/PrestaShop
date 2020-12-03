require('module-alias/register');

// Helpers to open and close browser
const helper = require('@utils/helpers');

// Common tests login BO
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const cartRulesPage = require('@pages/BO/catalog/discounts');
const addCartRulePage = require('@pages/BO/catalog/discounts/add');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_catalog_discounts_cartRules_filterQuickEditAndBulkActionsCartRules';

// Import expect from chai
const {expect} = require('chai');

// Import data
const CartRuleFaker = require('@data/faker/cartRule');

const firstCartRule = new CartRuleFaker(
  {
    name: 'todelete1',
    code: '4QABV6I0',
    discountType: 'Percent',
    discountPercent: 20,
  },
);

const secondCartRule = new CartRuleFaker(
  {
    name: 'todelete2',
    code: '3PAJA674',
    discountType: 'Percent',
    discountPercent: 30,
  },
);

// Browser and tab
let browserContext;
let page;

const numberOfCartRules = 0;

/*
Create 2 cart rules
Filter cart rules by id, priority, code, quantity, status
Quick edit first cart rule in list
Enable, disable and delete cart rules by bulk actions
 */
describe('Filter, quick edit and bulk actions cart rules', async () => {
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

  it('should go to discounts page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToDiscountsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.catalogParentLink,
      dashboardPage.discountsLink,
    );

    const pageTitle = await cartRulesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(cartRulesPage.pageTitle);
  });

  describe('Create 2 cart rules', async () => {
    [firstCartRule, secondCartRule]
      .forEach((cartRuleToCreate, index) => {
        it('should go to new cart rule page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToNewCartRulePage${index}`, baseContext);

          await cartRulesPage.goToAddNewCartRulesPage(page);
          const pageTitle = await addCartRulePage.getPageTitle(page);
          await expect(pageTitle).to.contains(addCartRulePage.pageTitle);
        });

        it('should create new cart rule', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `createCartRule${index}`, baseContext);

          const validationMessage = await addCartRulePage.createEditCartRules(page, cartRuleToCreate);
          await expect(validationMessage).to.contains(addCartRulePage.successfulCreationMessage);

          const numberOfCartRulesAfterCreation = await cartRulesPage.getNumberOfElementInGrid(page);
          await expect(numberOfCartRulesAfterCreation).to.be.at.most(numberOfCartRules + index + 1);
        });
      });
  });

  describe('Filter cart rules', async () => {
    const tests = [
      {
        args: {
          testIdentifier: 'filterName', filterType: 'input', filterBy: 'name', filterValue: firstCartRule.name,
        },
      },
      {
        args: {
          testIdentifier: 'filterPriority', filterType: 'input', filterBy: 'priority', filterValue: 1,
        },
      },
      {
        args: {
          testIdentifier: 'filterCode', filterType: 'input', filterBy: 'code', filterValue: firstCartRule.code,
        },
      },
      {
        args: {
          testIdentifier: 'filterQuantity', filterType: 'input', filterBy: 'quantity', filterValue: 1,
        },
      },
      {
        args: {
          testIdentifier: 'filterStatus', filterType: 'select', filterBy: 'active', filterValue: true,
        },
      },
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        await cartRulesPage.filterCartRules(
          page,
          test.args.filterType,
          test.args.filterBy,
          test.args.filterValue,
        );

        const numberOfCartRulesAfterFilter = await cartRulesPage.getNumberOfElementInGrid(page);
        await expect(numberOfCartRulesAfterFilter).to.be.at.most(numberOfCartRules + 2);

        for (let row = 1; row <= numberOfCartRulesAfterFilter; row++) {
          if (test.args.filterBy === 'active') {
            const cartRuleStatus = await cartRulesPage.getCartRuleStatus(page, row);
            await expect(cartRuleStatus).to.equal(test.args.filterValue);
          } else {
            const textColumn = await cartRulesPage.getTextColumn(
              page,
              row,
              test.args.filterBy,
            );

            await expect(textColumn).to.contains(test.args.filterValue);
          }
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfCartRulesAfterReset = await cartRulesPage.resetAndGetNumberOfLines(page);
        await expect(numberOfCartRulesAfterReset).to.equal(numberOfCartRules + 2);
      });
    });
  });

  describe('Quick edit cart rule', async () => {
    it(`should filter by name '${firstCartRule.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToQuickEdit', baseContext);

      await cartRulesPage.filterCartRules(
        page,
        'input',
        'name',
        firstCartRule.name,
      );

      const textColumn = await cartRulesPage.getTextColumn(
        page,
        1,
        'name',
      );

      await expect(textColumn).to.contains(firstCartRule.name);
    });

    [
      {args: {status: 'disable', enable: false}},
      {args: {status: 'enable', enable: true}},
    ].forEach((status) => {
      it(`should ${status.args.status} the first cart rule`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${status.args.status}CartRule`, baseContext);

        await cartRulesPage.setCartRuleStatus(
          page,
          1,
          status.args.enable,
        );


        const currentStatus = await cartRulesPage.getCartRuleStatus(page, 1);
        await expect(currentStatus).to.be.equal(status.args.enable);
      });
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterQuickEdit', baseContext);

      const numberOfCartRulesAfterReset = await cartRulesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfCartRulesAfterReset).to.equal(numberOfCartRules + 2);
    });
  });

  describe('Bulk actions cart rules', async () => {
    [
      {action: 'enable', wantedStatus: true},
      {action: 'disable', wantedStatus: false},
    ].forEach((test) => {
      it(`should ${test.action} cart rules with bulk actions`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.action}CartRules`, baseContext);

        await cartRulesPage.bulkSetStatus(page, test.wantedStatus);

        const numberOfCartRulesBulkActions = await cartRulesPage.getNumberOfElementInGrid(page);

        for (let row = 1; row <= numberOfCartRulesBulkActions; row++) {
          const rowStatus = await cartRulesPage.getCartRuleStatus(page, row);
          await expect(rowStatus).to.equal(test.wantedStatus);
        }
      });
    });

    it('should bulk delete cart rules', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteCartRules', baseContext);

      const deleteTextResult = await cartRulesPage.bulkDeleteCartRules(page);
      await expect(deleteTextResult).to.be.contains(cartRulesPage.successfulMultiDeleteMessage);
    });
  });
});
