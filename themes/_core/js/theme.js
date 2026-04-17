/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
__webpack_public_path__ = window.prestashop.core_js_public_path;

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

window.$ = $;
window.jQuery = $;

$(() => {
  psShowHide();
  initEmailFields('input[type="email"]');
});
