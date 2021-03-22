window.translation_settings=function(e){function t(r){if(n[r])return n[r].exports;var o=n[r]={i:r,l:!1,exports:{}};return e[r].call(o.exports,o,o.exports,t),o.l=!0,o.exports}var n={};return t.m=e,t.c=n,t.i=function(e){return e},t.d=function(e,n,r){t.o(e,n)||Object.defineProperty(e,n,{configurable:!1,enumerable:!0,get:r})},t.n=function(e){var n=e&&e.__esModule?function(){return e.default}:function(){return e};return t.d(n,"a",n),n},t.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},t.p="",t(t.s=540)}({0:function(e,t,n){"use strict";t.__esModule=!0,t.default=function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}},1:function(e,t,n){"use strict";t.__esModule=!0;var r=n(19),o=function(e){return e&&e.__esModule?e:{default:e}}(r);t.default=function(){function e(e,t){for(var n=0;n<t.length;n++){var r=t[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),(0,o.default)(e,r.key,r)}}return function(t,n,r){return n&&e(t.prototype,n),r&&e(t,r),t}}()},10:function(e,t,n){var r=n(6),o=n(12);e.exports=n(2)?function(e,t,n){return r.f(e,t,o(1,n))}:function(e,t,n){return e[t]=n,e}},11:function(e,t,n){var r=n(4);e.exports=function(e){if(!r(e))throw TypeError(e+" is not an object!");return e}},12:function(e,t){e.exports=function(e,t){return{enumerable:!(1&e),configurable:!(2&e),writable:!(4&e),value:t}}},13:function(e,t,n){var r=n(4);e.exports=function(e,t){if(!r(e))return e;var n,o;if(t&&"function"==typeof(n=e.toString)&&!r(o=n.call(e)))return o;if("function"==typeof(n=e.valueOf)&&!r(o=n.call(e)))return o;if(!t&&"function"==typeof(n=e.toString)&&!r(o=n.call(e)))return o;throw TypeError("Can't convert object to primitive value")}},15:function(e,t,n){var r=n(18);e.exports=function(e,t,n){if(r(e),void 0===t)return e;switch(n){case 1:return function(n){return e.call(t,n)};case 2:return function(n,r){return e.call(t,n,r)};case 3:return function(n,r,o){return e.call(t,n,r,o)}}return function(){return e.apply(t,arguments)}}},16:function(e,t,n){var r=n(4),o=n(5).document,i=r(o)&&r(o.createElement);e.exports=function(e){return i?o.createElement(e):{}}},17:function(e,t,n){e.exports=!n(2)&&!n(7)(function(){return 7!=Object.defineProperty(n(16)("div"),"a",{get:function(){return 7}}).a})},18:function(e,t){e.exports=function(e){if("function"!=typeof e)throw TypeError(e+" is not a function!");return e}},19:function(e,t,n){e.exports={default:n(20),__esModule:!0}},2:function(e,t,n){e.exports=!n(7)(function(){return 7!=Object.defineProperty({},"a",{get:function(){return 7}}).a})},20:function(e,t,n){n(21);var r=n(3).Object;e.exports=function(e,t,n){return r.defineProperty(e,t,n)}},21:function(e,t,n){var r=n(8);r(r.S+r.F*!n(2),"Object",{defineProperty:n(6).f})},3:function(e,t){var n=e.exports={version:"2.4.0"};"number"==typeof __e&&(__e=n)},4:function(e,t){e.exports=function(e){return"object"==typeof e?null!==e:"function"==typeof e}},428:function(e,t,n){"use strict";function r(e){return e&&e.__esModule?e:{default:e}}Object.defineProperty(t,"__esModule",{value:!0});var o=n(0),i=r(o),u=n(539),f=r(u),a=function e(){(0,i.default)(this,e),new f.default};/**
    * Copyright since 2007 PrestaShop SA and Contributors
    * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
    *
    * NOTICE OF LICENSE
    *
    * This source file is subject to the Open Software License (OSL 3.0)
    * that is bundled with this package in the file LICENSE.md.
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
    * needs please refer to https://devdocs.prestashop.com/ for more information.
    *
    * @author    PrestaShop SA and Contributors <contact@prestashop.com>
    * @copyright Since 2007 PrestaShop SA and Contributors
    * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
    */
t.default=a},5:function(e,t){var n=e.exports="undefined"!=typeof window&&window.Math==Math?window:"undefined"!=typeof self&&self.Math==Math?self:Function("return this")();"number"==typeof __g&&(__g=n)},539:function(e,t,n){"use strict";function r(e){return e&&e.__esModule?e:{default:e}}Object.defineProperty(t,"__esModule",{value:!0});var o=n(0),i=r(o),u=n(1),f=r(u),a=window.$,c=function(){function e(){(0,i.default)(this,e),a(".js-translation-type").on("change",this.toggleFields.bind(this)),a(".js-email-content-type").on("change",this.toggleEmailFields.bind(this)),this.toggleFields()}return(0,f.default)(e,[{key:"toggleFields",value:function(){var e=a(".js-translation-type").val(),t=a(".js-module-form-group"),n=a(".js-email-form-group"),r=a(".js-theme-form-group"),o=r.find("select"),i=o.find(".js-no-theme"),u=o.find("option:not(.js-no-theme):first");switch(e){case"back":case"others":this._hide(t,n,r);break;case"themes":i.is(":selected")&&o.val(u.val()),this._hide(t,n,i),this._show(r);break;case"modules":this._hide(n,r),this._show(t);break;case"mails":this._hide(t,r),this._show(n)}this.toggleEmailFields()}},{key:"toggleEmailFields",value:function(){if("mails"===a(".js-translation-type").val()){var e=a(".js-email-form-group").find("select").val(),t=a(".js-theme-form-group"),n=t.find(".js-no-theme");"body"===e?(n.prop("selected",!0),this._show(n,t)):this._hide(n,t)}}},{key:"_hide",value:function(){for(var e=arguments.length,t=Array(e),n=0;n<e;n++)t[n]=arguments[n];for(var r in t)t[r].addClass("d-none"),t[r].find("select").prop("disabled","disabled")}},{key:"_show",value:function(){for(var e=arguments.length,t=Array(e),n=0;n<e;n++)t[n]=arguments[n];for(var r in t)t[r].removeClass("d-none"),t[r].find("select").prop("disabled",!1)}}]),e}();t.default=c},540:function(e,t,n){"use strict";var r=n(428),o=function(e){return e&&e.__esModule?e:{default:e}}(r);/**
                   * Copyright since 2007 PrestaShop SA and Contributors
                   * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
                   *
                   * NOTICE OF LICENSE
                   *
                   * This source file is subject to the Open Software License (OSL 3.0)
                   * that is bundled with this package in the file LICENSE.md.
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
                   * needs please refer to https://devdocs.prestashop.com/ for more information.
                   *
                   * @author    PrestaShop SA and Contributors <contact@prestashop.com>
                   * @copyright Since 2007 PrestaShop SA and Contributors
                   * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
                   */
(0,window.$)(function(){new o.default})},6:function(e,t,n){var r=n(11),o=n(17),i=n(13),u=Object.defineProperty;t.f=n(2)?Object.defineProperty:function(e,t,n){if(r(e),t=i(t,!0),r(n),o)try{return u(e,t,n)}catch(e){}if("get"in n||"set"in n)throw TypeError("Accessors not supported!");return"value"in n&&(e[t]=n.value),e}},7:function(e,t){e.exports=function(e){try{return!!e()}catch(e){return!0}}},8:function(e,t,n){var r=n(5),o=n(3),i=n(15),u=n(10),f=function(e,t,n){var a,c,s,l=e&f.F,d=e&f.G,p=e&f.S,h=e&f.P,v=e&f.B,y=e&f.W,_=d?o:o[t]||(o[t]={}),g=_.prototype,w=d?r:p?r[t]:(r[t]||{}).prototype;d&&(n=t);for(a in n)(c=!l&&w&&void 0!==w[a])&&a in _||(s=c?w[a]:n[a],_[a]=d&&"function"!=typeof w[a]?n[a]:v&&c?i(s,r):y&&w[a]==s?function(e){var t=function(t,n,r){if(this instanceof e){switch(arguments.length){case 0:return new e;case 1:return new e(t);case 2:return new e(t,n)}return new e(t,n,r)}return e.apply(this,arguments)};return t.prototype=e.prototype,t}(s):h&&"function"==typeof s?i(Function.call,s):s,h&&((_.virtual||(_.virtual={}))[a]=s,e&f.R&&g&&!g[a]&&u(g,a,s)))};f.F=1,f.G=2,f.S=4,f.P=8,f.B=16,f.W=32,f.U=64,f.R=128,e.exports=f}});