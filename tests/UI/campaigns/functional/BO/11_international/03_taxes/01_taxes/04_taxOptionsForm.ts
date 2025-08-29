import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boTaxesPage,
  type BrowserContext,
  dataTaxOptions,
  FakerTaxOption,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_international_taxes_taxes_taxOptionsForm';

// Edit Tax options
describe('BO - International - Taxes : Edit Tax options with all EcoTax values', async () => {
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

  it('should go to \'International > Taxes\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToTaxesPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.internationalParentLink,
      boDashboardPage.taxesLink,
    );
    await boTaxesPage.closeSfToolBar(page);

    const pageTitle = await boTaxesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boTaxesPage.pageTitle);
  });

  // Testing all options of EcoTax
  describe('Edit tax options', async () => {
    dataTaxOptions.forEach((taxOption: FakerTaxOption, index: number) => {
      it(`should edit Tax Option,
      \tEnable Tax:${taxOption.enabled},
      \tDisplay tax in the shopping cart: '${taxOption.displayInShoppingCart}',
      \tBased on: '${taxOption.basedOn}',
      \tUse ecotax: '${taxOption.useEcoTax}',
      \tEcotax: '${taxOption.ecoTax}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `updateForm${index + 1}`, baseContext);

        const textResult = await boTaxesPage.updateTaxOption(page, taxOption);
        expect(textResult).to.be.equal('Update successful');
      });
    });
  });
});
