import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boCartRulesPage,
  boCartRulesCreatePage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  FakerCartRule,
  type Page,
  utilsDate,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_discounts_cartRules_CRUDCartRule_conditions_invalidDateTime';

describe('BO - Cart rules - Condition : Case 2 bis - Invalid Date Time', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numCartRules: number = 0;

  const cartRuleDataDate: FakerCartRule = new FakerCartRule({
    name: 'Cart rule invalid date time',
    discountType: 'Amount',
    discountAmount: {
      value: 1,
      currency: 'EUR',
      tax: 'Tax excluded',
    },
    dateFrom: utilsDate.setDateFormat('yyyy-mm-dd', '2025-05-14'),
    dateTo: utilsDate.setDateFormat('yyyy-mm-dd', '2024-05-14'),
  });
  const cartRuleDataTime: FakerCartRule = new FakerCartRule({
    name: 'Cart rule invalid date time',
    discountType: 'Amount',
    discountAmount: {
      value: 1,
      currency: 'EUR',
      tax: 'Tax excluded',
    },
    dateFrom: utilsDate.setDateFormat('yyyy-mm-dd', '2025-05-14 12:00:00'),
    dateTo: utilsDate.setDateFormat('yyyy-mm-dd', '2025-05-14 11:00:00'),
  });

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('BO : Create new cart rule', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Catalog > Discounts\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDiscountsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.catalogParentLink,
        boDashboardPage.discountsLink,
      );

      const pageTitle = await boCartRulesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCartRulesPage.pageTitle);
    });

    it('should reset and get number of cart rules', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

      numCartRules = await boCartRulesPage.resetAndGetNumberOfLines(page);
      expect(numCartRules).to.be.at.least(0);
    });

    it('should go to new cart rule page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewCartRulePage', baseContext);

      await boCartRulesPage.goToAddNewCartRulesPage(page);

      const pageTitle = await boCartRulesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCartRulesCreatePage.pageTitle);
    });

    it('should create new cart rule with begin date > end date', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCartRuleDate', baseContext);

      const validationMessage = await boCartRulesCreatePage.createEditCartRules(page, cartRuleDataDate, true, true);
      expect(validationMessage)
        .to.contains(boCartRulesCreatePage.errorMessageInvalidDateFrom)
        .and.to.contains(boCartRulesCreatePage.errorMessageInvalidDateTo)
        .and.to.contains(boCartRulesCreatePage.errorMessageVoucherCannotEndBeforeBegin);
    });

    it('should create new cart rule with begin time > end time', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCartRuleTime', baseContext);

      const validationMessage = await boCartRulesCreatePage.createEditCartRules(page, cartRuleDataTime, true, true);
      expect(validationMessage).to.contains(boCartRulesCreatePage.errorMessageVoucherCannotEndBeforeBegin);
    });

    it('should click on cancel button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnCancelButton', baseContext);

      await boCartRulesCreatePage.clickOnCancelButton(page);

      const pageTitle = await boCartRulesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCartRulesPage.pageTitle);
    });

    it('should get number of cart rules', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterCreate', baseContext);

      const numCartRulesAfterCreate = await boCartRulesPage.getNumberOfElementInGrid(page);
      expect(numCartRulesAfterCreate).to.equals(numCartRules);
    });
  });
});
