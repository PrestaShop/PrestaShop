// Import FO Pages
import {HomePage} from '@pages/FO/home';

/**
 * Home page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class Home extends HomePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on home page
   */
  constructor() {
    super('hummingbird');
  }
}

export default new Home();
