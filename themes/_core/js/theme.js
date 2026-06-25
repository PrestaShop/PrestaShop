/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import $ from 'jquery';
import './migrate-mute';
import 'jquery-migrate';
import './selectors';
import './cart';
import './checkout';
import './facets';
import './listing';
import './product';
import './address';

import {psShowHide} from './common';
import initEmailFields from './email-idn';

// Must be set after static imports (which are hoisted) but before any dynamic import()
// is triggered at runtime, so webpack resolves chunk URLs correctly.
// Do not move this line above the import declarations.
// eslint-disable-next-line camelcase, no-undef
__webpack_public_path__ = window.prestashop.core_js_public_path;

window.$ = $;
window.jQuery = $;

$(() => {
  psShowHide();
  initEmailFields('input[type="email"]');
});
