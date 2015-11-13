import $ from 'jquery';
/* expose jQuery for modules */
window.$ = $;

import './setup-rivets';
import './checkout';

import prestashop from 'prestashop';
import EventEmitter from 'events';
import {psShowHide} from './common';

// "inherit" EventEmitter
for (var i in EventEmitter.prototype) {
    prestashop[i] = EventEmitter.prototype[i];
}

$(document).ready(() => {
  psShowHide();
});
