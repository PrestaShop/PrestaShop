// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import emailThemesPage from '@pages/BO/design/emailThemes';
import previewEmailThemesPage from '@pages/BO/design/emailThemes/preview';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_design_emailTheme_viewRawHtmlAndText';

/*
Go to design > email themes page
Preview classic theme
View email as raw html
View email as text
 */
describe('BO - Design - Email Theme : View raw html and text', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let newPage: Page;

  const emailThemeName: string = 'classic';

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

  it('should go to \'Design > Email Theme\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEmailThemesPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.designParentLink,
      dashboardPage.emailThemeLink,
    );
    await emailThemesPage.closeSfToolBar(page);

    const pageTitle = await emailThemesPage.getPageTitle(page);
    expect(pageTitle).to.contains(emailThemesPage.pageTitle);
  });

  it('should preview classic email theme', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'previewEmailTheme', baseContext);

    await emailThemesPage.previewEmailTheme(page, emailThemeName);

    const pageTitle = await previewEmailThemesPage.getPageTitle(page);
    expect(pageTitle).to.contains(
      `${previewEmailThemesPage.pageTitle} ${emailThemeName}`,
    );
  });

  describe('View raw html', async () => {
    it('should view raw html and check its URL', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewRawHtml', baseContext);

      newPage = await previewEmailThemesPage.viewRawHtml(page, 1);
      expect(newPage.url())
        .to.contain(emailThemeName)
        .and.to.contain('raw')
        .and.to.contain('.html');
    });

    it('should get text from page and check email format', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkRawHtmlText', baseContext);

      const pageText = await previewEmailThemesPage.getTextFromViewLayoutPage(newPage);
      expect(pageText)
        .to.contain(global.FO.URL)
        .and.to.contain('<html')
        .and.to.contain('<body')
        .and.to.contain('</body>')
        .and.to.contain('</html>');
    });

    after(() => newPage.close());
  });

  describe('View raw text', async () => {
    it('should view raw text and check its URL', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewRawText', baseContext);

      newPage = await previewEmailThemesPage.viewRawText(page, 1);
      expect(newPage.url())
        .to.contain(emailThemeName)
        .and.to.contain('raw')
        .and.to.contain('.txt');
    });

    it('should get text from page and check format', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkRawTextPage', baseContext);

      const pageText = await previewEmailThemesPage.getTextFromViewLayoutPage(newPage);
      expect(pageText)
        .to.contain(global.FO.URL)
        .and.to.contain('Hi');
    });

    after(() => newPage.close());
  });
});
