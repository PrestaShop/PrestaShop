// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boDashboardPage,
  boDesignEmailThemesPage,
  boDesignEmailThemesPreviewPage,
  boLoginPage,
  type BrowserContext,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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

  it('should go to \'Design > Email Theme\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEmailThemesPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.designParentLink,
      boDashboardPage.emailThemeLink,
    );
    await boDesignEmailThemesPage.closeSfToolBar(page);

    const pageTitle = await boDesignEmailThemesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDesignEmailThemesPage.pageTitle);
  });

  it('should preview classic email theme', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'previewEmailTheme', baseContext);

    await boDesignEmailThemesPage.previewEmailTheme(page, emailThemeName);

    const pageTitle = await boDesignEmailThemesPreviewPage.getPageTitle(page);
    expect(pageTitle).to.contains(
      `${boDesignEmailThemesPreviewPage.pageTitle} ${emailThemeName}`,
    );
  });

  describe('View raw html', async () => {
    it('should view raw html and check its URL', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewRawHtml', baseContext);

      newPage = await boDesignEmailThemesPreviewPage.viewRawHtml(page, 1);
      expect(newPage.url())
        .to.contain(emailThemeName)
        .and.to.contain('raw')
        .and.to.contain('.html');
    });

    it('should get text from page and check email format', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkRawHtmlText', baseContext);

      const pageText = await boDesignEmailThemesPreviewPage.getTextFromViewLayoutPage(newPage);
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

      newPage = await boDesignEmailThemesPreviewPage.viewRawText(page, 1);
      expect(newPage.url())
        .to.contain(emailThemeName)
        .and.to.contain('raw')
        .and.to.contain('.txt');
    });

    it('should get text from page and check format', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkRawTextPage', baseContext);

      const pageText = await boDesignEmailThemesPreviewPage.getTextFromViewLayoutPage(newPage);
      expect(pageText)
        .to.contain(global.FO.URL)
        .and.to.contain('Hi');
    });

    after(() => newPage.close());
  });
});
