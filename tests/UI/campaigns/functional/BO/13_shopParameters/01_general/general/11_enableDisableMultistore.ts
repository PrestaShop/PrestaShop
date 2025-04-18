// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  boShopParametersPage,
  type BrowserContext,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_shopParameters_general_general_enableDisableMultistore';

/*
Enable/Disable multistore
Check the existence of multistore page
 */
describe('BO - Shop Parameters - General : Enable/Disable multi store', async () => {
  let browserContext: BrowserContext;
  let page: Page;

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

  it('should go to \'Shop parameters > General\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToGeneralPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.shopParametersParentLink,
      boDashboardPage.shopParametersGeneralLink,
    );
    await boShopParametersPage.closeSfToolBar(page);

    const pageTitle = await boShopParametersPage.getPageTitle(page);
    expect(pageTitle).to.contains(boShopParametersPage.pageTitle);
  });

  const tests = [
    {args: {action: 'Enable', exist: true}},
    {args: {action: 'Disable', exist: false}},
  ];

  tests.forEach((test, index: number) => {
    describe(`${test.args.action} Display Multistore`, async () => {
      it(`should ${test.args.action} multi store`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}MultiStore`, baseContext);

        const result = await boShopParametersPage.setMultiStoreStatus(page, test.args.exist);
        expect(result).to.contains(boShopParametersPage.successfulUpdateMessage);
      });

      it('should check the existence of \'Advanced Parameters > Multistore\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToMultiStorePage_${index}`, baseContext);

        const result = await boShopParametersPage.isSubmenuVisible(
          page,
          boShopParametersPage.advancedParametersLink,
          boShopParametersPage.multistoreLink,
        );
        expect(result).to.be.equal(test.args.exist);
      });
    });
  });
});
