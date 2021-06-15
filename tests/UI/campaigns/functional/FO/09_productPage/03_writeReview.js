require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
// BO pages
const dashboardPage = require('@pages/BO/dashboard');
const moduleManagerPage = require('@pages/BO/modules/moduleManager');
const moduleConfigurationPage = require('@pages/BO/modules/moduleConfiguration');
const productcommentsModulePage = require('@pages/BO/modules/productComments');
// FO pages
const foHomePage = require('@pages/FO/home');
const foLoginPage = require('@pages/FO/login');
const productPage = require('@pages/FO/product');

// Import datas
const {DefaultCustomer} = require('@data/demo/customer');
const ProductReviewData = require('@data/faker/productReview');
const ProductData = require('@data/FO/product');

const productReviewData = new ProductReviewData();


// Import test context
const testContext = require('@utils/testContext');


// context
const baseContext = 'functional_FO_productPage_writeReview';

let browserContext;
let page;
let foCommentCount;
let waitingReviewsCount;
let approvedReviewsCount;
let reportedReviewsCount;
const moduleName = 'Product Comments';
const moduleTag = 'productcomments';

/*
Go to the FO and login
Go to a product detail page
Add a review on for this product
Logout from FO
Go to the BO and login
Go to the "product comments" module configuration page
Check if the review is visible in the "Reviews waiting for approval" table and if the content is correct
Approve the review
Check if the review is visible in the "Approved review" table and if the content is correct
Logout from BO
Go to the FO and login
Go to product detail page
Check if the review is visible and if the content is correct
 */
