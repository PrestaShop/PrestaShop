// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import taxesPage from '@pages/BO/international/taxes';

// Import data
import TaxOptions from '@data/demo/taxOptions';
import TaxOptionData from '@data/faker/taxOption';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_international_taxes_taxes_taxOptionsForm';

// Edit Tax options
describe('BO - International - Taxes : Edit Tax options with all EcoTax values', async () => {
  let browserContext: BrowserContext;
  let page: Page;

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

  it('should go to \'International > Taxes\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToTaxesPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.internationalParentLink,
      dashboardPage.taxesLink,
    );
    await taxesPage.closeSfToolBar(page);

    const pageTitle = await taxesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(taxesPage.pageTitle);
  });

  // Testing all options of EcoTax
  describe('Edit tax options', async () => {
    TaxOptions.forEach((taxOption: TaxOptionData, index: number) => {
      it(`should edit Tax Option,
      \tEnable Tax:${taxOption.enabled},
      \tDisplay tax in the shopping cart: '${taxOption.displayInShoppingCart}',
      \tBased on: '${taxOption.basedOn}',
      \tUse ecotax: '${taxOption.useEcoTax}',
      \tEcotax: '${taxOption.ecoTax}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `updateForm${index + 1}`, baseContext);

        // @todo : https://github.com/PrestaShop/PrestaShop/issues/32914
        if (taxOption.ecoTax === 'FR Taux standard (20%)') {
          this.skip();
          return;
        }

        const textResult = await taxesPage.updateTaxOption(page, taxOption);
        await expect(textResult).to.be.equal('Update successful');
      });
    });
  });
});
