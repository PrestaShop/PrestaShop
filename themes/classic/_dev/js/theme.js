import $ from 'jquery';
/* expose jQuery for modules */
window.$ = $;
window.jQuery = $;

import '../css/theme';
import './checkout';
import './facets';
import './product';

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
