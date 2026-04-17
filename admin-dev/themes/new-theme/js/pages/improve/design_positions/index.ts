/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import PositionsListHandler from './positions-list-handler';
import HookStatusHandler from './hook-status-handler';

const {$} = window;

$(() => {
  new PositionsListHandler();
  new HookStatusHandler();
});
