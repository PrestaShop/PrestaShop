
// Plugins CSS

import 'dropzone/dist/min/dropzone.min.css';
import 'bootstrap-tokenfield/dist/css/bootstrap-tokenfield.min.css';
import 'bootstrap-tokenfield/dist/css/tokenfield-typeahead.min.css';
import 'eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'magnific-popup/dist/magnific-popup.css';
import 'PrestaKit/dist/css/bootstrap-prestashop-ui-kit.css';
import 'PrestaKit/dist/css/jquery.growl.css';
import 'PrestaKit/dist/css/bootstrap-switch.min.css';

// Theme SCSS

import '../scss/theme.scss';

// Theme Javascript

import NavBar from './nav_bar.js';

import './product-page/index';
import Header from './header.js';

new NavBar();
new Header();
