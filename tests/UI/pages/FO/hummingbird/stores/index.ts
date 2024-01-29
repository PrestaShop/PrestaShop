// Import FO pages
import {StoresPage} from '@pages/FO/classic/stores/index';

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
