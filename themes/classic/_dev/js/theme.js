import $ from 'expose?$!expose?jQuery!jquery';
import 'expose?Tether!tether';
import 'bootstrap/dist/js/npm';
import 'flexibility';
import 'bootstrap-touchspin';

import '../css/theme';
import './checkout';
import './customer';
import './listing';
import './product';
import './cart';

import DropDown from './components/drop-down';
import TopMenu from './components/top-menu';
import ProductMinitature from './components/product-miniature';
import ProductSelect from './components/product-select';

import prestashop from 'prestashop';
import EventEmitter from 'events';

import './lib/bootstrap-filestyle.min';

import './components/block-cart';

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
  let productSelect  = new ProductSelect();
  dropDown.init();
  topMenu.init();
  productMinitature.init();
  productSelect.init();
});