describe('FO write a review', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Go to FO product detail page and add a review', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openFoShop', baseContext);

      await foHomePage.goTo(page, global.FO.URL);

      const result = await foHomePage.isHomePage(page);
      await expect(result).to.be.true;
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFOLoginPage', baseContext);

      await foHomePage.goToLoginPage(page);

      const pageHeaderTitle = await foLoginPage.getPageTitle(page);
      await expect(pageHeaderTitle).to.equal(foLoginPage.pageTitle);
    });

    it('should login with default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFo', baseContext);

      await foLoginPage.customerLogin(page, DefaultCustomer);

      const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
      await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
    });

    it('should go to home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToHomePage', baseContext);

      await foHomePage.goToHomePage(page);

      const result = await foHomePage.isHomePage(page);
      await expect(result).to.be.true;
    });

    it('should go to the first product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

      await foHomePage.goToProductPage(page, 1);

      const pageTitle = await productPage.getPageTitle(page);
      await expect(pageTitle.toUpperCase()).to.contains(ProductData.firstProductData.name);
    });

    it('should get and save the actual comment count', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getFoCommentCount', baseContext);

      foCommentCount = await productPage.getNumberOfComment(page);
    });

    it('should add a product review', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductReview', baseContext);

      const isReviewSent = await productPage.addProductReview(page, productReviewData);
      await expect(isReviewSent).to.be.true;
    });

    it('should sign out from FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signOutFO', baseContext);

      await productPage.logout(page);
      const isCustomerConnected = await productPage.isCustomerConnected(page);
      await expect(isCustomerConnected, 'Customer is connected').to.be.false;
    });
  });

  describe('Go to BO and go to "product comments" module configuration page', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginToBO', baseContext);
      await loginCommon.loginBO(this, page);
    });

    it('should go to module manager page', async function () {
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

    it('should search product comments module in module manager page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

      const isModuleVisible = await moduleManagerPage.searchModule(page, moduleTag, moduleName);
      await expect(isModuleVisible).to.be.true;
    });

    it('should go to "product comments" module configuration page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToModuleConfigurationPage', baseContext);

      await moduleManagerPage.goToConfigurationPage(page, moduleName);

      const moduleConfigurationPageSubtitle = await moduleConfigurationPage.getPageSubtitle(page);
      await expect(moduleConfigurationPageSubtitle).to.contains(moduleName);
    });
  });

  describe('Check if review is in "waiting for approval" table and approve it', async () => {
    it('should check if review is in waiting for approval table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkIfReviewInWaitingApproval', baseContext);

      const waitingApprovalReviewData = await productcommentsModulePage.getReviewDataFromTable(page, 'waiting');

      await expect(waitingApprovalReviewData.title).to.contains(productReviewData.reviewTitle);
      await expect(waitingApprovalReviewData.content).to.contains(productReviewData.reviewContent);
      await expect(waitingApprovalReviewData.rating).to.contains(productReviewData.reviewRating);
    });

    it('should approve review', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'approveReview', baseContext);

      await productcommentsModulePage.approveReview(page)
    });
  });

  describe('Check if review is now in "approved review" table', async () => {
    it('should check if review is in "approved review" table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkIfReviewIsApproved', baseContext);

      const approvedReviewData = await productcommentsModulePage.getReviewDataFromTable(page, 'approved');

      await expect(approvedReviewData.title).to.contains(productReviewData.reviewTitle);
      await expect(approvedReviewData.content).to.contains(productReviewData.reviewContent);
      await expect(approvedReviewData.rating).to.contains(productReviewData.reviewRating);
    });

    it('should logout from BO', async function () {
      await loginCommon.logoutBO(this, page);
    });
  });

  describe('Go to FO product detail page and check if review is displayed', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goCheckIfReviewIsDisplayed', baseContext);

      await foHomePage.goTo(page, global.FO.URL);

      const result = await foHomePage.isHomePage(page);
      await expect(result).to.be.true;
    });

    it('should go to the first product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPageToCheckReview', baseContext);

      await foHomePage.goToProductPage(page, 1);

      const pageTitle = await productPage.getPageTitle(page);
      await expect(pageTitle.toUpperCase()).to.contains(ProductData.firstProductData.name);
    });

    it('should get, check and save the comment count', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getCommentCountAfterApprove', baseContext);

      const actualCommentCount = await productPage.getNumberOfComment(page);
      await expect(actualCommentCount).to.equal(foCommentCount + 1);
    });

    it('should check the product review title', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductReviewTitle', baseContext);

      const reviewTitle = await productPage.getReviewTitle(page);
      await expect(reviewTitle).to.contains(productReviewData.reviewTitle);
    });

    it('should check the product review text content', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductReviewTextContent', baseContext);

      const reviewTextContent = await productPage.getReviewTextContent(page);
      await expect(reviewTextContent).to.contains(productReviewData.reviewContent);
    });

    it('should check the product review rating', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductReviewRating', baseContext);

      const reviewRating = await productPage.getReviewRating(page);
      await expect(reviewRating).to.equal(productReviewData.reviewRating);
    });
  });

  describe('Go to BO and delete the approved review', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'secondBoLogin', baseContext);
      await loginCommon.loginBO(this, page);
    });

    it('should go to module manager page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToModuleManagerPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.modulesParentLink,
        dashboardPage.moduleManagerLink,
      );

      await moduleManagerPage.closeSfToolBar(page);

      const pageTitle = await moduleManagerPage.getPageTitle(page);
      await expect(pageTitle).to.contains(moduleManagerPage.pageTitle);
    });

    it('should search product comments module in module manager page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProductCommentsModule', baseContext);

      const isModuleVisible = await moduleManagerPage.searchModule(page, moduleTag, moduleName);
      await expect(isModuleVisible).to.be.true;
    });

    it('should go to "product comments" module configuration page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToModuleConfigPage', baseContext);

      await moduleManagerPage.goToConfigurationPage(page, moduleName);

      const moduleConfigurationPageSubtitle = await moduleConfigurationPage.getPageSubtitle(page);
      await expect(moduleConfigurationPageSubtitle).to.contains(moduleName);
    });

    it('should delete the review in the "approved review" table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteApprovedReview', baseContext);

      await productcommentsModulePage.deleteReview(page, 'approved');
    });

    it('should logout from BO', async function () {
      await loginCommon.logoutBO(this, page);
    });
  });

  describe('Go to product detail page and check if review is deleted', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openFoShop', baseContext);

      await foHomePage.goTo(page, global.FO.URL);

      const result = await foHomePage.isHomePage(page);
      await expect(result).to.be.true;
    });

    it('should go to the first product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

      await foHomePage.goToProductPage(page, 1);

      const pageTitle = await productPage.getPageTitle(page);
      await expect(pageTitle.toUpperCase()).to.contains(ProductData.firstProductData.name);
    });

    it('should check if review block is empty', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

      const actualCommentCount = await productPage.getNumberOfComment(page);
      await expect(actualCommentCount).to.equal(foCommentCount);
    });
  });
});
