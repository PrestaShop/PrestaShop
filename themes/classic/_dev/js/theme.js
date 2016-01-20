/* expose jQuery for modules */
import $ from 'expose?$!expose?jQuery!jquery';
import 'expose?Tether!tether';
import 'bootstrap/dist/js/npm';

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
  let dropDownEl = $('.js-dropdown');
  let topMenuEl = $('.js-top-menu ul');
  let dropDown = new DropDown(dropDownEl).init();
  let topMenu = new TopMenu(topMenuEl).init();
  psShowHide();
});
