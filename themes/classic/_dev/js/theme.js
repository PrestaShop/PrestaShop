/* expose jQuery for modules */
import $ from 'expose?$!expose?jQuery!jquery';
import 'expose?Tether!tether';
import 'bootstrap/dist/js/npm';
import 'flexibility';

import '../css/theme';
import './checkout';
import './customer';
import './facets';

import DropDown from './components/drop-down';
import TopMenu from './components/top-menu';
import ProductMinitature from './components/product-miniature';

import prestashop from 'prestashop';
import EventEmitter from 'events';
import {
  psShowHide
}
from './common';

import 'bootstrap-datepicker/dist/js/bootstrap-datepicker.min';
import 'bootstrap-validator/dist/validator.min';

// "inherit" EventEmitter
for (var i in EventEmitter.prototype) {
  prestashop[i] = EventEmitter.prototype[i];
}

$(document).ready(() => {
  let dropDownEl = $('.js-dropdown');
  let topMenuEl = $('.js-top-menu ul');
  let dropDown = new DropDown(dropDownEl).init();
  let topMenu = new TopMenu(topMenuEl).init();
  let productMinitature = new ProductMinitature().init();
  psShowHide();
});
