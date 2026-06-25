/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import PositionsListHandler from './positions-list-handler';
import HookStatusHandler from './hook-status-handler';
import HookModuleHandler from './hook-module-handler';
import ExceptionListHandler from './exception-list-handler';

const {$} = window;

$(() => {
  new PositionsListHandler();
  new HookStatusHandler();
  new HookModuleHandler();
  new ExceptionListHandler();
});
