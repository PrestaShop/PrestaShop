// Import FO pages
import {LegalNoticePage} from '@pages/FO/legalNotice/index';

/**
 * Contact Us page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class LegalNotice extends LegalNoticePage {
  /**
   * @constructs
   */
  constructor() {
    super('hummingbird');
  }
}

export default new LegalNotice();
