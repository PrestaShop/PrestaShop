/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import IpInput from './ip-input';

const {$} = window;

$(() => {
  // Do not run if we're not on the maintenance page
  if (!window.location.pathname.match('/configure/shop/maintenance\\b')) {
    return;
  }

  IpInput.init();
});
