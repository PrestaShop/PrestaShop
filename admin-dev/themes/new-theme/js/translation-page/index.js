import $ from 'jquery';
import initMessagesVisibilityToggling from './messages-visibility'
import initMessagesEdition from './messages-edition'
import initMessagesPagination from './messages-pagination'

$(() => {
    initMessagesVisibilityToggling();
    initMessagesEdition();
    initMessagesPagination();
});
