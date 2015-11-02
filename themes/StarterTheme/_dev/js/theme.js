import $ from 'jquery';
/* expose jQuery for modules */
window.$ = $;

import './setup-rivets';
import './checkout';

import prestashop from 'prestashop';
import EventEmitter from 'events';

// "inherit" EventEmitter
for (var i in EventEmitter.prototype) {
    prestashop[i] = EventEmitter.prototype[i];
}

$(document).ready(() => {
    $('.ps-shown-by-js').show();
    $('.ps-hidden-by-js').hide();
});
