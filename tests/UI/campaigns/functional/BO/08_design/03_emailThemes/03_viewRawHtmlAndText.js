require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const emailThemesPage = require('@pages/BO/design/emailThemes');
const previewEmailThemesPage = require('@pages/BO/design/emailThemes/preview');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_design_emailThemes_viewRawHtmlAndText';

const emailThemeName = 'classic';

let browserContext;
let page;

/*
Go to design > email themes page
Preview classic theme
View email as raw html
View email as text
 */
describe('View raw html and text and check result', async () => {
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

  it('should go to design > email themes page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEmailThemesPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.designParentLink,
      dashboardPage.emailThemeLink,
    );

    await emailThemesPage.closeSfToolBar(page);

    const pageTitle = await emailThemesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(emailThemesPage.pageTitle);
  });

  it('should preview classic email theme', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'previewEmailTheme', baseContext);

    await emailThemesPage.previewEmailTheme(page, emailThemeName);

    const pageTitle = await previewEmailThemesPage.getPageTitle(page);

    await expect(pageTitle).to.contains(
      `${previewEmailThemesPage.pageTitle} ${emailThemeName}`,
    );
  });

  describe('View raw html', async () => {
    let newPage;
    it('should view raw html and check its URL', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewRawHtml', baseContext);

      newPage = await previewEmailThemesPage.viewRawHtml(page, 1);
      await expect(newPage.url())
        .to.contain(emailThemeName)
        .and.to.contain('raw')
        .and.to.contain('.html');
    });

    it('should get text from page and check email format', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkRawHtmlText', baseContext);

      const pageText = await previewEmailThemesPage.getTextFromViewLayoutPage(newPage);
      await expect(pageText)
        .to.contain(global.FO.URL)
        .and.to.contain('<html')
        .and.to.contain('<body')
        .and.to.contain('</body>')
        .and.to.contain('</html>');
    });

    after(() => newPage.close());
  });

  describe('View raw text', async () => {
    let newPage;
    it('should view raw text and check its URL', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewRawText', baseContext);

      newPage = await previewEmailThemesPage.viewRawText(page, 1);
      await expect(newPage.url())
        .to.contain(emailThemeName)
        .and.to.contain('raw')
        .and.to.contain('.txt');
    });

    it('should get text from page and check format', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkRawTextPage', baseContext);

      const pageText = await previewEmailThemesPage.getTextFromViewLayoutPage(newPage);
      await expect(pageText)
        .to.contain(global.FO.URL)
        .and.to.contain('Hi');
    });

    after(() => newPage.close());
  });
});
