// Import pages
import FOBasePage from '@pages/FO/classic/FObasePage';

/**
 * Delivery page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class DeliveryPage extends FOBasePage {
  public readonly pageTitle: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on delivery page
   */
  constructor(theme: string = 'classic') {
    super(theme);

    this.pageTitle = 'Delivery';
  }
}

const deliveryPage = new DeliveryPage();
export {deliveryPage, DeliveryPage};
