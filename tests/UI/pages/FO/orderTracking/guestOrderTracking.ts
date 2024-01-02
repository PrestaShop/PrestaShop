import FOBasePage from '@pages/FO/FObasePage';

/**
 * Guest order tracking page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class GuestOrderTrackingPage extends FOBasePage {
  public readonly pageTitle: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on Guest order tracking page
   */
  constructor(theme: string = 'classic') {
    super(theme);

    this.pageTitle = 'Guest tracking';

    // Selectors for the page
  }
}

const guestOrderTrackingPage = new GuestOrderTrackingPage();
export {guestOrderTrackingPage, GuestOrderTrackingPage};
