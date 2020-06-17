/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
require('module-alias/register');
const testContext = require('@utils/testContext');

const baseContext = 'sanity_installShop_installShop';
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
// Importing pages
const InstallPage = require('@pages/install');
const HomePage = require('@pages/FO/home');

let browserContext;
let page;

// Init objects needed
const init = async function () {
  return {
    installPage: new InstallPage(page),
    homePage: new HomePage(page),
  };
};

describe('Install Prestashop', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
    this.pageObjects = await init();
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // Steps
  it('should open the Install page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToInstallPage', baseContext);
    await this.pageObjects.installPage.goTo(global.INSTALL.URL);
    const result = await this.pageObjects.installPage.checkStepTitle(
      this.pageObjects.installPage.firstStepPageTitle,
      [
        this.pageObjects.installPage.firstStepFrTitle,
        this.pageObjects.installPage.firstStepEnTitle,
      ],
    );
    await expect(result).to.be.true;
  });

  it('should change language to English and check title', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'ChangeLanguageToEnglish', baseContext);
    await this.pageObjects.installPage.setInstallLanguage();
    const result = await this.pageObjects.installPage.checkStepTitle(
      this.pageObjects.installPage.firstStepPageTitle,
      this.pageObjects.installPage.firstStepEnTitle,
    );
    await expect(result).to.be.true;
  });

  it('should click on next and go to step \'License Agreements\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLicenseAgreements', baseContext);
    await this.pageObjects.installPage.nextStep();
    const result = await this.pageObjects.installPage.checkStepTitle(
      this.pageObjects.installPage.secondStepPageTitle,
      this.pageObjects.installPage.secondStepEnTitle,
    );
    await expect(result).to.be.true;
  });

  it('should agree to terms and conditions and go to step \'System compatibility\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSystemCompatibility', baseContext);
    await this.pageObjects.installPage.agreeToTermsAndConditions();
    await this.pageObjects.installPage.nextStep();
    if (!this.pageObjects.installPage.elementVisible(this.pageObjects.installPage.thirdStepFinishedListItem)) {
      const result = await this.pageObjects.installPage.checkStepTitle(
        this.pageObjects.installPage.thirdStepPageTitle,
        this.pageObjects.installPage.thirdStepEnTitle,
      );
      await expect(result).to.be.true;
    }
  });

  it('should click on next and go to step \'shop Information\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToShopInformation', baseContext);
    if (!this.pageObjects.installPage.elementVisible(this.pageObjects.installPage.thirdStepFinishedListItem)) {
      await this.pageObjects.installPage.nextStep();
    }
    const result = await this.pageObjects.installPage.checkStepTitle(
      this.pageObjects.installPage.fourthStepPageTitle,
      this.pageObjects.installPage.fourthStepEnTitle,
    );
    await expect(result).to.be.true;
  });

  it('should fill shop Information form and go to step \'Database Configuration\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToDatabaseConfiguration', baseContext);
    await this.pageObjects.installPage.fillInformationForm();
    await this.pageObjects.installPage.nextStep();
    const result = await this.pageObjects.installPage.checkStepTitle(
      this.pageObjects.installPage.fifthStepPageTitle,
      this.pageObjects.installPage.fifthStepEnTitle,
    );
    await expect(result).to.be.true;
  });

  it('should fill database configuration form and check database connection', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkDatabaseConnection', baseContext);
    await this.pageObjects.installPage.fillDatabaseForm();
    const result = await this.pageObjects.installPage.isDatabaseConnected();
    await expect(result).to.be.true;
  });

  it('should finish installation and check that installation is successful', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkInstallationSuccessful', baseContext);
    await this.pageObjects.installPage.nextStep();
    const result = await this.pageObjects.installPage.isInstallationSuccessful();
    await expect(result).to.be.true;
  });

  it('should go to FO and check that Prestashop logo exists', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkPrestashopFO', baseContext);
    page = await this.pageObjects.installPage.goToFOAfterInstall();
    this.pageObjects = await init();
    const result = await this.pageObjects.homePage.isHomePage();
    await expect(result).to.be.true;
  });
});
