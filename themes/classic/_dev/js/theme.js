/* global document */
/* expose jQuery for modules */

import $ from 'expose?$!expose?jQuery!jquery';
import 'expose?Tether!tether';
import 'bootstrap/dist/js/npm';
import 'flexibility';

import '../css/theme';
import './checkout';
import './customer';
import './facets';
import './product';

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
  let dropDown = new DropDown(dropDownEl);
  let topMenu = new TopMenu(topMenuEl);
  let productMinitature = new ProductMinitature();

  dropDown.init();
  topMenu.init();
  productMinitature.init();

  psShowHide();
});
