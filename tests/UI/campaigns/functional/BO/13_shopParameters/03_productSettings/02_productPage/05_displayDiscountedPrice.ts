import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boCartRulesPage,
  boCatalogPriceRulesPage,
  boCatalogPriceRulesCreatePage,
  boDashboardPage,
  boLoginPage,
  boProductSettingsPage,
  type BrowserContext,
  dataProducts,
  FakerCatalogPriceRule,
  foClassicHomePage,
  foClassicProductPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_shopParameters_productSettings_productPage_displayDiscountedPrice';

describe('BO - Shop Parameters - Product Settings : Enable/Disable display discounted price', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const priceRuleData: FakerCatalogPriceRule = new FakerCatalogPriceRule({
    currency: 'All currencies',
    country: 'All countries',
    group: 'All groups',
    reductionType: 'Amount',
    reductionTax: 'Tax included',
    fromQuantity: 3,
    reduction: 20,
  });
  // Unit discount in Volume discounts table(Product page FO)
  const unitDiscountToCheck: string = '€20.00';
  // Unit price in Volume discounts table(Product page FO)
  const unitPriceToCheck: string = '€8.68';

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

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

  it('should go to \'Catalog Price Rules\' tab', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCatalogPriceRulesTab', baseContext);

    await boCartRulesPage.goToCatalogPriceRulesTab(page);

    const pageTitle = await boCatalogPriceRulesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boCatalogPriceRulesPage.pageTitle);
  });

  it('should create new catalog price rule', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'createCatalogPriceRule', baseContext);

    await boCatalogPriceRulesPage.goToAddNewCatalogPriceRulePage(page);

    const pageTitle = await boCatalogPriceRulesCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boCatalogPriceRulesCreatePage.pageTitle);

    const validationMessage = await boCatalogPriceRulesCreatePage.setCatalogPriceRule(page, priceRuleData);
    expect(validationMessage).to.contains(boCatalogPriceRulesPage.successfulCreationMessage);
  });

  it('should go to \'Shop parameters > Product Settings\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductSettingsPage', baseContext);

    await boCatalogPriceRulesCreatePage.goToSubMenu(
      page,
      boCatalogPriceRulesCreatePage.shopParametersParentLink,
      boCatalogPriceRulesCreatePage.productSettingsLink,
    );
    await boProductSettingsPage.closeSfToolBar(page);

    const pageTitle = await boProductSettingsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boProductSettingsPage.pageTitle);
  });

  const tests = [
    {
      args: {
        action: 'disable', enable: false, textColumnToCheck: 'Unit discount', valueToCheck: unitDiscountToCheck,
      },
    },
    {
      args: {
        action: 'enable', enable: true, textColumnToCheck: 'Unit price', valueToCheck: unitPriceToCheck,
      },
    },
  ];

  tests.forEach((test, index: number) => {
    it(`should ${test.args.action} display discounted price`, async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        `${test.args.action}DisplayDiscountedPrice`,
        baseContext,
      );

      const result = await boProductSettingsPage.setDisplayDiscountedPriceStatus(
        page,
        test.args.enable,
      );
      expect(result).to.contains(boProductSettingsPage.successfulUpdateMessage);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${index}`, baseContext);

      page = await boProductSettingsPage.viewMyShop(page);
      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage, 'Home page was not opened').to.eq(true);
    });

    it('should go to first product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToFirstProductPage${index}`, baseContext);

      await foClassicHomePage.goToProductPage(page, 1);

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle.toUpperCase()).to.contains(dataProducts.demo_1.name.toUpperCase());
    });

    it('should check the existence of the unit value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkUnitValue${index}`, baseContext);

      const columnTitle = await foClassicProductPage.getDiscountColumnTitle(page);
      expect(columnTitle).to.equal(test.args.textColumnToCheck);

      const columnValue = await foClassicProductPage.getDiscountValue(page);
      expect(columnValue).to.equal(test.args.valueToCheck);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goBackToBo${index}`, baseContext);

      page = await foClassicProductPage.closePage(browserContext, page, 0);

      const pageTitle = await boProductSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductSettingsPage.pageTitle);
    });
  });

  it('should go to \'Catalog > Discounts\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToDiscountsPageToDeletePriceRule', baseContext);

    await boProductSettingsPage.goToSubMenu(
      page,
      boProductSettingsPage.catalogParentLink,
      boProductSettingsPage.discountsLink,
    );

    const pageTitle = await boCartRulesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boCartRulesPage.pageTitle);
  });

  it('should go to \'Catalog Price Rules\' tab', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCatalogPriceRuleTabToDeletePriceRule', baseContext);

    await boCartRulesPage.goToCatalogPriceRulesTab(page);

    const pageTitle = await boCatalogPriceRulesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boCatalogPriceRulesPage.pageTitle);
  });

  it('should delete catalog price rule', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'deleteCatalogPriceRule', baseContext);

    const deleteTextResult = await boCatalogPriceRulesPage.deleteCatalogPriceRule(page, priceRuleData.name);
    expect(deleteTextResult).to.contains(boCatalogPriceRulesPage.successfulDeleteMessage);
  });
});
