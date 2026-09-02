/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import TableSorting from '@app/utils/table-sorting';

const {$} = window;

$(() => {
  new TableSorting($('table.table')).attach();
});
