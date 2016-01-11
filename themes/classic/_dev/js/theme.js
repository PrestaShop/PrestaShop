import $ from 'jquery';
/* expose jQuery for modules */
window.$ = $;
window.jQuery = $;

import '../css/theme';
import './checkout';
import './facets';

import DropDown from './components/drop-down';
import TopMenu from './components/top-menu';

import prestashop from 'prestashop';
import EventEmitter from 'events';
import {
  psShowHide
}
from './common';

// "inherit" EventEmitter
for (var i in EventEmitter.prototype) {
  prestashop[i] = EventEmitter.prototype[i];
}

$(document).ready(() => {
  let dropDown = new DropDown();
  let topMenu = new TopMenu();
  psShowHide();
});
