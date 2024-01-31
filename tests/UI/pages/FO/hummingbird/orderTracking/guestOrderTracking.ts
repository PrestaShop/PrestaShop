// Import FO pages
import {GuestOrderTrackingPage} from '@pages/FO/classic/orderTracking/guestOrderTracking';

/**
 * @class
 * @extends FOBasePage
 */
class GuestOrderTracking extends GuestOrderTrackingPage {
  /**
    * @constructs
    */
  constructor() {
    super('hummingbird');
  }
}

export default new GuestOrderTracking();
