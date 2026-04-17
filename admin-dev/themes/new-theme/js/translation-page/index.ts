/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import initMessagesVisibilityToggling from './messages-visibility';
import initMessagesEdition from './messages-edition';
import initMessagesPagination from './messages-pagination';
import initMessagesTree from './messages-tree';
import initSearch from './messages-search';

$(() => {
  initMessagesVisibilityToggling(initMessagesPagination);
  const search = initSearch();
  initMessagesEdition(search);
  initMessagesTree();
});
