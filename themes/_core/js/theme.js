import './prestashop-core';
import $ from 'jquery';
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

/* expose jQuery for modules */
/* eslint-enable */
window.$ = $;
window.jQuery = $;

psShowHide();
initEmailFields('input[type="email"]');
