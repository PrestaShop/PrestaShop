// Import FO pages
import {ContactUsPage} from '@pages/FO/classic/contactUs/index';

/**
 * Contact Us page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class ContactUs extends ContactUsPage {
  /**
   * @constructs
   */
  constructor() {
    super('hummingbird');

    this.attachmentLabel = 'input[name="fileUpload"]';
  }
}

export default new ContactUs();
