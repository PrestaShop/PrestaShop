require('module-alias/register');

const {expect} = require('chai');

const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const localizationPage = require('@pages/BO/international/localization');
const languagesPage = require('@pages/BO/international/languages');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_international_localization_languages_helpCard';


let browserContext;
let page;

// Check that help button is in english in languages page
describe('BO - International - Languages : Help Button', async () => {
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

  it('should go to \'International > Localization\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLocalizationPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.internationalParentLink,
      dashboardPage.localizationLink,
    );

    await localizationPage.closeSfToolBar(page);

    const pageTitle = await localizationPage.getPageTitle(page);
    await expect(pageTitle).to.contains(localizationPage.pageTitle);
  });

  it('should go to \'Languages\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLanguagesPage', baseContext);

    await localizationPage.goToSubTabLanguages(page);
    const pageTitle = await languagesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(languagesPage.pageTitle);
  });

  it('should open the help side bar and check the document language', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'openHelpSidebar', baseContext);

    const isHelpSidebarVisible = await languagesPage.openHelpSideBar(page);
    await expect(isHelpSidebarVisible).to.be.true;

    const documentURL = await languagesPage.getHelpDocumentURL(page);
    await expect(documentURL).to.contains('country=en');
  });

  it('should close the help side bar', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'closeHelpSidebar', baseContext);

    const isHelpSidebarVisible = await languagesPage.closeHelpSideBar(page);
    await expect(isHelpSidebarVisible).to.be.true;
  });
});
