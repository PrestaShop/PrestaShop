import testContext from '@utils/testContext';
import setMultiStoreStatus from '@commonTests/BO/advancedParameters/multistore';
import {expect} from 'chai';

import {
  boContactsPage,
  boDashboardPage,
  boLoginPage,
  boMultistorePage,
  boMultistoreShopCreatePage,
  boMultistoreShopPage,
  boMultistoreShopUrlCreatePage,
  boStoresPage,
  type BrowserContext,
  dataStores,
  FakerShop,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_shopParameters_contact_stores_contactDetailsMultistore';

/*
Pre-condition:
- Enable multistore
Scenario (TEST-8905):
- Create a second shop
- Set United States / Alabama for all stores
- Override the State alone on the second shop, then the Country alone (the State follows, emptied)
- Check that all stores keep their values, then uncheck the Country of the second shop (its empty State override goes with it)
- Restore the contact details and delete the second shop
Post-condition:
- Disable multistore
 */
describe('BO - Shop Parameters - Contact : Override contact details per shop', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let shopID: number = 0;

  const createShopData: FakerShop = new FakerShop({name: 'contactDetailsShop', shopGroup: 'Default', categoryRoot: 'Home'});

  // Pre-condition: Enable multistore
  setMultiStoreStatus(true, `${baseContext}_preTest`);

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Create a second shop', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Advanced Parameters > Multistore\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMultiStorePage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.advancedParametersLink,
        boDashboardPage.multistoreLink,
      );
      await boMultistorePage.closeSfToolBar(page);

      const pageTitle = await boMultistorePage.getPageTitle(page);
      expect(pageTitle).to.contains(boMultistorePage.pageTitle);
    });

    it('should go to add new shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewShopPage', baseContext);

      await boMultistorePage.goToNewShopPage(page);

      const pageTitle = await boMultistoreShopCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boMultistoreShopCreatePage.pageTitleCreate);
    });

    it('should create shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createShop', baseContext);

      const textResult = await boMultistoreShopCreatePage.setShop(page, createShopData);
      expect(textResult).to.contains(boMultistorePage.successfulCreationMessage);
    });

    it('should get the id of the new shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getShopID', baseContext);

      const numberOfShops = await boMultistoreShopPage.getNumberOfElementInGrid(page);
      expect(numberOfShops).to.be.above(0);

      shopID = parseInt(await boMultistoreShopPage.getTextColumn(page, 1, 'id_shop'), 10);
    });

    it('should go to add URL', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddURL', baseContext);

      await boMultistoreShopPage.filterTable(page, 'a!name', createShopData.name);
      await boMultistoreShopPage.goToSetURL(page, 1);

      const pageTitle = await boMultistoreShopUrlCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boMultistoreShopUrlCreatePage.pageTitleCreate);
    });

    it('should set URL', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addURL', baseContext);

      const textResult = await boMultistoreShopUrlCreatePage.setVirtualUrl(page, createShopData.name);
      expect(textResult).to.contains(boMultistoreShopUrlCreatePage.successfulCreationMessage);
    });
  });

  describe('Override the contact details per shop', async () => {
    it('should go to \'Shop Parameters > Contact\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToContactPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.shopParametersParentLink,
        boDashboardPage.contactLink,
      );

      const pageTitle = await boContactsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boContactsPage.pageTitle);
    });

    it('should go to stores page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToStoresPage', baseContext);

      await boContactsPage.goToStoresPage(page);

      const pageTitle = await boStoresPage.getPageTitle(page);
      expect(pageTitle).to.contains(boStoresPage.pageTitlePrefix);
    });

    it('should choose \'All stores\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseAllStores', baseContext);

      await boStoresPage.clickOnMultiStoreHeader(page);
      await boStoresPage.chooseAllShops(page);

      const pageTitle = await boStoresPage.getPageTitle(page);
      expect(pageTitle).to.contains(boStoresPage.pageTitlePrefix);
    });

    it('should set United States / Alabama for all stores', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setUnitedStatesForAllStores', baseContext);

      await boStoresPage.selectContactDetailsCountry(page, 'United States');
      await boStoresPage.selectContactDetailsState(page, 'Alabama');

      const textResult = await boStoresPage.saveContactDetails(page);
      expect(textResult).to.contains(boStoresPage.contactFormSuccessfulUpdateMessage);

      const country = await boStoresPage.getContactDetailsSelectedOption(page, 'id_country');
      const state = await boStoresPage.getContactDetailsSelectedOption(page, 'id_state');
      expect(country).to.equal('United States');
      expect(state).to.equal('Alabama');
    });

    it('should choose the second shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseSecondShop', baseContext);

      await boStoresPage.clickOnMultiStoreHeader(page);
      await boStoresPage.chooseShop(page, 2);

      const countryOverridden = await boStoresPage.isContactDetailsOverridden(page, 'id_country');
      const stateOverridden = await boStoresPage.isContactDetailsOverridden(page, 'id_state');
      const countryDisabled = await boStoresPage.isContactDetailsFieldDisabled(page, 'id_country');
      const stateDisabled = await boStoresPage.isContactDetailsFieldDisabled(page, 'id_state');
      expect(countryOverridden).to.equal(false);
      expect(stateOverridden).to.equal(false);
      expect(countryDisabled).to.equal(true);
      expect(stateDisabled).to.equal(true);

      const country = await boStoresPage.getContactDetailsSelectedOption(page, 'id_country');
      const state = await boStoresPage.getContactDetailsSelectedOption(page, 'id_state');
      expect(country).to.equal('United States');
      expect(state).to.equal('Alabama');
    });

    it('should override the State alone', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'overrideStateAlone', baseContext);

      await boStoresPage.setContactDetailsOverride(page, 'id_state', true);

      const stateDisabled = await boStoresPage.isContactDetailsFieldDisabled(page, 'id_state');
      const countryDisabled = await boStoresPage.isContactDetailsFieldDisabled(page, 'id_country');
      expect(stateDisabled).to.equal(false);
      expect(countryDisabled).to.equal(true);

      await boStoresPage.selectContactDetailsState(page, 'Alaska');

      const textResult = await boStoresPage.saveContactDetails(page);
      expect(textResult).to.contains(boStoresPage.contactFormSuccessfulUpdateMessage);
    });

    it('should check that only the State is overridden', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStateOverride', baseContext);

      const stateOverridden = await boStoresPage.isContactDetailsOverridden(page, 'id_state');
      const countryOverridden = await boStoresPage.isContactDetailsOverridden(page, 'id_country');
      expect(stateOverridden).to.equal(true);
      expect(countryOverridden).to.equal(false);

      const state = await boStoresPage.getContactDetailsSelectedOption(page, 'id_state');
      const country = await boStoresPage.getContactDetailsSelectedOption(page, 'id_country');
      expect(state).to.equal('Alaska');
      expect(country).to.equal('United States');
    });

    it('should override the Country alone', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'overrideCountryAlone', baseContext);

      await boStoresPage.setContactDetailsOverride(page, 'id_state', false);
      await boStoresPage.setContactDetailsOverride(page, 'id_country', true);

      const countryDisabled = await boStoresPage.isContactDetailsFieldDisabled(page, 'id_country');
      expect(countryDisabled).to.equal(false);

      await boStoresPage.selectContactDetailsCountry(page, 'France');

      const stateHidden = await boStoresPage.isContactDetailsStateHidden(page);
      expect(stateHidden).to.equal(true);

      const textResult = await boStoresPage.saveContactDetails(page);
      expect(textResult).to.contains(boStoresPage.contactFormSuccessfulUpdateMessage);
    });

    it('should check that the State is overridden and emptied with the Country', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCountryOverride', baseContext);

      const countryOverridden = await boStoresPage.isContactDetailsOverridden(page, 'id_country');
      const stateOverridden = await boStoresPage.isContactDetailsOverridden(page, 'id_state');
      expect(countryOverridden).to.equal(true);
      expect(stateOverridden).to.equal(true);

      const country = await boStoresPage.getContactDetailsSelectedOption(page, 'id_country');
      const state = await boStoresPage.getContactDetailsSelectedOption(page, 'id_state');
      expect(country).to.equal('France');
      expect(state).to.equal('');
    });

    it('should check that all stores keep United States / Alabama', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAllStoresUnchanged', baseContext);

      await boStoresPage.clickOnMultiStoreHeader(page);
      await boStoresPage.chooseAllShops(page);

      const country = await boStoresPage.getContactDetailsSelectedOption(page, 'id_country');
      const state = await boStoresPage.getContactDetailsSelectedOption(page, 'id_state');
      expect(country).to.equal('United States');
      expect(state).to.equal('Alabama');
    });

    it('should remove the overrides of the second shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'removeOverrides', baseContext);

      await boStoresPage.clickOnMultiStoreHeader(page);
      await boStoresPage.chooseShop(page, 2);
      // The State row is hidden (France has no states): unchecking the Country also removes the empty State override
      await boStoresPage.setContactDetailsOverride(page, 'id_country', false);

      const textResult = await boStoresPage.saveContactDetails(page);
      expect(textResult).to.contains(boStoresPage.contactFormSuccessfulUpdateMessage);
    });

    it('should check that the second shop inherits all stores again', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkInheritedAgain', baseContext);

      const countryOverridden = await boStoresPage.isContactDetailsOverridden(page, 'id_country');
      const stateOverridden = await boStoresPage.isContactDetailsOverridden(page, 'id_state');
      expect(countryOverridden).to.equal(false);
      expect(stateOverridden).to.equal(false);

      const country = await boStoresPage.getContactDetailsSelectedOption(page, 'id_country');
      const state = await boStoresPage.getContactDetailsSelectedOption(page, 'id_state');
      expect(country).to.equal('United States');
      expect(state).to.equal('Alabama');
    });

    it('should restore the default contact details for all stores', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'restoreContactDetails', baseContext);

      await boStoresPage.clickOnMultiStoreHeader(page);
      await boStoresPage.chooseAllShops(page);
      // Wait for the States of the restored Country first, or the save still sends Alabama
      await boStoresPage.selectContactDetailsCountry(page, dataStores.contact.country);

      const textResult = await boStoresPage.setContactDetails(page, dataStores.contact);
      expect(textResult).to.contains(boStoresPage.contactFormSuccessfulUpdateMessage);
    });
  });

  describe('Delete the second shop', async () => {
    it('should go back to \'Advanced Parameters > Multistore\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToMultiStorePage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.advancedParametersLink,
        boDashboardPage.multistoreLink,
      );
      await boMultistorePage.closeSfToolBar(page);

      const pageTitle = await boMultistorePage.getPageTitle(page);
      expect(pageTitle).to.contains(boMultistorePage.pageTitle);
    });

    it('should go to the created shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreatedShopPage', baseContext);

      await boMultistorePage.goToShopPage(page, shopID);

      const pageTitle = await boMultistoreShopPage.getPageTitle(page);
      expect(pageTitle).to.contains(createShopData.name);
    });

    it('should delete the shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteShop', baseContext);

      await boMultistoreShopPage.filterTable(page, 'a!name', createShopData.name);

      const textResult = await boMultistoreShopPage.deleteShop(page, 1);
      expect(textResult).to.contains(boMultistoreShopPage.successfulDeleteMessage);
    });
  });

  // Post-condition: Disable multistore
  setMultiStoreStatus(false, `${baseContext}_postTest`);
});
