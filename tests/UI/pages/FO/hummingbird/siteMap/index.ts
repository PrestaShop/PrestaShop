// Import FO pages
import {SiteMapPage} from '@pages/FO/siteMap/index';

/**
 * @class
 * @extends FOBasePage
 */
class SiteMap extends SiteMapPage {
  /**
   * @constructs
   */
  constructor() {
    super('hummingbird');
  }
}

export default new SiteMap();
