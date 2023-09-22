// Import FO pages
import {StoresPage} from '@pages/FO/stores/index';

/**
 * @class
 * @extends FOBasePage
 */
class Stores extends StoresPage {
  /**
   * @constructs
   */
  constructor() {
    super('hummingbird');
  }
}

export default new Stores();
