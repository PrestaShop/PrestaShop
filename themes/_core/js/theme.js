import $ from 'jquery';

import prestashop from 'prestashop';
import EventEmitter from 'events';

import './migrate-mute';
import 'jquery-migrate';
import 'jquery.browser';
import '@prestashop-core/jquery.live-polyfill';

// "inherit" EventEmitter
for (var i in EventEmitter.prototype) {
  prestashop[i] = EventEmitter.prototype[i];
}

import './selectors';
import './cart';
import './checkout';
import './facets';
import './listing';
import './product';
import './address';

import {psShowHide} from './common';
import initEmailFields from './email-idn';
/* expose jQuery for modules */
window.$ = $;
window.jQuery = $;

$(document).ready(() => {
  psShowHide();
  initEmailFields('input[type="email"]');
});
