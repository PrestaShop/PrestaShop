/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

// Plugins CSS

import 'dropzone/dist/min/dropzone.min.css';

import 'magnific-popup/dist/magnific-popup.css';

// Theme SCSS

import '../scss/theme.scss';

// Theme Javascript

Dropzone.autoDiscover = false;

import NavBar from './nav_bar.js';

// this needs to be ported into the UI kit
import './clickable-dropdown';

import './maintenance-page';
import './product-page/index';
import './translation-page/index';

import Header from './header.js';

new NavBar();
new Header();
