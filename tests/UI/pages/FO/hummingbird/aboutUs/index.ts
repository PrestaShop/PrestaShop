// Import FO pages
import {AboutUsPage} from '@pages/FO/classic/aboutUs/index';

/**
 * Contact Us page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class AboutUs extends AboutUsPage {
  /**
   * @constructs
   */
  constructor() {
    super('hummingbird');
  }
}

export default new AboutUs();
