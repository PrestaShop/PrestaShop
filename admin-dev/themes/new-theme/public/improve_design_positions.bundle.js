window.improve_design_positions=function(t){function n(o){if(e[o])return e[o].exports;var r=e[o]={i:o,l:!1,exports:{}};return t[o].call(r.exports,r,r.exports,n),r.l=!0,r.exports}var e={};return n.m=t,n.c=e,n.i=function(t){return t},n.d=function(t,e,o){n.o(t,e)||Object.defineProperty(t,e,{configurable:!1,enumerable:!0,get:o})},n.n=function(t){var e=t&&t.__esModule?function(){return t.default}:function(){return t};return n.d(e,"a",e),e},n.o=function(t,n){return Object.prototype.hasOwnProperty.call(t,n)},n.p="",n(n.s=494)}({0:function(t,n,e){"use strict";n.__esModule=!0,n.default=function(t,n){if(!(t instanceof n))throw new TypeError("Cannot call a class as a function")}},1:function(t,n,e){"use strict";n.__esModule=!0;var o=e(19),r=function(t){return t&&t.__esModule?t:{default:t}}(o);n.default=function(){function t(t,n){for(var e=0;e<n.length;e++){var o=n[e];o.enumerable=o.enumerable||!1,o.configurable=!0,"value"in o&&(o.writable=!0),(0,r.default)(t,o.key,o)}}return function(n,e,o){return e&&t(n.prototype,e),o&&t(n,o),n}}()},10:function(t,n,e){var o=e(6),r=e(12);t.exports=e(2)?function(t,n,e){return o.f(t,n,r(1,e))}:function(t,n,e){return t[n]=e,t}},100:function(t,n,e){var o=e(6),r=e(11),i=e(33);t.exports=e(2)?Object.defineProperties:function(t,n){r(t);for(var e,u=i(n),c=u.length,s=0;c>s;)o.f(t,e=u[s++],n[e]);return t}},101:function(t,n,e){var o=e(36),r=e(35);t.exports=function(t){return function(n,e){var i,u,c=String(r(n)),s=o(e),a=c.length;return s<0||s>=a?t?"":void 0:(i=c.charCodeAt(s),i<55296||i>56319||s+1===a||(u=c.charCodeAt(s+1))<56320||u>57343?t?c.charAt(s):i:t?c.slice(s,s+2):u-56320+(i-55296<<10)+65536)}}},102:function(t,n,e){var o=e(92),r=e(29)("iterator"),i=e(55);t.exports=e(3).getIteratorMethod=function(t){if(void 0!=t)return t[r]||t["@@iterator"]||i[o(t)]}},103:function(t,n,e){"use strict";var o=e(97),r=e(99),i=e(55),u=e(22);t.exports=e(75)(Array,"Array",function(t,n){this._t=u(t),this._i=0,this._k=n},function(){var t=this._t,n=this._k,e=this._i++;return!t||e>=t.length?(this._t=void 0,r(1)):"keys"==n?r(0,e):"values"==n?r(0,t[e]):r(0,[e,t[e]])},"values"),i.Arguments=i.Array,o("keys"),o("values"),o("entries")},11:function(t,n,e){var o=e(4);t.exports=function(t){if(!o(t))throw TypeError(t+" is not an object!");return t}},12:function(t,n){t.exports=function(t,n){return{enumerable:!(1&t),configurable:!(2&t),writable:!(4&t),value:n}}},13:function(t,n,e){var o=e(4);t.exports=function(t,n){if(!o(t))return t;var e,r;if(n&&"function"==typeof(e=t.toString)&&!o(r=e.call(t)))return r;if("function"==typeof(e=t.valueOf)&&!o(r=e.call(t)))return r;if(!n&&"function"==typeof(e=t.toString)&&!o(r=e.call(t)))return r;throw TypeError("Can't convert object to primitive value")}},145:function(t,n,e){t.exports={default:e(149),__esModule:!0}},146:function(t,n,e){t.exports={default:e(150),__esModule:!0}},147:function(t,n,e){"use strict";function o(t){return t&&t.__esModule?t:{default:t}}n.__esModule=!0;var r=e(146),i=o(r),u=e(145),c=o(u);n.default=function(){function t(t,n){var e=[],o=!0,r=!1,i=void 0;try{for(var u,s=(0,c.default)(t);!(o=(u=s.next()).done)&&(e.push(u.value),!n||e.length!==n);o=!0);}catch(t){r=!0,i=t}finally{try{!o&&s.return&&s.return()}finally{if(r)throw i}}return e}return function(n,e){if(Array.isArray(n))return n;if((0,i.default)(Object(n)))return t(n,e);throw new TypeError("Invalid attempt to destructure non-iterable instance")}}()},149:function(t,n,e){e(73),e(65),t.exports=e(152)},15:function(t,n,e){var o=e(18);t.exports=function(t,n,e){if(o(t),void 0===n)return t;switch(e){case 1:return function(e){return t.call(n,e)};case 2:return function(e,o){return t.call(n,e,o)};case 3:return function(e,o,r){return t.call(n,e,o,r)}}return function(){return t.apply(n,arguments)}}},150:function(t,n,e){e(73),e(65),t.exports=e(153)},152:function(t,n,e){var o=e(11),r=e(102);t.exports=e(3).getIterator=function(t){var n=r(t);if("function"!=typeof n)throw TypeError(t+" is not iterable!");return o(n.call(t))}},153:function(t,n,e){var o=e(92),r=e(29)("iterator"),i=e(55);t.exports=e(3).isIterable=function(t){var n=Object(t);return void 0!==n[r]||"@@iterator"in n||i.hasOwnProperty(o(n))}},16:function(t,n,e){var o=e(4),r=e(5).document,i=o(r)&&o(r.createElement);t.exports=function(t){return i?r.createElement(t):{}}},17:function(t,n,e){t.exports=!e(2)&&!e(7)(function(){return 7!=Object.defineProperty(e(16)("div"),"a",{get:function(){return 7}}).a})},18:function(t,n){t.exports=function(t){if("function"!=typeof t)throw TypeError(t+" is not a function!");return t}},19:function(t,n,e){t.exports={default:e(20),__esModule:!0}},2:function(t,n,e){t.exports=!e(7)(function(){return 7!=Object.defineProperty({},"a",{get:function(){return 7}}).a})},20:function(t,n,e){e(21);var o=e(3).Object;t.exports=function(t,n,e){return o.defineProperty(t,n,e)}},21:function(t,n,e){var o=e(8);o(o.S+o.F*!e(2),"Object",{defineProperty:e(6).f})},22:function(t,n,e){var o=e(51),r=e(35);t.exports=function(t){return o(r(t))}},27:function(t,n){var e={}.hasOwnProperty;t.exports=function(t,n){return e.call(t,n)}},29:function(t,n,e){var o=e(50)("wks"),r=e(42),i=e(5).Symbol,u="function"==typeof i;(t.exports=function(t){return o[t]||(o[t]=u&&i[t]||(u?i:r)("Symbol."+t))}).store=o},3:function(t,n){var e=t.exports={version:"2.4.0"};"number"==typeof __e&&(__e=e)},33:function(t,n,e){var o=e(53),r=e(49);t.exports=Object.keys||function(t){return o(t,r)}},35:function(t,n){t.exports=function(t){if(void 0==t)throw TypeError("Can't call method on  "+t);return t}},36:function(t,n){var e=Math.ceil,o=Math.floor;t.exports=function(t){return isNaN(t=+t)?0:(t>0?o:e)(t)}},4:function(t,n){t.exports=function(t){return"object"==typeof t?null!==t:"function"==typeof t}},406:function(t,n,e){"use strict";function o(t){return t&&t.__esModule?t:{default:t}}Object.defineProperty(n,"__esModule",{value:!0});var r=e(147),i=o(r),u=e(0),c=o(u),s=e(1),a=o(s),l=window.$,f=function(){function t(){if((0,c.default)(this,t),0!==l("#position-filters").length){var n=this;n.$panelSelection=l("#modules-position-selection-panel"),n.$panelSelectionSingleSelection=l("#modules-position-single-selection"),n.$panelSelectionMultipleSelection=l("#modules-position-multiple-selection"),n.$panelSelectionOriginalY=n.$panelSelection.offset().top,n.$showModules=l("#show-modules"),n.$modulesList=l(".modules-position-checkbox"),n.$hookPosition=l("#hook-position"),n.$hookSearch=l("#hook-search"),n.$modulePositionsForm=l("#module-positions-form"),n.$moduleUnhookButton=l("#unhook-button-position-bottom"),n.$moduleButtonsUpdate=l(".module-buttons-update .btn"),n.handleList(),n.handleSortable(),l('input[name="form[general][enable_tos]"]').on("change",function(){return n.handle()})}}return(0,a.default)(t,[{key:"handleList",value:function(){var t=this;l(window).on("scroll",function(){var n=l(window).scrollTop();t.$panelSelection.css("top",n<20?0:n-t.$panelSelectionOriginalY)}),t.$modulesList.on("change",function(){var n=t.$modulesList.filter(":checked").length;0===n?(t.$moduleUnhookButton.hide(),t.$panelSelection.hide(),t.$panelSelectionSingleSelection.hide(),t.$panelSelectionMultipleSelection.hide()):1===n?(t.$moduleUnhookButton.show(),t.$panelSelection.show(),t.$panelSelectionSingleSelection.show(),t.$panelSelectionMultipleSelection.hide()):(t.$moduleUnhookButton.show(),t.$panelSelection.show(),t.$panelSelectionSingleSelection.hide(),t.$panelSelectionMultipleSelection.show(),l("#modules-position-selection-count").html(n))}),t.$panelSelection.find("button").click(function(){l('button[name="unhookform"]').trigger("click")}),t.$hooksList=[],l("section.hook-panel .hook-name").each(function(){var n=l(this);t.$hooksList.push({title:n.html(),element:n,container:n.parents(".hook-panel")})}),t.$showModules.select2(),t.$showModules.on("change",function(){t.modulesPositionFilterHooks()}),t.$hookPosition.on("change",function(){t.modulesPositionFilterHooks()}),t.$hookSearch.on("input",function(){t.modulesPositionFilterHooks()}),t.$hookSearch.on("keypress",function(t){return 13!==(t.keyCode||t.which)}),l(".hook-checker").on("click",function(){l(".hook"+l(this).data("hook-id")).prop("checked",l(this).prop("checked"))}),t.$modulesList.on("click",function(){l("#Ghook"+l(this).data("hook-id")).prop("checked",0===l(".hook"+l(this).data("hook-id")+":not(:checked)").length)}),t.$moduleButtonsUpdate.on("click",function(){var n=l(this),e=n.closest(".module-item"),o=void 0;return o=n.data("way")?e.next(".module-item"):e.prev(".module-item"),0!==o.length&&(n.data("way")?e.insertAfter(o):e.insertBefore(o),t.updatePositions({hookId:n.data("hook-id"),moduleId:n.data("module-id"),way:n.data("way"),positions:[]},n.closest("ul")),!1)})}},{key:"handleSortable",value:function(){var t=this;l(".sortable").sortable({forcePlaceholderSize:!0,start:function(t,n){l(this).data("previous-index",n.item.index())},update:function(n,e){var o=e.item.attr("id").split("_"),r=(0,i.default)(o,2),u=r[0],c=r[1],s={hookId:u,moduleId:c,way:l(this).data("previous-index")<e.item.index()?1:0,positions:[]};t.updatePositions(s,l(n.target))}})}},{key:"updatePositions",value:function(t,n){var e=this;l.each(n.children(),function(n,e){t.positions.push(l(e).attr("id"))}),l.ajax({type:"POST",headers:{"cache-control":"no-cache"},url:e.$modulePositionsForm.data("update-url"),data:t,success:function(){var t=0;l.each(n.children(),function(n,e){console.log(l(e).find(".index-position")),l(e).find(".index-position").html(++t)}),window.showSuccessMessage(window.update_success_msg)}})}},{key:"modulesPositionFilterHooks",value:function(){for(var t=this,n=t.$hookSearch.val(),e=t.$showModules.val(),o=new RegExp("("+n+")","gi"),r=0;r<t.$hooksList.length;r++)t.$hooksList[r].container.toggle(""===n&&"all"===e),t.$hooksList[r].element.html(t.$hooksList[r].title),t.$hooksList[r].container.find(".module-item").removeClass("highlight");if(""!==n||"all"!==e){for(var i=l(),u=l(),c=void 0,s=0;s<t.$hooksList.length;s++)"all"!==e&&(c=t.$hooksList[s].container.find(".module-position-"+e),c.length>0&&(i=i.add(t.$hooksList[s].container),c.addClass("highlight"))),""!==n&&-1!==t.$hooksList[s].title.toLowerCase().search(n.toLowerCase())&&(u=u.add(t.$hooksList[s].container),t.$hooksList[s].element.html(t.$hooksList[s].title.replace(o,'<span class="highlight">$1</span>')));"all"===e&&""!==n?u.show():""===n&&"all"!==e?i.show():u.filter(i).show()}if(!t.$hookPosition.prop("checked"))for(var a=0;a<t.$hooksList.length;a++)t.$hooksList[a].container.is(".hook-position")&&t.$hooksList[a].container.hide()}}]),t}();n.default=f},42:function(t,n){var e=0,o=Math.random();t.exports=function(t){return"Symbol(".concat(void 0===t?"":t,")_",(++e+o).toString(36))}},45:function(t,n,e){var o=e(35);t.exports=function(t){return Object(o(t))}},47:function(t,n,e){var o=e(50)("keys"),r=e(42);t.exports=function(t){return o[t]||(o[t]=r(t))}},48:function(t,n){var e={}.toString;t.exports=function(t){return e.call(t).slice(8,-1)}},49:function(t,n){t.exports="constructor,hasOwnProperty,isPrototypeOf,propertyIsEnumerable,toLocaleString,toString,valueOf".split(",")},494:function(t,n,e){"use strict";var o=e(406),r=function(t){return t&&t.__esModule?t:{default:t}}(o);/**
                   * 2007-2020 PrestaShop SA and Contributors
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
                   * @copyright 2007-2020 PrestaShop SA and Contributors
                   * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
                   * International Registered Trademark & Property of PrestaShop SA
                   */
(0,window.$)(function(){new r.default})},5:function(t,n){var e=t.exports="undefined"!=typeof window&&window.Math==Math?window:"undefined"!=typeof self&&self.Math==Math?self:Function("return this")();"number"==typeof __g&&(__g=e)},50:function(t,n,e){var o=e(5),r=o["__core-js_shared__"]||(o["__core-js_shared__"]={});t.exports=function(t){return r[t]||(r[t]={})}},51:function(t,n,e){var o=e(48);t.exports=Object("z").propertyIsEnumerable(0)?Object:function(t){return"String"==o(t)?t.split(""):Object(t)}},53:function(t,n,e){var o=e(27),r=e(22),i=e(58)(!1),u=e(47)("IE_PROTO");t.exports=function(t,n){var e,c=r(t),s=0,a=[];for(e in c)e!=u&&o(c,e)&&a.push(e);for(;n.length>s;)o(c,e=n[s++])&&(~i(a,e)||a.push(e));return a}},55:function(t,n){t.exports={}},56:function(t,n,e){var o=e(36),r=Math.min;t.exports=function(t){return t>0?r(o(t),9007199254740991):0}},58:function(t,n,e){var o=e(22),r=e(56),i=e(59);t.exports=function(t){return function(n,e,u){var c,s=o(n),a=r(s.length),l=i(u,a);if(t&&e!=e){for(;a>l;)if((c=s[l++])!=c)return!0}else for(;a>l;l++)if((t||l in s)&&s[l]===e)return t||l||0;return!t&&-1}}},59:function(t,n,e){var o=e(36),r=Math.max,i=Math.min;t.exports=function(t,n){return t=o(t),t<0?r(t+n,0):i(t,n)}},6:function(t,n,e){var o=e(11),r=e(17),i=e(13),u=Object.defineProperty;n.f=e(2)?Object.defineProperty:function(t,n,e){if(o(t),n=i(n,!0),o(e),r)try{return u(t,n,e)}catch(t){}if("get"in e||"set"in e)throw TypeError("Accessors not supported!");return"value"in e&&(t[n]=e.value),t}},62:function(t,n,e){var o=e(6).f,r=e(27),i=e(29)("toStringTag");t.exports=function(t,n,e){t&&!r(t=e?t:t.prototype,i)&&o(t,i,{configurable:!0,value:n})}},63:function(t,n){t.exports=!0},65:function(t,n,e){"use strict";var o=e(101)(!0);e(75)(String,"String",function(t){this._t=String(t),this._i=0},function(){var t,n=this._t,e=this._i;return e>=n.length?{value:void 0,done:!0}:(t=o(n,e),this._i+=t.length,{value:t,done:!1})})},7:function(t,n){t.exports=function(t){try{return!!t()}catch(t){return!0}}},70:function(t,n,e){var o=e(11),r=e(100),i=e(49),u=e(47)("IE_PROTO"),c=function(){},s=function(){var t,n=e(16)("iframe"),o=i.length;for(n.style.display="none",e(93).appendChild(n),n.src="javascript:",t=n.contentWindow.document,t.open(),t.write("<script>document.F=Object<\/script>"),t.close(),s=t.F;o--;)delete s.prototype[i[o]];return s()};t.exports=Object.create||function(t,n){var e;return null!==t?(c.prototype=o(t),e=new c,c.prototype=null,e[u]=t):e=s(),void 0===n?e:r(e,n)}},73:function(t,n,e){e(103);for(var o=e(5),r=e(10),i=e(55),u=e(29)("toStringTag"),c=["NodeList","DOMTokenList","MediaList","StyleSheetList","CSSRuleList"],s=0;s<5;s++){var a=c[s],l=o[a],f=l&&l.prototype;f&&!f[u]&&r(f,u,a),i[a]=i.Array}},75:function(t,n,e){"use strict";var o=e(63),r=e(8),i=e(82),u=e(10),c=e(27),s=e(55),a=e(98),l=e(62),f=e(88),p=e(29)("iterator"),h=!([].keys&&"next"in[].keys()),d=function(){return this};t.exports=function(t,n,e,v,y,m,k){a(e,n,v);var g,w,x,_=function(t){if(!h&&t in O)return O[t];switch(t){case"keys":case"values":return function(){return new e(this,t)}}return function(){return new e(this,t)}},S=n+" Iterator",b="values"==y,$=!1,O=t.prototype,P=O[p]||O["@@iterator"]||y&&O[y],M=P||_(y),j=y?b?_("entries"):M:void 0,L="Array"==n?O.entries||P:P;if(L&&(x=f(L.call(new t)))!==Object.prototype&&(l(x,S,!0),o||c(x,p)||u(x,p,d)),b&&P&&"values"!==P.name&&($=!0,M=function(){return P.call(this)}),o&&!k||!h&&!$&&O[p]||u(O,p,M),s[n]=M,s[S]=d,y)if(g={values:b?M:_("values"),keys:m?M:_("keys"),entries:j},k)for(w in g)w in O||i(O,w,g[w]);else r(r.P+r.F*(h||$),n,g);return g}},8:function(t,n,e){var o=e(5),r=e(3),i=e(15),u=e(10),c=function(t,n,e){var s,a,l,f=t&c.F,p=t&c.G,h=t&c.S,d=t&c.P,v=t&c.B,y=t&c.W,m=p?r:r[n]||(r[n]={}),k=m.prototype,g=p?o:h?o[n]:(o[n]||{}).prototype;p&&(e=n);for(s in e)(a=!f&&g&&void 0!==g[s])&&s in m||(l=a?g[s]:e[s],m[s]=p&&"function"!=typeof g[s]?e[s]:v&&a?i(l,o):y&&g[s]==l?function(t){var n=function(n,e,o){if(this instanceof t){switch(arguments.length){case 0:return new t;case 1:return new t(n);case 2:return new t(n,e)}return new t(n,e,o)}return t.apply(this,arguments)};return n.prototype=t.prototype,n}(l):d&&"function"==typeof l?i(Function.call,l):l,d&&((m.virtual||(m.virtual={}))[s]=l,t&c.R&&k&&!k[s]&&u(k,s,l)))};c.F=1,c.G=2,c.S=4,c.P=8,c.B=16,c.W=32,c.U=64,c.R=128,t.exports=c},82:function(t,n,e){t.exports=e(10)},88:function(t,n,e){var o=e(27),r=e(45),i=e(47)("IE_PROTO"),u=Object.prototype;t.exports=Object.getPrototypeOf||function(t){return t=r(t),o(t,i)?t[i]:"function"==typeof t.constructor&&t instanceof t.constructor?t.constructor.prototype:t instanceof Object?u:null}},92:function(t,n,e){var o=e(48),r=e(29)("toStringTag"),i="Arguments"==o(function(){return arguments}()),u=function(t,n){try{return t[n]}catch(t){}};t.exports=function(t){var n,e,c;return void 0===t?"Undefined":null===t?"Null":"string"==typeof(e=u(n=Object(t),r))?e:i?o(n):"Object"==(c=o(n))&&"function"==typeof n.callee?"Arguments":c}},93:function(t,n,e){t.exports=e(5).document&&document.documentElement},97:function(t,n){t.exports=function(){}},98:function(t,n,e){"use strict";var o=e(70),r=e(12),i=e(62),u={};e(10)(u,e(29)("iterator"),function(){return this}),t.exports=function(t,n,e){t.prototype=o(u,{next:r(1,e)}),i(t,n+" Iterator")}},99:function(t,n){t.exports=function(t,n){return{value:n,done:!!t}}}});