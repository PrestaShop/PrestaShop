import $ from 'jquery';
import initMessagesVisibilityToggling from './messages-visibility'
import initMessagesEdition from './messages-edition'
import initMessagesPagination from './messages-pagination'
import initMessagesTree from './messages-tree'
import initSearch from './messages-search'

$(() => {
  initMessagesVisibilityToggling(initMessagesPagination);
  var search = initSearch();
  initMessagesEdition(search);
  initMessagesTree()
});
