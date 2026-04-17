/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import {EventEmitter} from '@components/event-emitter';
import RedirectOptionManager from '@pages/category/edit/manager/redirect-option-manager';

const {$} = window;

$(() => {
  new RedirectOptionManager(EventEmitter);
});
