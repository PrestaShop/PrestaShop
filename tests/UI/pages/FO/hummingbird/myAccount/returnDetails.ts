// Import FO pages
import {ReturnDetails} from '@pages/FO/classic/myAccount/returnDetails';

/**
 * @class
 * @extends FOBasePage
 */
class ReturnDetailsPage extends ReturnDetails {
  /**
   * @constructs
   * Setting up texts and selectors to use
   */
  constructor() {
    super('hummingbird');

    this.pageTitleHeader = '#content-wrapper h1';
    this.alertWarning = '#notifications article.alert-warning';
  }
}

export default new ReturnDetailsPage();
