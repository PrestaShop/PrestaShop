window.translation_settings=function(e){function t(o){if(n[o])return n[o].exports;var i=n[o]={i:o,l:!1,exports:{}};return e[o].call(i.exports,i,i.exports,t),i.l=!0,i.exports}var n={};return t.m=e,t.c=n,t.i=function(e){return e},t.d=function(e,n,o){t.o(e,n)||Object.defineProperty(e,n,{configurable:!1,enumerable:!0,get:o})},t.n=function(e){var n=e&&e.__esModule?function(){return e.default}:function(){return e};return t.d(n,"a",n),n},t.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},t.p="",t(t.s=404)}({301:function(e,t,n){"use strict";function o(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(t,"__esModule",{value:!0});var i=n(403),r=function(e){return e&&e.__esModule?e:{default:e}}(i),s=function e(){o(this,e),new r.default};t.default=s},403:function(e,t,n){"use strict";function o(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(t,"__esModule",{value:!0});var i=function(){function e(e,t){for(var n=0;n<t.length;n++){var o=t[n];o.enumerable=o.enumerable||!1,o.configurable=!0,"value"in o&&(o.writable=!0),Object.defineProperty(e,o.key,o)}}return function(t,n,o){return n&&e(t.prototype,n),o&&e(t,o),t}}(),r=window,s=r.$,a=function(){function e(){o(this,e),s(".js-translation-type").on("change",this.toggleFields.bind(this)),s(".js-email-content-type").on("change",this.toggleEmailFields.bind(this)),this.toggleFields()}return i(e,[{key:"toggleFields",value:function(){var e=s(".js-translation-type").val(),t=s(".js-module-form-group"),n=s(".js-email-form-group"),o=s(".js-theme-form-group"),i=o.find("select"),r=i.find(".js-no-theme"),a=i.find("option:not(.js-no-theme):first");switch(e){case"back":case"others":this.hide(t,n,o);break;case"themes":r.is(":selected")&&i.val(a.val()),this.hide(t,n,r),this.show(o);break;case"modules":this.hide(n,o),this.show(t);break;case"mails":this.hide(t,o),this.show(n)}this.toggleEmailFields()}},{key:"toggleEmailFields",value:function(){if("mails"===s(".js-translation-type").val()){var e=s(".js-email-form-group").find("select").val(),t=s(".js-theme-form-group"),n=t.find(".js-no-theme");"body"===e?(n.prop("selected",!0),this.show(n,t)):this.hide(n,t)}}},{key:"hide",value:function(){for(var e=arguments.length,t=Array(e),n=0;n<e;n++)t[n]=arguments[n];Object.values(t).forEach(function(e){e.addClass("d-none"),e.find("select").prop("disabled","disabled")})}},{key:"show",value:function(){for(var e=arguments.length,t=Array(e),n=0;n<e;n++)t[n]=arguments[n];Object.values(t).forEach(function(e){e.removeClass("d-none"),e.find("select").prop("disabled",!1)})}}]),e}();t.default=a},404:function(e,t,n){"use strict";var o=n(301),i=function(e){return e&&e.__esModule?e:{default:e}}(o);/**
                    * 2007-2019 PrestaShop SA and Contributors
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
                    * needs please refer to https://www.prestashop.com for more information.
                    *
                    * @author    PrestaShop SA <contact@prestashop.com>
                    * @copyright 2007-2019 PrestaShop SA and Contributors
                    * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
                    * International Registered Trademark & Property of PrestaShop SA
                    */
(0,window.$)(function(){new i.default})}});