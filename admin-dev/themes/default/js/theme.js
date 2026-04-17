/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import '../scss/font.scss';
import '../scss/admin-theme.scss';
import 'perfect-scrollbar/css/perfect-scrollbar.css';
import '@openfonts/ubuntu-condensed_latin';

import PerfectScrollBar from 'perfect-scrollbar';

$(() => {
  const $navBarOverflow = $('.nav-bar-overflow');

  if ($navBarOverflow.length > 0) {
    new PerfectScrollBar('.nav-bar-overflow');
  }
});
