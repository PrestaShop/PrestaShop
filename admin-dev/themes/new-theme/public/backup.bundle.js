window.backup=function(t){function e(o){if(n[o])return n[o].exports;var i=n[o]={i:o,l:!1,exports:{}};return t[o].call(i.exports,i,i.exports,e),i.l=!0,i.exports}var n={};return e.m=t,e.c=n,e.i=function(t){return t},e.d=function(t,n,o){e.o(t,n)||Object.defineProperty(t,n,{configurable:!1,enumerable:!0,get:o})},e.n=function(t){var n=t&&t.__esModule?function(){return t.default}:function(){return t};return e.d(n,"a",n),n},e.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},e.p="",e(e.s=473)}({0:function(t,e,n){"use strict";e.__esModule=!0,e.default=function(t,e){if(!(t instanceof e))throw new TypeError("Cannot call a class as a function")}},1:function(t,e,n){"use strict";e.__esModule=!0;var o=n(19),i=function(t){return t&&t.__esModule?t:{default:t}}(o);e.default=function(){function t(t,e){for(var n=0;n<e.length;n++){var o=e[n];o.enumerable=o.enumerable||!1,o.configurable=!0,"value"in o&&(o.writable=!0),(0,i.default)(t,o.key,o)}}return function(e,n,o){return n&&t(e.prototype,n),o&&t(e,o),e}}()},10:function(t,e,n){var o=n(6),i=n(12);t.exports=n(2)?function(t,e,n){return o.f(t,e,i(1,n))}:function(t,e,n){return t[e]=n,t}},11:function(t,e,n){var o=n(4);t.exports=function(t){if(!o(t))throw TypeError(t+" is not an object!");return t}},12:function(t,e){t.exports=function(t,e){return{enumerable:!(1&t),configurable:!(2&t),writable:!(4&t),value:e}}},13:function(t,e,n){var o=n(4);t.exports=function(t,e){if(!o(t))return t;var n,i;if(e&&"function"==typeof(n=t.toString)&&!o(i=n.call(t)))return i;if("function"==typeof(n=t.valueOf)&&!o(i=n.call(t)))return i;if(!e&&"function"==typeof(n=t.toString)&&!o(i=n.call(t)))return i;throw TypeError("Can't convert object to primitive value")}},15:function(t,e,n){var o=n(18);t.exports=function(t,e,n){if(o(t),void 0===e)return t;switch(n){case 1:return function(n){return t.call(e,n)};case 2:return function(n,o){return t.call(e,n,o)};case 3:return function(n,o,i){return t.call(e,n,o,i)}}return function(){return t.apply(e,arguments)}}},16:function(t,e,n){var o=n(4),i=n(5).document,r=o(i)&&o(i.createElement);t.exports=function(t){return r?i.createElement(t):{}}},17:function(t,e,n){t.exports=!n(2)&&!n(7)(function(){return 7!=Object.defineProperty(n(16)("div"),"a",{get:function(){return 7}}).a})},18:function(t,e){t.exports=function(t){if("function"!=typeof t)throw TypeError(t+" is not a function!");return t}},19:function(t,e,n){t.exports={default:n(20),__esModule:!0}},2:function(t,e,n){t.exports=!n(7)(function(){return 7!=Object.defineProperty({},"a",{get:function(){return 7}}).a})},20:function(t,e,n){n(21);var o=n(3).Object;t.exports=function(t,e,n){return o.defineProperty(t,e,n)}},21:function(t,e,n){var o=n(8);o(o.S+o.F*!n(2),"Object",{defineProperty:n(6).f})},23:function(t,e,n){"use strict";function o(t){return t&&t.__esModule?t:{default:t}}Object.defineProperty(e,"__esModule",{value:!0});var i=n(0),r=o(i),u=n(1),a=o(u),c=window.$,l=function(){function t(e){(0,r.default)(this,t),this.id=e,this.$container=c("#"+this.id+"_grid")}return(0,a.default)(t,[{key:"getId",value:function(){return this.id}},{key:"getContainer",value:function(){return this.$container}},{key:"getHeaderContainer",value:function(){return this.$container.closest(".js-grid-panel").find(".js-grid-header")}},{key:"addExtension",value:function(t){t.extend(this)}}]),t}();e.default=l},3:function(t,e){var n=t.exports={version:"2.4.0"};"number"==typeof __e&&(__e=n)},30:function(t,e,n){"use strict";function o(t){return t&&t.__esModule?t:{default:t}}Object.defineProperty(e,"__esModule",{value:!0});var i=n(0),r=o(i),u=n(1),a=o(u),c=function(){function t(){(0,r.default)(this,t)}return(0,a.default)(t,[{key:"extend",value:function(t){var e=t.getContainer().find(".column-filters");e.find(".grid-search-button").prop("disabled",!0),e.find("input:not(.js-bulk-action-select-all), select").on("input dp.change",function(){e.find(".grid-search-button").prop("disabled",!1),e.find(".js-grid-reset-button").prop("hidden",!1)})}}]),t}();e.default=c},31:function(t,e,n){"use strict";function o(t){return t&&t.__esModule?t:{default:t}}Object.defineProperty(e,"__esModule",{value:!0});var i=n(0),r=o(i),u=n(1),a=o(u),c=window.$,l=function(){function t(){(0,r.default)(this,t)}return(0,a.default)(t,[{key:"extend",value:function(t){this._handleBulkActionCheckboxSelect(t),this._handleBulkActionSelectAllCheckbox(t)}},{key:"_handleBulkActionSelectAllCheckbox",value:function(t){var e=this;t.getContainer().on("change",".js-bulk-action-select-all",function(n){var o=c(n.currentTarget),i=o.is(":checked");i?e._enableBulkActionsBtn(t):e._disableBulkActionsBtn(t),t.getContainer().find(".js-bulk-action-checkbox").prop("checked",i)})}},{key:"_handleBulkActionCheckboxSelect",value:function(t){var e=this;t.getContainer().on("change",".js-bulk-action-checkbox",function(){t.getContainer().find(".js-bulk-action-checkbox:checked").length>0?e._enableBulkActionsBtn(t):e._disableBulkActionsBtn(t)})}},{key:"_enableBulkActionsBtn",value:function(t){t.getContainer().find(".js-bulk-actions-btn").prop("disabled",!1)}},{key:"_disableBulkActionsBtn",value:function(t){t.getContainer().find(".js-bulk-actions-btn").prop("disabled",!0)}}]),t}();e.default=l},32:function(t,e,n){"use strict";function o(t){return t&&t.__esModule?t:{default:t}}Object.defineProperty(e,"__esModule",{value:!0});var i=n(0),r=o(i),u=n(1),a=o(u),c=n(45),l=o(c),d=window.$,s=function(){function t(){var e=this;return(0,r.default)(this,t),{extend:function(t){return e.extend(t)}}}return(0,a.default)(t,[{key:"extend",value:function(t){var e=this;t.getContainer().on("click",".js-bulk-action-submit-btn",function(n){e.submit(n,t)})}},{key:"submit",value:function(t,e){var n=d(t.currentTarget),o=n.data("confirm-message"),i=n.data("confirmTitle");void 0!==o&&0<o.length?void 0!==i?this.showConfirmModal(n,e,o,i):confirm(o)&&this.postForm(n,e):this.postForm(n,e)}},{key:"showConfirmModal",value:function(t,e,n,o){var i=this,r=t.data("confirmButtonLabel"),u=t.data("closeButtonLabel"),a=t.data("confirmButtonClass");new l.default({id:e.getId()+"_grid_confirm_modal",confirmTitle:o,confirmMessage:n,confirmButtonLabel:r,closeButtonLabel:u,confirmButtonClass:a},function(){return i.postForm(t,e)}).show()}},{key:"postForm",value:function(t,e){var n=d("#"+e.getId()+"_filter_form");n.attr("action",t.data("form-url")),n.attr("method",t.data("form-method")),n.submit()}}]),t}();e.default=s},33:function(t,e,n){"use strict";function o(t){return t&&t.__esModule?t:{default:t}}Object.defineProperty(e,"__esModule",{value:!0});var i=n(0),r=o(i),u=n(1),a=o(u),c=window.$,l=function(){function t(){(0,r.default)(this,t)}return(0,a.default)(t,[{key:"extend",value:function(t){this.initRowLinks(t),this.initConfirmableActions(t)}},{key:"initConfirmableActions",value:function(t){t.getContainer().on("click",".js-link-row-action",function(t){var e=c(t.currentTarget).data("confirm-message");e.length&&!confirm(e)&&t.preventDefault()})}},{key:"initRowLinks",value:function(t){c("tr",t.getContainer()).each(function(){var t=c(this);c(".js-link-row-action[data-clickable-row=1]:first",t).each(function(){var e=c(this),n=e.closest("td");c("td.clickable",t).not(n).addClass("cursor-pointer").click(function(){var t=e.data("confirm-message");t.length&&!confirm(t)||(document.location=e.attr("href"))})})})}}]),t}();e.default=l},35:function(t,e,n){"use strict";function o(t){return t&&t.__esModule?t:{default:t}}Object.defineProperty(e,"__esModule",{value:!0});var i=n(0),r=o(i),u=n(1),a=o(u),c=window.$,l=function(){function t(){(0,r.default)(this,t)}return(0,a.default)(t,[{key:"extend",value:function(t){t.getContainer().on("click",".js-submit-row-action",function(t){t.preventDefault();var e=c(t.currentTarget),n=e.data("confirm-message");if(!n.length||confirm(n)){var o=e.data("method"),i=["GET","POST"].includes(o),r=c("<form>",{action:e.data("url"),method:i?o:"POST"}).appendTo("body");i||r.append(c("<input>",{type:"_hidden",name:"_method",value:o})),r.submit()}})}}]),t}();e.default=l},4:function(t,e){t.exports=function(t){return"object"==typeof t?null!==t:"function"==typeof t}},45:function(t,e,n){"use strict";function o(t,e){var n=this,o=t.id,u=t.closable;this.modal=i(t),this.$modal=r(this.modal.container),this.show=function(){n.$modal.modal()},this.modal.confirmButton.addEventListener("click",e),this.$modal.modal({backdrop:!!u||"static",keyboard:void 0===u||u,closable:void 0===u||u,show:!1}),this.$modal.on("hidden.bs.modal",function(){document.querySelector("#"+o).remove()}),document.body.appendChild(this.modal.container)}function i(t){var e=t.id,n=void 0===e?"confirm_modal":e,o=t.confirmTitle,i=t.confirmMessage,r=void 0===i?"":i,u=t.closeButtonLabel,a=void 0===u?"Close":u,c=t.confirmButtonLabel,l=void 0===c?"Accept":c,d=t.confirmButtonClass,s=void 0===d?"btn-primary":d,f={};return f.container=document.createElement("div"),f.container.classList.add("modal","fade"),f.container.id=n,f.dialog=document.createElement("div"),f.dialog.classList.add("modal-dialog"),f.content=document.createElement("div"),f.content.classList.add("modal-content"),f.header=document.createElement("div"),f.header.classList.add("modal-header"),o&&(f.title=document.createElement("h4"),f.title.classList.add("modal-title"),f.title.innerHTML=o),f.closeIcon=document.createElement("button"),f.closeIcon.classList.add("close"),f.closeIcon.setAttribute("type","button"),f.closeIcon.dataset.dismiss="modal",f.closeIcon.innerHTML="×",f.body=document.createElement("div"),f.body.classList.add("modal-body","text-left","font-weight-normal"),f.message=document.createElement("p"),f.message.classList.add("confirm-message"),f.message.innerHTML=r,f.footer=document.createElement("div"),f.footer.classList.add("modal-footer"),f.closeButton=document.createElement("button"),f.closeButton.setAttribute("type","button"),f.closeButton.classList.add("btn","btn-outline-secondary","btn-lg"),f.closeButton.dataset.dismiss="modal",f.closeButton.innerHTML=a,f.confirmButton=document.createElement("button"),f.confirmButton.setAttribute("type","button"),f.confirmButton.classList.add("btn",s,"btn-lg","btn-confirm-submit"),f.confirmButton.dataset.dismiss="modal",f.confirmButton.innerHTML=l,o?f.header.append(f.title,f.closeIcon):f.header.appendChild(f.closeIcon),f.body.appendChild(f.message),f.footer.append(f.closeButton,f.confirmButton),f.content.append(f.header,f.body,f.footer),f.dialog.appendChild(f.content),f.container.appendChild(f.dialog),f}Object.defineProperty(e,"__esModule",{value:!0}),e.default=o;/**
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
var r=window.$},473:function(t,e,n){"use strict";function o(t){return t&&t.__esModule?t:{default:t}}var i=n(23),r=o(i),u=n(31),a=o(u),c=n(32),l=o(c),d=n(33),s=o(d),f=n(35),m=o(f),p=n(30),h=o(p);(0,window.$)(function(){var t=new r.default("backup");t.addExtension(new a.default),t.addExtension(new l.default),t.addExtension(new s.default),t.addExtension(new m.default),t.addExtension(new h.default)})},5:function(t,e){var n=t.exports="undefined"!=typeof window&&window.Math==Math?window:"undefined"!=typeof self&&self.Math==Math?self:Function("return this")();"number"==typeof __g&&(__g=n)},6:function(t,e,n){var o=n(11),i=n(17),r=n(13),u=Object.defineProperty;e.f=n(2)?Object.defineProperty:function(t,e,n){if(o(t),e=r(e,!0),o(n),i)try{return u(t,e,n)}catch(t){}if("get"in n||"set"in n)throw TypeError("Accessors not supported!");return"value"in n&&(t[e]=n.value),t}},7:function(t,e){t.exports=function(t){try{return!!t()}catch(t){return!0}}},8:function(t,e,n){var o=n(5),i=n(3),r=n(15),u=n(10),a=function(t,e,n){var c,l,d,s=t&a.F,f=t&a.G,m=t&a.S,p=t&a.P,h=t&a.B,b=t&a.W,v=f?i:i[e]||(i[e]={}),y=v.prototype,g=f?o:m?o[e]:(o[e]||{}).prototype;f&&(n=e);for(c in n)(l=!s&&g&&void 0!==g[c])&&c in v||(d=l?g[c]:n[c],v[c]=f&&"function"!=typeof g[c]?n[c]:h&&l?r(d,o):b&&g[c]==d?function(t){var e=function(e,n,o){if(this instanceof t){switch(arguments.length){case 0:return new t;case 1:return new t(e);case 2:return new t(e,n)}return new t(e,n,o)}return t.apply(this,arguments)};return e.prototype=t.prototype,e}(d):p&&"function"==typeof d?r(Function.call,d):d,p&&((v.virtual||(v.virtual={}))[c]=d,t&a.R&&y&&!y[c]&&u(y,c,d)))};a.F=1,a.G=2,a.S=4,a.P=8,a.B=16,a.W=32,a.U=64,a.R=128,t.exports=a}});