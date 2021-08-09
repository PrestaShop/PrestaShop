import $ from 'jquery';

import prestashop from 'prestashop';
// eslint-disable-next-line
import EventEmitter from "events";

import './migrate-mute';
import 'jquery-migrate';
import 'jquery.browser';
import '@prestashop-core/jquery.live-polyfill';

import './selectors';
import './cart';
import './checkout';
import './facets';
import './listing';
import './product';
import './address';

import {psShowHide} from './common';
import initEmailFields from './email-idn';

/* eslint-disable */
// "inherit" EventEmitter
for (const i in EventEmitter.prototype) {
  prestashop[i] = EventEmitter.prototype[i];
}
/* expose jQuery for modules */
/* eslint-enable */
window.$ = $;
window.jQuery = $;

$(document).ready(() => {
  psShowHide();
  initEmailFields('input[type="email"]');
});
