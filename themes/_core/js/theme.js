import $ from 'jquery';
/* expose jQuery for modules */
window.$ = $;
window.jQuery = $;

import 'jquery-migrate';
import 'jquery.browser';
import '@prestashop-core/jquery.live-polyfill';
import './cart';
import './checkout';
import './facets';
import './listing';
import './product';
import './address';

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
