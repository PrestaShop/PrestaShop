import $ from 'jquery';
import initMessagesVisibilityToggling from './messages-visibility'
import initMessagesEdition from './messages-edition'

$(() => {
    initMessagesVisibilityToggling();
    initMessagesEdition();
});
