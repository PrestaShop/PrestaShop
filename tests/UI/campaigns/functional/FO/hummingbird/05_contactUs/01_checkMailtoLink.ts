// Import utils
import testContext from '@utils/testContext';

// Import common tests
import {enableTheme, disableTheme} from '@commonTests/BO/design/hummingbird';

import {expect} from 'chai';
import {
  type BrowserContext,
  dataEmployees,
  foHummingbirdContactUsPage,
  foHummingbirdHomePage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_hummingbird_contactUs_checkMailtoLink';

/*
Pre-condition:
- Install hummingbird theme
Scenario:
- Go to FO
- Click on contact us link
- Check email us link
Post-condition:
- Uninstall hummingbird theme
 */
describe('FO - Contact us : Check mail link on contact us page', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Pre-condition : Install Hummingbird
  enableTheme('hummingbird', `${baseContext}_preTest`);

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Check mail link on contact us page', async () => {
    it('should go to FO home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

      await foHummingbirdHomePage.goToFo(page);

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should go to \'Contact us\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToContactUsPage', baseContext);

      await foHummingbirdHomePage.clickOnHeaderLink(page, 'Contact us');

      const pageTitle = await foHummingbirdContactUsPage.getPageTitle(page);
      expect(pageTitle, 'Fail to open FO login page').to.contains(foHummingbirdContactUsPage.pageTitle);
    });

    it('should check email us link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkEmailUsLink', baseContext);

      const emailUsLinkHref = await foHummingbirdContactUsPage.getEmailUsLink(page);
      expect(emailUsLinkHref).to.equal(`mailto:${dataEmployees.defaultEmployee.email}`);
    });
  });

  // Post-condition : Uninstall Hummingbird
  disableTheme('hummingbird', `${baseContext}_postTest`);
});
