// Import pages
import FOBasePage from '@pages/FO/FObasePage';

import type {Page} from 'playwright';

/**
 * Addresses page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class AddressesPage extends FOBasePage {
  public readonly pageTitle: string;

  public readonly addressPageTitle: string;

  public readonly addAddressSuccessfulMessage: string;

  public readonly updateAddressSuccessfulMessage: string;

  public readonly deleteAddressSuccessfulMessage: string;

  public readonly deleteAddressErrorMessage: string;

  private readonly addressBlock: string;

  public addressBodyTitle: string;

  public createNewAddressLink: string;

  private readonly editAddressLink: string;

  private readonly deleteAddressLink: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on addresses page
   */
  constructor(theme: string = 'classic') {
    super(theme);

    this.pageTitle = 'Addresses';
    this.addressPageTitle = 'Address';
    this.addAddressSuccessfulMessage = 'Address successfully added.';
    this.updateAddressSuccessfulMessage = 'Address successfully updated.';
    this.deleteAddressSuccessfulMessage = 'Address successfully deleted.';
    this.deleteAddressErrorMessage = 'Could not delete the address since it is used in the shopping cart.';

    // Selectors
    this.addressBlock = 'article.address';
    this.addressBodyTitle = `${this.addressBlock} .address-body h4`;
    this.createNewAddressLink = '#content div.addresses-footer a[data-link-action=\'add-address\']';
    this.editAddressLink = 'a[data-link-action=\'edit-address\']';
    this.deleteAddressLink = 'a[data-link-action=\'delete-address\']';
  }

  /*
  Methods
   */
  /**
   * Open create new address form
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   * @constructor
   */
  async openNewAddressForm(page: Page): Promise<void> {
    if (await this.elementVisible(page, this.createNewAddressLink, 2000)) {
      await this.clickAndWaitForURL(page, this.createNewAddressLink);
    }
  }

  /**
   * Get address position from its alias
   * @param page {Page} Browser tab
   * @param alias {string} Alias of the address
   * @return {Promise<number>}
   */
  async getAddressPosition(page: Page, alias: string): Promise<number> {
    const titles = await page.locator(this.addressBodyTitle).allTextContents();

    return titles.indexOf(alias) + 1;
  }

  /**
   * Go to edit address page in FO
   * @param page {Page} Browser tab
   * @param position {string} String of the position
   * @returns {Promise<void>}
   */
  async goToEditAddressPage(page: Page, position: string | number = 'last'): Promise<void> {
    const editButtonsLocators = page.locator(this.editAddressLink);
    const positionEditButtons: number = typeof position === 'string'
      ? ((await editButtonsLocators.count()) - 1) : (position - 1);
    const currentUrl: string = page.url();

    await Promise.all([
      page.waitForURL((url: URL): boolean => url.toString() !== currentUrl, {waitUntil: 'networkidle'}),
      editButtonsLocators.nth(positionEditButtons).click(),
    ]);
  }

  /**
   * Delete address in FO
   * @param page {Page} Browser tab
   * @param position {string} String of the position
   * @returns {Promise<string>}
   */
  async deleteAddress(page: Page, position: string | number = 'last'): Promise<string> {
    const deleteButtonsLocator = page.locator(this.deleteAddressLink);
    const positionDeleteButtons: number = typeof position === 'string'
      ? ((await deleteButtonsLocator.count()) - 1) : (position - 1);

    await Promise.all([
      page.waitForLoadState(),
      deleteButtonsLocator.nth(positionDeleteButtons).click(),
    ]);

    return this.getTextContent(page, this.notificationsBlock);
  }
}

const addressesPage = new AddressesPage();
export {addressesPage, AddressesPage};
