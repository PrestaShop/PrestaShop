require('module-alias/register');

// Using chai
const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const moduleManagerPage = require('@pages/BO/modules/moduleManager');
const moduleConfigurationPage = require('@pages/BO/modules/moduleConfiguration');

// Import data
const {contactForm} = require('@data/demo/modules');

const baseContext = 'functional_BO_modules_moduleManager_configureModule';

let browserContext;
let page;

describe('BO - Modules - Module Manager : Configure module', async () => {
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

  it('should go to \'Modules > Module Manager\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToModuleManagerPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.modulesParentLink,
      dashboardPage.moduleManagerLink,
    );

    await moduleManagerPage.closeSfToolBar(page);

    const pageTitle = await moduleManagerPage.getPageTitle(page);
    await expect(pageTitle).to.contains(moduleManagerPage.pageTitle);
  });

  it(`should search for module ${contactForm.name}`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchForModule', baseContext);

    const isModuleVisible = await moduleManagerPage.searchModule(page, contactForm.tag, contactForm.name);
    await expect(isModuleVisible).to.be.true;
  });

  it('should go to module configuration page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'configureModule', baseContext);

    await moduleManagerPage.goToConfigurationPage(page, contactForm.name);
    const pageSubtitle = await moduleConfigurationPage.getPageSubtitle(page);
    await expect(pageSubtitle).to.contains(contactForm.name);
  });
});
