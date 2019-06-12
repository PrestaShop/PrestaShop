<<<<<<< HEAD
<<<<<<< HEAD
window.invoices=function(e){function t(r){if(n[r])return n[r].exports;var o=n[r]={i:r,l:!1,exports:{}};return e[r].call(o.exports,o,o.exports,t),o.l=!0,o.exports}var n={};return t.m=e,t.c=n,t.i=function(e){return e},t.d=function(e,n,r){t.o(e,n)||Object.defineProperty(e,n,{configurable:!1,enumerable:!0,get:r})},t.n=function(e){var n=e&&e.__esModule?function(){return e.default}:function(){return e};return t.d(n,"a",n),n},t.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},t.p="",t(t.s=327)}({1:function(e,t){var n;n=function(){return this}();try{n=n||Function("return this")()||(0,eval)("this")}catch(e){"object"==typeof window&&(n=window)}e.exports=n},15:function(e,t,n){"use strict";function r(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(t,"__esModule",{value:!0});var o=function(){function e(e,t){for(var n=0;n<t.length;n++){var r=t[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}return function(t,n,r){return n&&e(t.prototype,n),r&&e(t,r),t}}(),i=n(18),s=window.$,a=function(){function e(t){r(this,e),t=t||{},this.localeItemSelector=t.localeItemSelector||".js-locale-item",this.localeButtonSelector=t.localeButtonSelector||".js-locale-btn",this.localeInputSelector=t.localeInputSelector||".js-locale-input",s("body").on("click",this.localeItemSelector,this.toggleLanguage.bind(this)),i.EventEmitter.on("languageSelected",this.toggleInputs.bind(this))}return o(e,[{key:"toggleLanguage",value:function(e){var t=s(e.target),n=t.closest("form");i.EventEmitter.emit("languageSelected",{selectedLocale:t.data("locale"),form:n})}},{key:"toggleInputs",value:function(e){var t=e.form,n=e.selectedLocale,r=t.find(this.localeButtonSelector),o=r.data("change-language-url");r.text(n),t.find(this.localeInputSelector).addClass("d-none"),t.find(this.localeInputSelector+".js-locale-"+n).removeClass("d-none"),o&&this._saveSelectedLanguage(o,n)}},{key:"_saveSelectedLanguage",value:function(e,t){s.post({url:e,data:{language_iso_code:t}})}}]),e}();t.default=a},18:function(e,t,n){"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.EventEmitter=void 0;var r=n(20),o=function(e){return e&&e.__esModule?e:{default:e}}(r);t.EventEmitter=new o.default},20:function(e,t,n){"use strict";function r(e){console&&console.warn&&console.warn(e)}function o(){o.init.call(this)}function i(e){return void 0===e._maxListeners?o.defaultMaxListeners:e._maxListeners}function s(e,t,n,o){var s,a,u;if("function"!=typeof n)throw new TypeError('The "listener" argument must be of type Function. Received type '+typeof n);if(a=e._events,void 0===a?(a=e._events=Object.create(null),e._eventsCount=0):(void 0!==a.newListener&&(e.emit("newListener",t,n.listener?n.listener:n),a=e._events),u=a[t]),void 0===u)u=a[t]=n,++e._eventsCount;else if("function"==typeof u?u=a[t]=o?[n,u]:[u,n]:o?u.unshift(n):u.push(n),(s=i(e))>0&&u.length>s&&!u.warned){u.warned=!0;var c=new Error("Possible EventEmitter memory leak detected. "+u.length+" "+String(t)+" listeners added. Use emitter.setMaxListeners() to increase limit");c.name="MaxListenersExceededWarning",c.emitter=e,c.type=t,c.count=u.length,r(c)}return e}function a(){for(var e=[],t=0;t<arguments.length;t++)e.push(arguments[t]);this.fired||(this.target.removeListener(this.type,this.wrapFn),this.fired=!0,m(this.listener,this.target,e))}function u(e,t,n){var r={fired:!1,wrapFn:void 0,target:e,type:t,listener:n},o=a.bind(r);return o.listener=n,r.wrapFn=o,o}function c(e,t,n){var r=e._events;if(void 0===r)return[];var o=r[t];return void 0===o?[]:"function"==typeof o?n?[o.listener||o]:[o]:n?p(o):l(o,o.length)}function f(e){var t=this._events;if(void 0!==t){var n=t[e];if("function"==typeof n)return 1;if(void 0!==n)return n.length}return 0}function l(e,t){for(var n=new Array(t),r=0;r<t;++r)n[r]=e[r];return n}function h(e,t){for(;t+1<e.length;t++)e[t]=e[t+1];e.pop()}function p(e){for(var t=new Array(e.length),n=0;n<t.length;++n)t[n]=e[n].listener||e[n];return t}var d,v="object"==typeof Reflect?Reflect:null,m=v&&"function"==typeof v.apply?v.apply:function(e,t,n){return Function.prototype.apply.call(e,t,n)};d=v&&"function"==typeof v.ownKeys?v.ownKeys:Object.getOwnPropertySymbols?function(e){return Object.getOwnPropertyNames(e).concat(Object.getOwnPropertySymbols(e))}:function(e){return Object.getOwnPropertyNames(e)};var y=Number.isNaN||function(e){return e!==e};e.exports=o,o.EventEmitter=o,o.prototype._events=void 0,o.prototype._eventsCount=0,o.prototype._maxListeners=void 0;var g=10;Object.defineProperty(o,"defaultMaxListeners",{enumerable:!0,get:function(){return g},set:function(e){if("number"!=typeof e||e<0||y(e))throw new RangeError('The value of "defaultMaxListeners" is out of range. It must be a non-negative number. Received '+e+".");g=e}}),o.init=function(){void 0!==this._events&&this._events!==Object.getPrototypeOf(this)._events||(this._events=Object.create(null),this._eventsCount=0),this._maxListeners=this._maxListeners||void 0},o.prototype.setMaxListeners=function(e){if("number"!=typeof e||e<0||y(e))throw new RangeError('The value of "n" is out of range. It must be a non-negative number. Received '+e+".");return this._maxListeners=e,this},o.prototype.getMaxListeners=function(){return i(this)},o.prototype.emit=function(e){for(var t=[],n=1;n<arguments.length;n++)t.push(arguments[n]);var r="error"===e,o=this._events;if(void 0!==o)r=r&&void 0===o.error;else if(!r)return!1;if(r){var i;if(t.length>0&&(i=t[0]),i instanceof Error)throw i;var s=new Error("Unhandled error."+(i?" ("+i.message+")":""));throw s.context=i,s}var a=o[e];if(void 0===a)return!1;if("function"==typeof a)m(a,this,t);else for(var u=a.length,c=l(a,u),n=0;n<u;++n)m(c[n],this,t);return!0},o.prototype.addListener=function(e,t){return s(this,e,t,!1)},o.prototype.on=o.prototype.addListener,o.prototype.prependListener=function(e,t){return s(this,e,t,!0)},o.prototype.once=function(e,t){if("function"!=typeof t)throw new TypeError('The "listener" argument must be of type Function. Received type '+typeof t);return this.on(e,u(this,e,t)),this},o.prototype.prependOnceListener=function(e,t){if("function"!=typeof t)throw new TypeError('The "listener" argument must be of type Function. Received type '+typeof t);return this.prependListener(e,u(this,e,t)),this},o.prototype.removeListener=function(e,t){var n,r,o,i,s;if("function"!=typeof t)throw new TypeError('The "listener" argument must be of type Function. Received type '+typeof t);if(void 0===(r=this._events))return this;if(void 0===(n=r[e]))return this;if(n===t||n.listener===t)0==--this._eventsCount?this._events=Object.create(null):(delete r[e],r.removeListener&&this.emit("removeListener",e,n.listener||t));else if("function"!=typeof n){for(o=-1,i=n.length-1;i>=0;i--)if(n[i]===t||n[i].listener===t){s=n[i].listener,o=i;break}if(o<0)return this;0===o?n.shift():h(n,o),1===n.length&&(r[e]=n[0]),void 0!==r.removeListener&&this.emit("removeListener",e,s||t)}return this},o.prototype.off=o.prototype.removeListener,o.prototype.removeAllListeners=function(e){var t,n,r;if(void 0===(n=this._events))return this;if(void 0===n.removeListener)return 0===arguments.length?(this._events=Object.create(null),this._eventsCount=0):void 0!==n[e]&&(0==--this._eventsCount?this._events=Object.create(null):delete n[e]),this;if(0===arguments.length){var o,i=Object.keys(n);for(r=0;r<i.length;++r)"removeListener"!==(o=i[r])&&this.removeAllListeners(o);return this.removeAllListeners("removeListener"),this._events=Object.create(null),this._eventsCount=0,this}if("function"==typeof(t=n[e]))this.removeListener(e,t);else if(void 0!==t)for(r=t.length-1;r>=0;r--)this.removeListener(e,t[r]);return this},o.prototype.listeners=function(e){return c(this,e,!0)},o.prototype.rawListeners=function(e){return c(this,e,!1)},o.listenerCount=function(e,t){return"function"==typeof e.listenerCount?e.listenerCount(t):f.call(e,t)},o.prototype.listenerCount=f,o.prototype.eventNames=function(){return this._eventsCount>0?d(this._events):[]}},327:function(e,t,n){"use strict";function r(e){return e&&e.__esModule?e:{default:e}}var o=n(54),i=r(o),s=n(15),a=r(s);(0,window.$)(function(){(0,i.default)(),new a.default})},54:function(e,t,n){"use strict";(function(e){Object.defineProperty(t,"__esModule",{value:!0}),n(72);var r=e.$,o=function(){function t(){var e=r("body").find(".bootstrap-datetimepicker-widget:last");if(!(e.length<=0)){var n=e.offset(),o=e.outerHeight(),i=(e.outerHeight(!0)-e.outerHeight())/2;e.appendTo("body");var s=n.top+o-i-e.outerHeight();e.css({position:"absolute",top:s,bottom:"auto",left:n.left,right:"auto"}),r(window).on("resize",t)}}r('.datepicker input[type="text"]').datetimepicker({locale:e.full_language_code,format:"YYYY-MM-DD"}).on("dp.show",t).on("dp.hide",function(){r(window).off("resize",t)})};t.default=o}).call(t,n(1))},72:function(e,t,n){(function(e){!function(e){var t=function(){try{return!!Symbol.iterator}catch(e){return!1}}(),n=function(e){var n={next:function(){var t=e.shift();return{done:void 0===t,value:t}}};return t&&(n[Symbol.iterator]=function(){return n}),n},r=function(e){return encodeURIComponent(e).replace(/%20/g,"+")},o=function(e){return decodeURIComponent(e).replace(/\+/g," ")};"URLSearchParams"in e&&"a=1"===new URLSearchParams("?a=1").toString()||function(){var o=function(e){Object.defineProperty(this,"_entries",{writable:!0,value:{}});var t=typeof e;if("undefined"===t);else if("string"===t)""!==e&&this._fromString(e);else if(e instanceof o){var n=this;e.forEach(function(e,t){n.append(t,e)})}else{if(null===e||"object"!==t)throw new TypeError("Unsupported input's type for URLSearchParams");if("[object Array]"===Object.prototype.toString.call(e))for(var r=0;r<e.length;r++){var i=e[r];if("[object Array]"!==Object.prototype.toString.call(i)&&2===i.length)throw new TypeError("Expected [string, any] as entry at index "+r+" of URLSearchParams's input");this.append(i[0],i[1])}else for(var s in e)e.hasOwnProperty(s)&&this.append(s,e[s])}},i=o.prototype;i.append=function(e,t){e in this._entries?this._entries[e].push(String(t)):this._entries[e]=[String(t)]},i.delete=function(e){delete this._entries[e]},i.get=function(e){return e in this._entries?this._entries[e][0]:null},i.getAll=function(e){return e in this._entries?this._entries[e].slice(0):[]},i.has=function(e){return e in this._entries},i.set=function(e,t){this._entries[e]=[String(t)]},i.forEach=function(e,t){var n;for(var r in this._entries)if(this._entries.hasOwnProperty(r)){n=this._entries[r];for(var o=0;o<n.length;o++)e.call(t,n[o],r,this)}},i.keys=function(){var e=[];return this.forEach(function(t,n){e.push(n)}),n(e)},i.values=function(){var e=[];return this.forEach(function(t){e.push(t)}),n(e)},i.entries=function(){var e=[];return this.forEach(function(t,n){e.push([n,t])}),n(e)},t&&(i[Symbol.iterator]=i.entries),i.toString=function(){var e=[];return this.forEach(function(t,n){e.push(r(n)+"="+r(t))}),e.join("&")},e.URLSearchParams=o}();var i=URLSearchParams.prototype;"function"!=typeof i.sort&&(i.sort=function(){var e=this,t=[];this.forEach(function(n,r){t.push([r,n]),e._entries||e.delete(r)}),t.sort(function(e,t){return e[0]<t[0]?-1:e[0]>t[0]?1:0}),e._entries&&(e._entries={});for(var n=0;n<t.length;n++)this.append(t[n][0],t[n][1])}),"function"!=typeof i._fromString&&Object.defineProperty(i,"_fromString",{enumerable:!1,configurable:!1,writable:!1,value:function(e){if(this._entries)this._entries={};else{var t=[];this.forEach(function(e,n){t.push(n)});for(var n=0;n<t.length;n++)this.delete(t[n])}e=e.replace(/^\?/,"");for(var r,i=e.split("&"),n=0;n<i.length;n++)r=i[n].split("="),this.append(o(r[0]),r.length>1?o(r[1]):"")}})}(void 0!==e?e:"undefined"!=typeof window?window:"undefined"!=typeof self?self:this),function(e){if(function(){try{var e=new URL("b","http://a");return e.pathname="c%20d","http://a/c%20d"===e.href&&e.searchParams}catch(e){return!1}}()||function(){var t=e.URL,n=function(t,n){"string"!=typeof t&&(t=String(t));var r,o=document;if(n&&(void 0===e.location||n!==e.location.href)){o=document.implementation.createHTMLDocument(""),r=o.createElement("base"),r.href=n,o.head.appendChild(r);try{if(0!==r.href.indexOf(n))throw new Error(r.href)}catch(e){throw new Error("URL unable to set base "+n+" due to "+e)}}var i=o.createElement("a");if(i.href=t,r&&(o.body.appendChild(i),i.href=i.href),":"===i.protocol||!/:/.test(i.href))throw new TypeError("Invalid URL");Object.defineProperty(this,"_anchorElement",{value:i});var s=new URLSearchParams(this.search),a=!0,u=!0,c=this;["append","delete","set"].forEach(function(e){var t=s[e];s[e]=function(){t.apply(s,arguments),a&&(u=!1,c.search=s.toString(),u=!0)}}),Object.defineProperty(this,"searchParams",{value:s,enumerable:!0});var f=void 0;Object.defineProperty(this,"_updateSearchParams",{enumerable:!1,configurable:!1,writable:!1,value:function(){this.search!==f&&(f=this.search,u&&(a=!1,this.searchParams._fromString(this.search),a=!0))}})},r=n.prototype,o=function(e){Object.defineProperty(r,e,{get:function(){return this._anchorElement[e]},set:function(t){this._anchorElement[e]=t},enumerable:!0})};["hash","host","hostname","port","protocol"].forEach(function(e){o(e)}),Object.defineProperty(r,"search",{get:function(){return this._anchorElement.search},set:function(e){this._anchorElement.search=e,this._updateSearchParams()},enumerable:!0}),Object.defineProperties(r,{toString:{get:function(){var e=this;return function(){return e.href}}},href:{get:function(){return this._anchorElement.href.replace(/\?$/,"")},set:function(e){this._anchorElement.href=e,this._updateSearchParams()},enumerable:!0},pathname:{get:function(){return this._anchorElement.pathname.replace(/(^\/?)/,"/")},set:function(e){this._anchorElement.pathname=e},enumerable:!0},origin:{get:function(){var e={"http:":80,"https:":443,"ftp:":21}[this._anchorElement.protocol],t=this._anchorElement.port!=e&&""!==this._anchorElement.port;return this._anchorElement.protocol+"//"+this._anchorElement.hostname+(t?":"+this._anchorElement.port:"")},enumerable:!0},password:{get:function(){return""},set:function(e){},enumerable:!0},username:{get:function(){return""},set:function(e){},enumerable:!0}}),n.createObjectURL=function(e){return t.createObjectURL.apply(t,arguments)},n.revokeObjectURL=function(e){return t.revokeObjectURL.apply(t,arguments)},e.URL=n}(),void 0!==e.location&&!("origin"in e.location)){var t=function(){return e.location.protocol+"//"+e.location.hostname+(e.location.port?":"+e.location.port:"")};try{Object.defineProperty(e.location,"origin",{get:t,enumerable:!0})}catch(n){setInterval(function(){e.location.origin=t()},100)}}}(void 0!==e?e:"undefined"!=typeof window?window:"undefined"!=typeof self?self:this)}).call(t,n(1))}});
=======
<<<<<<< HEAD
window.invoices=function(e){function t(r){if(n[r])return n[r].exports;var o=n[r]={i:r,l:!1,exports:{}};return e[r].call(o.exports,o,o.exports,t),o.l=!0,o.exports}var n={};return t.m=e,t.c=n,t.i=function(e){return e},t.d=function(e,n,r){t.o(e,n)||Object.defineProperty(e,n,{configurable:!1,enumerable:!0,get:r})},t.n=function(e){var n=e&&e.__esModule?function(){return e.default}:function(){return e};return t.d(n,"a",n),n},t.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},t.p="",t(t.s=327)}({1:function(e,t){var n;n=function(){return this}();try{n=n||Function("return this")()||(0,eval)("this")}catch(e){"object"==typeof window&&(n=window)}e.exports=n},15:function(e,t,n){"use strict";function r(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(t,"__esModule",{value:!0});var o=function(){function e(e,t){for(var n=0;n<t.length;n++){var r=t[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}return function(t,n,r){return n&&e(t.prototype,n),r&&e(t,r),t}}(),i=n(18),s=window.$,a=function(){function e(t){r(this,e),t=t||{},this.localeItemSelector=t.localeItemSelector||".js-locale-item",this.localeButtonSelector=t.localeButtonSelector||".js-locale-btn",this.localeInputSelector=t.localeInputSelector||".js-locale-input",s("body").on("click",this.localeItemSelector,this.toggleLanguage.bind(this)),i.EventEmitter.on("languageSelected",this.toggleInputs.bind(this))}return o(e,[{key:"toggleLanguage",value:function(e){var t=s(e.target),n=t.closest("form");i.EventEmitter.emit("languageSelected",{selectedLocale:t.data("locale"),form:n})}},{key:"toggleInputs",value:function(e){var t=e.form,n=e.selectedLocale,r=t.find(this.localeButtonSelector),o=r.data("change-language-url");r.text(n),t.find(this.localeInputSelector).addClass("d-none"),t.find(this.localeInputSelector+".js-locale-"+n).removeClass("d-none"),o&&this._saveSelectedLanguage(o,n)}},{key:"_saveSelectedLanguage",value:function(e,t){s.post({url:e,data:{language_iso_code:t}})}}]),e}();t.default=a},18:function(e,t,n){"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.EventEmitter=void 0;var r=n(20),o=function(e){return e&&e.__esModule?e:{default:e}}(r);t.EventEmitter=new o.default},20:function(e,t,n){"use strict";function r(e){console&&console.warn&&console.warn(e)}function o(){o.init.call(this)}function i(e){return void 0===e._maxListeners?o.defaultMaxListeners:e._maxListeners}function s(e,t,n,o){var s,a,u;if("function"!=typeof n)throw new TypeError('The "listener" argument must be of type Function. Received type '+typeof n);if(a=e._events,void 0===a?(a=e._events=Object.create(null),e._eventsCount=0):(void 0!==a.newListener&&(e.emit("newListener",t,n.listener?n.listener:n),a=e._events),u=a[t]),void 0===u)u=a[t]=n,++e._eventsCount;else if("function"==typeof u?u=a[t]=o?[n,u]:[u,n]:o?u.unshift(n):u.push(n),(s=i(e))>0&&u.length>s&&!u.warned){u.warned=!0;var c=new Error("Possible EventEmitter memory leak detected. "+u.length+" "+String(t)+" listeners added. Use emitter.setMaxListeners() to increase limit");c.name="MaxListenersExceededWarning",c.emitter=e,c.type=t,c.count=u.length,r(c)}return e}function a(){for(var e=[],t=0;t<arguments.length;t++)e.push(arguments[t]);this.fired||(this.target.removeListener(this.type,this.wrapFn),this.fired=!0,m(this.listener,this.target,e))}function u(e,t,n){var r={fired:!1,wrapFn:void 0,target:e,type:t,listener:n},o=a.bind(r);return o.listener=n,r.wrapFn=o,o}function c(e,t,n){var r=e._events;if(void 0===r)return[];var o=r[t];return void 0===o?[]:"function"==typeof o?n?[o.listener||o]:[o]:n?p(o):l(o,o.length)}function f(e){var t=this._events;if(void 0!==t){var n=t[e];if("function"==typeof n)return 1;if(void 0!==n)return n.length}return 0}function l(e,t){for(var n=new Array(t),r=0;r<t;++r)n[r]=e[r];return n}function h(e,t){for(;t+1<e.length;t++)e[t]=e[t+1];e.pop()}function p(e){for(var t=new Array(e.length),n=0;n<t.length;++n)t[n]=e[n].listener||e[n];return t}var v,d="object"==typeof Reflect?Reflect:null,m=d&&"function"==typeof d.apply?d.apply:function(e,t,n){return Function.prototype.apply.call(e,t,n)};v=d&&"function"==typeof d.ownKeys?d.ownKeys:Object.getOwnPropertySymbols?function(e){return Object.getOwnPropertyNames(e).concat(Object.getOwnPropertySymbols(e))}:function(e){return Object.getOwnPropertyNames(e)};var y=Number.isNaN||function(e){return e!==e};e.exports=o,o.EventEmitter=o,o.prototype._events=void 0,o.prototype._eventsCount=0,o.prototype._maxListeners=void 0;var g=10;Object.defineProperty(o,"defaultMaxListeners",{enumerable:!0,get:function(){return g},set:function(e){if("number"!=typeof e||e<0||y(e))throw new RangeError('The value of "defaultMaxListeners" is out of range. It must be a non-negative number. Received '+e+".");g=e}}),o.init=function(){void 0!==this._events&&this._events!==Object.getPrototypeOf(this)._events||(this._events=Object.create(null),this._eventsCount=0),this._maxListeners=this._maxListeners||void 0},o.prototype.setMaxListeners=function(e){if("number"!=typeof e||e<0||y(e))throw new RangeError('The value of "n" is out of range. It must be a non-negative number. Received '+e+".");return this._maxListeners=e,this},o.prototype.getMaxListeners=function(){return i(this)},o.prototype.emit=function(e){for(var t=[],n=1;n<arguments.length;n++)t.push(arguments[n]);var r="error"===e,o=this._events;if(void 0!==o)r=r&&void 0===o.error;else if(!r)return!1;if(r){var i;if(t.length>0&&(i=t[0]),i instanceof Error)throw i;var s=new Error("Unhandled error."+(i?" ("+i.message+")":""));throw s.context=i,s}var a=o[e];if(void 0===a)return!1;if("function"==typeof a)m(a,this,t);else for(var u=a.length,c=l(a,u),n=0;n<u;++n)m(c[n],this,t);return!0},o.prototype.addListener=function(e,t){return s(this,e,t,!1)},o.prototype.on=o.prototype.addListener,o.prototype.prependListener=function(e,t){return s(this,e,t,!0)},o.prototype.once=function(e,t){if("function"!=typeof t)throw new TypeError('The "listener" argument must be of type Function. Received type '+typeof t);return this.on(e,u(this,e,t)),this},o.prototype.prependOnceListener=function(e,t){if("function"!=typeof t)throw new TypeError('The "listener" argument must be of type Function. Received type '+typeof t);return this.prependListener(e,u(this,e,t)),this},o.prototype.removeListener=function(e,t){var n,r,o,i,s;if("function"!=typeof t)throw new TypeError('The "listener" argument must be of type Function. Received type '+typeof t);if(void 0===(r=this._events))return this;if(void 0===(n=r[e]))return this;if(n===t||n.listener===t)0==--this._eventsCount?this._events=Object.create(null):(delete r[e],r.removeListener&&this.emit("removeListener",e,n.listener||t));else if("function"!=typeof n){for(o=-1,i=n.length-1;i>=0;i--)if(n[i]===t||n[i].listener===t){s=n[i].listener,o=i;break}if(o<0)return this;0===o?n.shift():h(n,o),1===n.length&&(r[e]=n[0]),void 0!==r.removeListener&&this.emit("removeListener",e,s||t)}return this},o.prototype.off=o.prototype.removeListener,o.prototype.removeAllListeners=function(e){var t,n,r;if(void 0===(n=this._events))return this;if(void 0===n.removeListener)return 0===arguments.length?(this._events=Object.create(null),this._eventsCount=0):void 0!==n[e]&&(0==--this._eventsCount?this._events=Object.create(null):delete n[e]),this;if(0===arguments.length){var o,i=Object.keys(n);for(r=0;r<i.length;++r)"removeListener"!==(o=i[r])&&this.removeAllListeners(o);return this.removeAllListeners("removeListener"),this._events=Object.create(null),this._eventsCount=0,this}if("function"==typeof(t=n[e]))this.removeListener(e,t);else if(void 0!==t)for(r=t.length-1;r>=0;r--)this.removeListener(e,t[r]);return this},o.prototype.listeners=function(e){return c(this,e,!0)},o.prototype.rawListeners=function(e){return c(this,e,!1)},o.listenerCount=function(e,t){return"function"==typeof e.listenerCount?e.listenerCount(t):f.call(e,t)},o.prototype.listenerCount=f,o.prototype.eventNames=function(){return this._eventsCount>0?v(this._events):[]}},327:function(e,t,n){"use strict";function r(e){return e&&e.__esModule?e:{default:e}}var o=n(54),i=r(o),s=n(15),a=r(s);(0,window.$)(function(){(0,i.default)(),new a.default})},54:function(e,t,n){"use strict";(function(e){Object.defineProperty(t,"__esModule",{value:!0}),n(72);var r=e.$,o=function(){r('.datepicker input[type="text"]').datetimepicker({locale:e.full_language_code,format:"YYYY-MM-DD"})};t.default=o}).call(t,n(1))},72:function(e,t,n){(function(e){!function(e){var t=function(){try{return!!Symbol.iterator}catch(e){return!1}}(),n=function(e){var n={next:function(){var t=e.shift();return{done:void 0===t,value:t}}};return t&&(n[Symbol.iterator]=function(){return n}),n},r=function(e){return encodeURIComponent(e).replace(/%20/g,"+")},o=function(e){return decodeURIComponent(e).replace(/\+/g," ")};"URLSearchParams"in e&&"a=1"===new URLSearchParams("?a=1").toString()||function(){var o=function(e){Object.defineProperty(this,"_entries",{writable:!0,value:{}});var t=typeof e;if("undefined"===t);else if("string"===t)""!==e&&this._fromString(e);else if(e instanceof o){var n=this;e.forEach(function(e,t){n.append(t,e)})}else{if(null===e||"object"!==t)throw new TypeError("Unsupported input's type for URLSearchParams");if("[object Array]"===Object.prototype.toString.call(e))for(var r=0;r<e.length;r++){var i=e[r];if("[object Array]"!==Object.prototype.toString.call(i)&&2===i.length)throw new TypeError("Expected [string, any] as entry at index "+r+" of URLSearchParams's input");this.append(i[0],i[1])}else for(var s in e)e.hasOwnProperty(s)&&this.append(s,e[s])}},i=o.prototype;i.append=function(e,t){e in this._entries?this._entries[e].push(String(t)):this._entries[e]=[String(t)]},i.delete=function(e){delete this._entries[e]},i.get=function(e){return e in this._entries?this._entries[e][0]:null},i.getAll=function(e){return e in this._entries?this._entries[e].slice(0):[]},i.has=function(e){return e in this._entries},i.set=function(e,t){this._entries[e]=[String(t)]},i.forEach=function(e,t){var n;for(var r in this._entries)if(this._entries.hasOwnProperty(r)){n=this._entries[r];for(var o=0;o<n.length;o++)e.call(t,n[o],r,this)}},i.keys=function(){var e=[];return this.forEach(function(t,n){e.push(n)}),n(e)},i.values=function(){var e=[];return this.forEach(function(t){e.push(t)}),n(e)},i.entries=function(){var e=[];return this.forEach(function(t,n){e.push([n,t])}),n(e)},t&&(i[Symbol.iterator]=i.entries),i.toString=function(){var e=[];return this.forEach(function(t,n){e.push(r(n)+"="+r(t))}),e.join("&")},e.URLSearchParams=o}();var i=URLSearchParams.prototype;"function"!=typeof i.sort&&(i.sort=function(){var e=this,t=[];this.forEach(function(n,r){t.push([r,n]),e._entries||e.delete(r)}),t.sort(function(e,t){return e[0]<t[0]?-1:e[0]>t[0]?1:0}),e._entries&&(e._entries={});for(var n=0;n<t.length;n++)this.append(t[n][0],t[n][1])}),"function"!=typeof i._fromString&&Object.defineProperty(i,"_fromString",{enumerable:!1,configurable:!1,writable:!1,value:function(e){if(this._entries)this._entries={};else{var t=[];this.forEach(function(e,n){t.push(n)});for(var n=0;n<t.length;n++)this.delete(t[n])}e=e.replace(/^\?/,"");for(var r,i=e.split("&"),n=0;n<i.length;n++)r=i[n].split("="),this.append(o(r[0]),r.length>1?o(r[1]):"")}})}(void 0!==e?e:"undefined"!=typeof window?window:"undefined"!=typeof self?self:this),function(e){if(function(){try{var e=new URL("b","http://a");return e.pathname="c%20d","http://a/c%20d"===e.href&&e.searchParams}catch(e){return!1}}()||function(){var t=e.URL,n=function(t,n){"string"!=typeof t&&(t=String(t));var r,o=document;if(n&&(void 0===e.location||n!==e.location.href)){o=document.implementation.createHTMLDocument(""),r=o.createElement("base"),r.href=n,o.head.appendChild(r);try{if(0!==r.href.indexOf(n))throw new Error(r.href)}catch(e){throw new Error("URL unable to set base "+n+" due to "+e)}}var i=o.createElement("a");if(i.href=t,r&&(o.body.appendChild(i),i.href=i.href),":"===i.protocol||!/:/.test(i.href))throw new TypeError("Invalid URL");Object.defineProperty(this,"_anchorElement",{value:i});var s=new URLSearchParams(this.search),a=!0,u=!0,c=this;["append","delete","set"].forEach(function(e){var t=s[e];s[e]=function(){t.apply(s,arguments),a&&(u=!1,c.search=s.toString(),u=!0)}}),Object.defineProperty(this,"searchParams",{value:s,enumerable:!0});var f=void 0;Object.defineProperty(this,"_updateSearchParams",{enumerable:!1,configurable:!1,writable:!1,value:function(){this.search!==f&&(f=this.search,u&&(a=!1,this.searchParams._fromString(this.search),a=!0))}})},r=n.prototype,o=function(e){Object.defineProperty(r,e,{get:function(){return this._anchorElement[e]},set:function(t){this._anchorElement[e]=t},enumerable:!0})};["hash","host","hostname","port","protocol"].forEach(function(e){o(e)}),Object.defineProperty(r,"search",{get:function(){return this._anchorElement.search},set:function(e){this._anchorElement.search=e,this._updateSearchParams()},enumerable:!0}),Object.defineProperties(r,{toString:{get:function(){var e=this;return function(){return e.href}}},href:{get:function(){return this._anchorElement.href.replace(/\?$/,"")},set:function(e){this._anchorElement.href=e,this._updateSearchParams()},enumerable:!0},pathname:{get:function(){return this._anchorElement.pathname.replace(/(^\/?)/,"/")},set:function(e){this._anchorElement.pathname=e},enumerable:!0},origin:{get:function(){var e={"http:":80,"https:":443,"ftp:":21}[this._anchorElement.protocol],t=this._anchorElement.port!=e&&""!==this._anchorElement.port;return this._anchorElement.protocol+"//"+this._anchorElement.hostname+(t?":"+this._anchorElement.port:"")},enumerable:!0},password:{get:function(){return""},set:function(e){},enumerable:!0},username:{get:function(){return""},set:function(e){},enumerable:!0}}),n.createObjectURL=function(e){return t.createObjectURL.apply(t,arguments)},n.revokeObjectURL=function(e){return t.revokeObjectURL.apply(t,arguments)},e.URL=n}(),void 0!==e.location&&!("origin"in e.location)){var t=function(){return e.location.protocol+"//"+e.location.hostname+(e.location.port?":"+e.location.port:"")};try{Object.defineProperty(e.location,"origin",{get:t,enumerable:!0})}catch(n){setInterval(function(){e.location.origin=t()},100)}}}(void 0!==e?e:"undefined"!=typeof window?window:"undefined"!=typeof self?self:this)}).call(t,n(1))}});
=======
/******/ (function(modules) { // webpackBootstrap
/******/ 	function hotDisposeChunk(chunkId) {
/******/ 		delete installedChunks[chunkId];
/******/ 	}
/******/ 	var parentHotUpdateCallback = this["webpackHotUpdate"];
/******/ 	this["webpackHotUpdate"] = 
/******/ 	function webpackHotUpdateCallback(chunkId, moreModules) { // eslint-disable-line no-unused-vars
/******/ 		hotAddUpdateChunk(chunkId, moreModules);
/******/ 		if(parentHotUpdateCallback) parentHotUpdateCallback(chunkId, moreModules);
/******/ 	} ;
/******/ 	
/******/ 	function hotDownloadUpdateChunk(chunkId) { // eslint-disable-line no-unused-vars
/******/ 		var head = document.getElementsByTagName("head")[0];
/******/ 		var script = document.createElement("script");
/******/ 		script.type = "text/javascript";
/******/ 		script.charset = "utf-8";
/******/ 		script.src = __webpack_require__.p + "" + chunkId + "." + hotCurrentHash + ".hot-update.js";
/******/ 		head.appendChild(script);
/******/ 	}
/******/ 	
/******/ 	function hotDownloadManifest() { // eslint-disable-line no-unused-vars
/******/ 		return new Promise(function(resolve, reject) {
/******/ 			if(typeof XMLHttpRequest === "undefined")
/******/ 				return reject(new Error("No browser support"));
/******/ 			try {
/******/ 				var request = new XMLHttpRequest();
/******/ 				var requestPath = __webpack_require__.p + "" + hotCurrentHash + ".hot-update.json";
/******/ 				request.open("GET", requestPath, true);
/******/ 				request.timeout = 10000;
/******/ 				request.send(null);
/******/ 			} catch(err) {
/******/ 				return reject(err);
/******/ 			}
/******/ 			request.onreadystatechange = function() {
/******/ 				if(request.readyState !== 4) return;
/******/ 				if(request.status === 0) {
/******/ 					// timeout
/******/ 					reject(new Error("Manifest request to " + requestPath + " timed out."));
/******/ 				} else if(request.status === 404) {
/******/ 					// no update available
/******/ 					resolve();
/******/ 				} else if(request.status !== 200 && request.status !== 304) {
/******/ 					// other failure
/******/ 					reject(new Error("Manifest request to " + requestPath + " failed."));
/******/ 				} else {
/******/ 					// success
/******/ 					try {
/******/ 						var update = JSON.parse(request.responseText);
/******/ 					} catch(e) {
/******/ 						reject(e);
/******/ 						return;
/******/ 					}
/******/ 					resolve(update);
/******/ 				}
/******/ 			};
/******/ 		});
/******/ 	}
/******/
<<<<<<< HEAD
var n={};t.m=e,t.c=n,t.i=function(e){return e},t.d=function(e,n,r){t.o(e,n)||Object.defineProperty(e,n,{configurable:!1,enumerable:!0,get:r})},t.n=function(e){var n=e&&e.__esModule?function(){return e.default}:function(){return e};return t.d(n,"a",n),n},t.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},t.p="",t(t.s=447)}({1:function(e,t){var n;n=function(){return this}();try{n=n||Function("return this")()||(0,eval)("this")}catch(e){"object"==typeof window&&(n=window)}e.exports=n},206:function(e,t,n){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var r=n(36),o=n(22);(0,window.$)(function(){n.i(r.a)(),new o.a})},22:function(e,t,n){"use strict";function r(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}var o=function(){function e(e,t){for(var n=0;n<t.length;n++){var r=t[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}return function(t,n,r){return n&&e(t.prototype,n),r&&e(t,r),t}}(),i=window.$,c=function(){function e(t){r(this,e),t=t||{},this.localeItemSelector=t.localeItemSelector||".js-locale-item",this.localeButtonSelector=t.localeButtonSelector||".js-locale-btn",this.localeInputSelector=t.localeInputSelector||".js-locale-input",i("body").on("click",this.localeItemSelector,this.toggleInputs.bind(this))}return o(e,[{key:"toggleInputs",value:function(e){var t=i(e.target),n=t.closest("form"),r=t.data("locale");n.find(this.localeButtonSelector).text(r),n.find(this.localeInputSelector).addClass("d-none"),n.find(this.localeInputSelector+".js-locale-"+r).removeClass("d-none")}}]),e}();t.a=c},36:function(e,t,n){"use strict";(function(e){var r=n(47),o=(n.n(r),e.$),i=function(){o('.datepicker input[type="text"]').datetimepicker({locale:e.full_language_code,format:"YYYY-MM-DD"})};t.a=i}).call(t,n(1))},447:function(e,t,n){e.exports=n(206)},47:function(e,t,n){(function(e){!function(e){var t=function(){try{return!!Symbol.iterator}catch(e){return!1}}(),n=function(e){var n={next:function(){var t=e.shift();return{done:void 0===t,value:t}}};return t&&(n[Symbol.iterator]=function(){return n}),n};"URLSearchParams"in e&&"a=1"===new URLSearchParams("?a=1").toString()||function(){var r=function(e){if(Object.defineProperty(this,"_entries",{value:{}}),"string"==typeof e){if(""!==e){e=e.replace(/^\?/,"");for(var t,n=e.split("&"),o=0;o<n.length;o++)t=n[o].split("="),this.append(decodeURIComponent(t[0]),t.length>1?decodeURIComponent(t[1]):"")}}else if(e instanceof r){var i=this;e.forEach(function(e,t){i.append(e,t)})}},o=r.prototype;o.append=function(e,t){e in this._entries?this._entries[e].push(t.toString()):this._entries[e]=[t.toString()]},o.delete=function(e){delete this._entries[e]},o.get=function(e){return e in this._entries?this._entries[e][0]:null},o.getAll=function(e){return e in this._entries?this._entries[e].slice(0):[]},o.has=function(e){return e in this._entries},o.set=function(e,t){this._entries[e]=[t.toString()]},o.forEach=function(e,t){var n;for(var r in this._entries)if(this._entries.hasOwnProperty(r)){n=this._entries[r];for(var o=0;o<n.length;o++)e.call(t,n[o],r,this)}},o.keys=function(){var e=[];return this.forEach(function(t,n){e.push(n)}),n(e)},o.values=function(){var e=[];return this.forEach(function(t){e.push(t)}),n(e)},o.entries=function(){var e=[];return this.forEach(function(t,n){e.push([n,t])}),n(e)},t&&(o[Symbol.iterator]=o.entries),o.toString=function(){var e="";return this.forEach(function(t,n){e.length>0&&(e+="&"),e+=encodeURIComponent(n)+"="+encodeURIComponent(t)}),e},e.URLSearchParams=r}()}(void 0!==e?e:"undefined"!=typeof window?window:"undefined"!=typeof self?self:this),function(e){if(function(){try{var e=new URL("b","http://a");return e.pathname="c%20d","http://a/c%20d"===e.href&&e.searchParams}catch(e){return!1}}()||function(){var t=e.URL,n=function(e,t){"string"!=typeof e&&(e=String(e));var n=document.implementation.createHTMLDocument("");if(window.doc=n,t){var r=n.createElement("base");r.href=t,n.head.appendChild(r)}var o=n.createElement("a");if(o.href=e,n.body.appendChild(o),o.href=o.href,":"===o.protocol||!/:/.test(o.href))throw new TypeError("Invalid URL");Object.defineProperty(this,"_anchorElement",{value:o})},r=n.prototype,o=function(e){Object.defineProperty(r,e,{get:function(){return this._anchorElement[e]},set:function(t){this._anchorElement[e]=t},enumerable:!0})};["hash","host","hostname","port","protocol","search"].forEach(function(e){o(e)}),Object.defineProperties(r,{toString:{get:function(){var e=this;return function(){return e.href}}},href:{get:function(){return this._anchorElement.href.replace(/\?$/,"")},set:function(e){this._anchorElement.href=e},enumerable:!0},pathname:{get:function(){return this._anchorElement.pathname.replace(/(^\/?)/,"/")},set:function(e){this._anchorElement.pathname=e},enumerable:!0},origin:{get:function(){return this._anchorElement.protocol+"//"+this._anchorElement.hostname+(this._anchorElement.port?":"+this._anchorElement.port:"")},enumerable:!0},password:{get:function(){return""},set:function(e){},enumerable:!0},username:{get:function(){return""},set:function(e){},enumerable:!0},searchParams:{get:function(){var e=new URLSearchParams(this.search),t=this;return["append","delete","set"].forEach(function(n){var r=e[n];e[n]=function(){r.apply(e,arguments),t.search=e.toString()}}),e},enumerable:!0}}),n.createObjectURL=function(e){return t.createObjectURL.apply(t,arguments)},n.revokeObjectURL=function(e){return t.revokeObjectURL.apply(t,arguments)},e.URL=n}(),void 0!==e.location&&!("origin"in e.location)){var t=function(){return e.location.protocol+"//"+e.location.hostname+(e.location.port?":"+e.location.port:"")};try{Object.defineProperty(e.location,"origin",{get:t,enumerable:!0})}catch(n){setInterval(function(){e.location.origin=t()},100)}}}(void 0!==e?e:"undefined"!=typeof window?window:"undefined"!=typeof self?self:this)}).call(t,n(1))}});
=======
/******/ 	
/******/ 	
/******/ 	var hotApplyOnUpdate = true;
/******/ 	var hotCurrentHash = "904bd858ab2c4dd1e50b"; // eslint-disable-line no-unused-vars
/******/ 	var hotCurrentModuleData = {};
/******/ 	var hotCurrentChildModule; // eslint-disable-line no-unused-vars
/******/ 	var hotCurrentParents = []; // eslint-disable-line no-unused-vars
/******/ 	var hotCurrentParentsTemp = []; // eslint-disable-line no-unused-vars
/******/ 	
/******/ 	function hotCreateRequire(moduleId) { // eslint-disable-line no-unused-vars
/******/ 		var me = installedModules[moduleId];
/******/ 		if(!me) return __webpack_require__;
/******/ 		var fn = function(request) {
/******/ 			if(me.hot.active) {
/******/ 				if(installedModules[request]) {
/******/ 					if(installedModules[request].parents.indexOf(moduleId) < 0)
/******/ 						installedModules[request].parents.push(moduleId);
/******/ 				} else {
/******/ 					hotCurrentParents = [moduleId];
/******/ 					hotCurrentChildModule = request;
/******/ 				}
/******/ 				if(me.children.indexOf(request) < 0)
/******/ 					me.children.push(request);
/******/ 			} else {
/******/ 				console.warn("[HMR] unexpected require(" + request + ") from disposed module " + moduleId);
/******/ 				hotCurrentParents = [];
/******/ 			}
/******/ 			return __webpack_require__(request);
/******/ 		};
/******/ 		var ObjectFactory = function ObjectFactory(name) {
/******/ 			return {
/******/ 				configurable: true,
/******/ 				enumerable: true,
/******/ 				get: function() {
/******/ 					return __webpack_require__[name];
/******/ 				},
/******/ 				set: function(value) {
/******/ 					__webpack_require__[name] = value;
/******/ 				}
/******/ 			};
/******/ 		};
/******/ 		for(var name in __webpack_require__) {
/******/ 			if(Object.prototype.hasOwnProperty.call(__webpack_require__, name) && name !== "e") {
/******/ 				Object.defineProperty(fn, name, ObjectFactory(name));
/******/ 			}
/******/ 		}
/******/ 		fn.e = function(chunkId) {
/******/ 			if(hotStatus === "ready")
/******/ 				hotSetStatus("prepare");
/******/ 			hotChunksLoading++;
/******/ 			return __webpack_require__.e(chunkId).then(finishChunkLoading, function(err) {
/******/ 				finishChunkLoading();
/******/ 				throw err;
/******/ 			});
/******/ 	
/******/ 			function finishChunkLoading() {
/******/ 				hotChunksLoading--;
/******/ 				if(hotStatus === "prepare") {
/******/ 					if(!hotWaitingFilesMap[chunkId]) {
/******/ 						hotEnsureUpdateChunk(chunkId);
/******/ 					}
/******/ 					if(hotChunksLoading === 0 && hotWaitingFiles === 0) {
/******/ 						hotUpdateDownloaded();
/******/ 					}
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 		return fn;
/******/ 	}
/******/ 	
/******/ 	function hotCreateModule(moduleId) { // eslint-disable-line no-unused-vars
/******/ 		var hot = {
/******/ 			// private stuff
/******/ 			_acceptedDependencies: {},
/******/ 			_declinedDependencies: {},
/******/ 			_selfAccepted: false,
/******/ 			_selfDeclined: false,
/******/ 			_disposeHandlers: [],
/******/ 			_main: hotCurrentChildModule !== moduleId,
/******/ 	
/******/ 			// Module API
/******/ 			active: true,
/******/ 			accept: function(dep, callback) {
/******/ 				if(typeof dep === "undefined")
/******/ 					hot._selfAccepted = true;
/******/ 				else if(typeof dep === "function")
/******/ 					hot._selfAccepted = dep;
/******/ 				else if(typeof dep === "object")
/******/ 					for(var i = 0; i < dep.length; i++)
/******/ 						hot._acceptedDependencies[dep[i]] = callback || function() {};
/******/ 				else
/******/ 					hot._acceptedDependencies[dep] = callback || function() {};
/******/ 			},
/******/ 			decline: function(dep) {
/******/ 				if(typeof dep === "undefined")
/******/ 					hot._selfDeclined = true;
/******/ 				else if(typeof dep === "object")
/******/ 					for(var i = 0; i < dep.length; i++)
/******/ 						hot._declinedDependencies[dep[i]] = true;
/******/ 				else
/******/ 					hot._declinedDependencies[dep] = true;
/******/ 			},
/******/ 			dispose: function(callback) {
/******/ 				hot._disposeHandlers.push(callback);
/******/ 			},
/******/ 			addDisposeHandler: function(callback) {
/******/ 				hot._disposeHandlers.push(callback);
/******/ 			},
/******/ 			removeDisposeHandler: function(callback) {
/******/ 				var idx = hot._disposeHandlers.indexOf(callback);
/******/ 				if(idx >= 0) hot._disposeHandlers.splice(idx, 1);
/******/ 			},
/******/ 	
/******/ 			// Management API
/******/ 			check: hotCheck,
/******/ 			apply: hotApply,
/******/ 			status: function(l) {
/******/ 				if(!l) return hotStatus;
/******/ 				hotStatusHandlers.push(l);
/******/ 			},
/******/ 			addStatusHandler: function(l) {
/******/ 				hotStatusHandlers.push(l);
/******/ 			},
/******/ 			removeStatusHandler: function(l) {
/******/ 				var idx = hotStatusHandlers.indexOf(l);
/******/ 				if(idx >= 0) hotStatusHandlers.splice(idx, 1);
/******/ 			},
/******/ 	
/******/ 			//inherit from previous dispose call
/******/ 			data: hotCurrentModuleData[moduleId]
/******/ 		};
/******/ 		hotCurrentChildModule = undefined;
/******/ 		return hot;
/******/ 	}
/******/ 	
/******/ 	var hotStatusHandlers = [];
/******/ 	var hotStatus = "idle";
/******/ 	
/******/ 	function hotSetStatus(newStatus) {
/******/ 		hotStatus = newStatus;
/******/ 		for(var i = 0; i < hotStatusHandlers.length; i++)
/******/ 			hotStatusHandlers[i].call(null, newStatus);
/******/ 	}
/******/ 	
/******/ 	// while downloading
/******/ 	var hotWaitingFiles = 0;
/******/ 	var hotChunksLoading = 0;
/******/ 	var hotWaitingFilesMap = {};
/******/ 	var hotRequestedFilesMap = {};
/******/ 	var hotAvailableFilesMap = {};
/******/ 	var hotDeferred;
/******/ 	
/******/ 	// The update info
/******/ 	var hotUpdate, hotUpdateNewHash;
/******/ 	
/******/ 	function toModuleId(id) {
/******/ 		var isNumber = (+id) + "" === id;
/******/ 		return isNumber ? +id : id;
/******/ 	}
/******/ 	
/******/ 	function hotCheck(apply) {
/******/ 		if(hotStatus !== "idle") throw new Error("check() is only allowed in idle status");
/******/ 		hotApplyOnUpdate = apply;
/******/ 		hotSetStatus("check");
/******/ 		return hotDownloadManifest().then(function(update) {
/******/ 			if(!update) {
/******/ 				hotSetStatus("idle");
/******/ 				return null;
/******/ 			}
/******/ 			hotRequestedFilesMap = {};
/******/ 			hotWaitingFilesMap = {};
/******/ 			hotAvailableFilesMap = update.c;
/******/ 			hotUpdateNewHash = update.h;
/******/ 	
/******/ 			hotSetStatus("prepare");
/******/ 			var promise = new Promise(function(resolve, reject) {
/******/ 				hotDeferred = {
/******/ 					resolve: resolve,
/******/ 					reject: reject
/******/ 				};
/******/ 			});
/******/ 			hotUpdate = {};
/******/ 			var chunkId = 9;
/******/ 			{ // eslint-disable-line no-lone-blocks
/******/ 				/*globals chunkId */
/******/ 				hotEnsureUpdateChunk(chunkId);
/******/ 			}
/******/ 			if(hotStatus === "prepare" && hotChunksLoading === 0 && hotWaitingFiles === 0) {
/******/ 				hotUpdateDownloaded();
/******/ 			}
/******/ 			return promise;
/******/ 		});
/******/ 	}
/******/ 	
/******/ 	function hotAddUpdateChunk(chunkId, moreModules) { // eslint-disable-line no-unused-vars
/******/ 		if(!hotAvailableFilesMap[chunkId] || !hotRequestedFilesMap[chunkId])
/******/ 			return;
/******/ 		hotRequestedFilesMap[chunkId] = false;
/******/ 		for(var moduleId in moreModules) {
/******/ 			if(Object.prototype.hasOwnProperty.call(moreModules, moduleId)) {
/******/ 				hotUpdate[moduleId] = moreModules[moduleId];
/******/ 			}
/******/ 		}
/******/ 		if(--hotWaitingFiles === 0 && hotChunksLoading === 0) {
/******/ 			hotUpdateDownloaded();
/******/ 		}
/******/ 	}
/******/ 	
/******/ 	function hotEnsureUpdateChunk(chunkId) {
/******/ 		if(!hotAvailableFilesMap[chunkId]) {
/******/ 			hotWaitingFilesMap[chunkId] = true;
/******/ 		} else {
/******/ 			hotRequestedFilesMap[chunkId] = true;
/******/ 			hotWaitingFiles++;
/******/ 			hotDownloadUpdateChunk(chunkId);
/******/ 		}
/******/ 	}
/******/ 	
/******/ 	function hotUpdateDownloaded() {
/******/ 		hotSetStatus("ready");
/******/ 		var deferred = hotDeferred;
/******/ 		hotDeferred = null;
/******/ 		if(!deferred) return;
/******/ 		if(hotApplyOnUpdate) {
/******/ 			hotApply(hotApplyOnUpdate).then(function(result) {
/******/ 				deferred.resolve(result);
/******/ 			}, function(err) {
/******/ 				deferred.reject(err);
/******/ 			});
/******/ 		} else {
/******/ 			var outdatedModules = [];
/******/ 			for(var id in hotUpdate) {
/******/ 				if(Object.prototype.hasOwnProperty.call(hotUpdate, id)) {
/******/ 					outdatedModules.push(toModuleId(id));
/******/ 				}
/******/ 			}
/******/ 			deferred.resolve(outdatedModules);
/******/ 		}
/******/ 	}
/******/ 	
/******/ 	function hotApply(options) {
/******/ 		if(hotStatus !== "ready") throw new Error("apply() is only allowed in ready status");
/******/ 		options = options || {};
/******/ 	
/******/ 		var cb;
/******/ 		var i;
/******/ 		var j;
/******/ 		var module;
/******/ 		var moduleId;
/******/ 	
/******/ 		function getAffectedStuff(updateModuleId) {
/******/ 			var outdatedModules = [updateModuleId];
/******/ 			var outdatedDependencies = {};
/******/ 	
/******/ 			var queue = outdatedModules.slice().map(function(id) {
/******/ 				return {
/******/ 					chain: [id],
/******/ 					id: id
/******/ 				};
/******/ 			});
/******/ 			while(queue.length > 0) {
/******/ 				var queueItem = queue.pop();
/******/ 				var moduleId = queueItem.id;
/******/ 				var chain = queueItem.chain;
/******/ 				module = installedModules[moduleId];
/******/ 				if(!module || module.hot._selfAccepted)
/******/ 					continue;
/******/ 				if(module.hot._selfDeclined) {
/******/ 					return {
/******/ 						type: "self-declined",
/******/ 						chain: chain,
/******/ 						moduleId: moduleId
/******/ 					};
/******/ 				}
/******/ 				if(module.hot._main) {
/******/ 					return {
/******/ 						type: "unaccepted",
/******/ 						chain: chain,
/******/ 						moduleId: moduleId
/******/ 					};
/******/ 				}
/******/ 				for(var i = 0; i < module.parents.length; i++) {
/******/ 					var parentId = module.parents[i];
/******/ 					var parent = installedModules[parentId];
/******/ 					if(!parent) continue;
/******/ 					if(parent.hot._declinedDependencies[moduleId]) {
/******/ 						return {
/******/ 							type: "declined",
/******/ 							chain: chain.concat([parentId]),
/******/ 							moduleId: moduleId,
/******/ 							parentId: parentId
/******/ 						};
/******/ 					}
/******/ 					if(outdatedModules.indexOf(parentId) >= 0) continue;
/******/ 					if(parent.hot._acceptedDependencies[moduleId]) {
/******/ 						if(!outdatedDependencies[parentId])
/******/ 							outdatedDependencies[parentId] = [];
/******/ 						addAllToSet(outdatedDependencies[parentId], [moduleId]);
/******/ 						continue;
/******/ 					}
/******/ 					delete outdatedDependencies[parentId];
/******/ 					outdatedModules.push(parentId);
/******/ 					queue.push({
/******/ 						chain: chain.concat([parentId]),
/******/ 						id: parentId
/******/ 					});
/******/ 				}
/******/ 			}
/******/ 	
/******/ 			return {
/******/ 				type: "accepted",
/******/ 				moduleId: updateModuleId,
/******/ 				outdatedModules: outdatedModules,
/******/ 				outdatedDependencies: outdatedDependencies
/******/ 			};
/******/ 		}
/******/ 	
/******/ 		function addAllToSet(a, b) {
/******/ 			for(var i = 0; i < b.length; i++) {
/******/ 				var item = b[i];
/******/ 				if(a.indexOf(item) < 0)
/******/ 					a.push(item);
/******/ 			}
/******/ 		}
/******/ 	
/******/ 		// at begin all updates modules are outdated
/******/ 		// the "outdated" status can propagate to parents if they don't accept the children
/******/ 		var outdatedDependencies = {};
/******/ 		var outdatedModules = [];
/******/ 		var appliedUpdate = {};
/******/ 	
/******/ 		var warnUnexpectedRequire = function warnUnexpectedRequire() {
/******/ 			console.warn("[HMR] unexpected require(" + result.moduleId + ") to disposed module");
/******/ 		};
/******/ 	
/******/ 		for(var id in hotUpdate) {
/******/ 			if(Object.prototype.hasOwnProperty.call(hotUpdate, id)) {
/******/ 				moduleId = toModuleId(id);
/******/ 				var result;
/******/ 				if(hotUpdate[id]) {
/******/ 					result = getAffectedStuff(moduleId);
/******/ 				} else {
/******/ 					result = {
/******/ 						type: "disposed",
/******/ 						moduleId: id
/******/ 					};
/******/ 				}
/******/ 				var abortError = false;
/******/ 				var doApply = false;
/******/ 				var doDispose = false;
/******/ 				var chainInfo = "";
/******/ 				if(result.chain) {
/******/ 					chainInfo = "\nUpdate propagation: " + result.chain.join(" -> ");
/******/ 				}
/******/ 				switch(result.type) {
/******/ 					case "self-declined":
/******/ 						if(options.onDeclined)
/******/ 							options.onDeclined(result);
/******/ 						if(!options.ignoreDeclined)
/******/ 							abortError = new Error("Aborted because of self decline: " + result.moduleId + chainInfo);
/******/ 						break;
/******/ 					case "declined":
/******/ 						if(options.onDeclined)
/******/ 							options.onDeclined(result);
/******/ 						if(!options.ignoreDeclined)
/******/ 							abortError = new Error("Aborted because of declined dependency: " + result.moduleId + " in " + result.parentId + chainInfo);
/******/ 						break;
/******/ 					case "unaccepted":
/******/ 						if(options.onUnaccepted)
/******/ 							options.onUnaccepted(result);
/******/ 						if(!options.ignoreUnaccepted)
/******/ 							abortError = new Error("Aborted because " + moduleId + " is not accepted" + chainInfo);
/******/ 						break;
/******/ 					case "accepted":
/******/ 						if(options.onAccepted)
/******/ 							options.onAccepted(result);
/******/ 						doApply = true;
/******/ 						break;
/******/ 					case "disposed":
/******/ 						if(options.onDisposed)
/******/ 							options.onDisposed(result);
/******/ 						doDispose = true;
/******/ 						break;
/******/ 					default:
/******/ 						throw new Error("Unexception type " + result.type);
/******/ 				}
/******/ 				if(abortError) {
/******/ 					hotSetStatus("abort");
/******/ 					return Promise.reject(abortError);
/******/ 				}
/******/ 				if(doApply) {
/******/ 					appliedUpdate[moduleId] = hotUpdate[moduleId];
/******/ 					addAllToSet(outdatedModules, result.outdatedModules);
/******/ 					for(moduleId in result.outdatedDependencies) {
/******/ 						if(Object.prototype.hasOwnProperty.call(result.outdatedDependencies, moduleId)) {
/******/ 							if(!outdatedDependencies[moduleId])
/******/ 								outdatedDependencies[moduleId] = [];
/******/ 							addAllToSet(outdatedDependencies[moduleId], result.outdatedDependencies[moduleId]);
/******/ 						}
/******/ 					}
/******/ 				}
/******/ 				if(doDispose) {
/******/ 					addAllToSet(outdatedModules, [result.moduleId]);
/******/ 					appliedUpdate[moduleId] = warnUnexpectedRequire;
/******/ 				}
/******/ 			}
/******/ 		}
/******/ 	
/******/ 		// Store self accepted outdated modules to require them later by the module system
/******/ 		var outdatedSelfAcceptedModules = [];
/******/ 		for(i = 0; i < outdatedModules.length; i++) {
/******/ 			moduleId = outdatedModules[i];
/******/ 			if(installedModules[moduleId] && installedModules[moduleId].hot._selfAccepted)
/******/ 				outdatedSelfAcceptedModules.push({
/******/ 					module: moduleId,
/******/ 					errorHandler: installedModules[moduleId].hot._selfAccepted
/******/ 				});
/******/ 		}
/******/ 	
/******/ 		// Now in "dispose" phase
/******/ 		hotSetStatus("dispose");
/******/ 		Object.keys(hotAvailableFilesMap).forEach(function(chunkId) {
/******/ 			if(hotAvailableFilesMap[chunkId] === false) {
/******/ 				hotDisposeChunk(chunkId);
/******/ 			}
/******/ 		});
/******/ 	
/******/ 		var idx;
/******/ 		var queue = outdatedModules.slice();
/******/ 		while(queue.length > 0) {
/******/ 			moduleId = queue.pop();
/******/ 			module = installedModules[moduleId];
/******/ 			if(!module) continue;
/******/ 	
/******/ 			var data = {};
/******/ 	
/******/ 			// Call dispose handlers
/******/ 			var disposeHandlers = module.hot._disposeHandlers;
/******/ 			for(j = 0; j < disposeHandlers.length; j++) {
/******/ 				cb = disposeHandlers[j];
/******/ 				cb(data);
/******/ 			}
/******/ 			hotCurrentModuleData[moduleId] = data;
/******/ 	
/******/ 			// disable module (this disables requires from this module)
/******/ 			module.hot.active = false;
/******/ 	
/******/ 			// remove module from cache
/******/ 			delete installedModules[moduleId];
/******/ 	
/******/ 			// remove "parents" references from all children
/******/ 			for(j = 0; j < module.children.length; j++) {
/******/ 				var child = installedModules[module.children[j]];
/******/ 				if(!child) continue;
/******/ 				idx = child.parents.indexOf(moduleId);
/******/ 				if(idx >= 0) {
/******/ 					child.parents.splice(idx, 1);
/******/ 				}
/******/ 			}
/******/ 		}
/******/ 	
/******/ 		// remove outdated dependency from module children
/******/ 		var dependency;
/******/ 		var moduleOutdatedDependencies;
/******/ 		for(moduleId in outdatedDependencies) {
/******/ 			if(Object.prototype.hasOwnProperty.call(outdatedDependencies, moduleId)) {
/******/ 				module = installedModules[moduleId];
/******/ 				if(module) {
/******/ 					moduleOutdatedDependencies = outdatedDependencies[moduleId];
/******/ 					for(j = 0; j < moduleOutdatedDependencies.length; j++) {
/******/ 						dependency = moduleOutdatedDependencies[j];
/******/ 						idx = module.children.indexOf(dependency);
/******/ 						if(idx >= 0) module.children.splice(idx, 1);
/******/ 					}
/******/ 				}
/******/ 			}
/******/ 		}
/******/ 	
/******/ 		// Not in "apply" phase
/******/ 		hotSetStatus("apply");
/******/ 	
/******/ 		hotCurrentHash = hotUpdateNewHash;
/******/ 	
/******/ 		// insert new code
/******/ 		for(moduleId in appliedUpdate) {
/******/ 			if(Object.prototype.hasOwnProperty.call(appliedUpdate, moduleId)) {
/******/ 				modules[moduleId] = appliedUpdate[moduleId];
/******/ 			}
/******/ 		}
/******/ 	
/******/ 		// call accept handlers
/******/ 		var error = null;
/******/ 		for(moduleId in outdatedDependencies) {
/******/ 			if(Object.prototype.hasOwnProperty.call(outdatedDependencies, moduleId)) {
/******/ 				module = installedModules[moduleId];
/******/ 				moduleOutdatedDependencies = outdatedDependencies[moduleId];
/******/ 				var callbacks = [];
/******/ 				for(i = 0; i < moduleOutdatedDependencies.length; i++) {
/******/ 					dependency = moduleOutdatedDependencies[i];
/******/ 					cb = module.hot._acceptedDependencies[dependency];
/******/ 					if(callbacks.indexOf(cb) >= 0) continue;
/******/ 					callbacks.push(cb);
/******/ 				}
/******/ 				for(i = 0; i < callbacks.length; i++) {
/******/ 					cb = callbacks[i];
/******/ 					try {
/******/ 						cb(moduleOutdatedDependencies);
/******/ 					} catch(err) {
/******/ 						if(options.onErrored) {
/******/ 							options.onErrored({
/******/ 								type: "accept-errored",
/******/ 								moduleId: moduleId,
/******/ 								dependencyId: moduleOutdatedDependencies[i],
/******/ 								error: err
/******/ 							});
/******/ 						}
/******/ 						if(!options.ignoreErrored) {
/******/ 							if(!error)
/******/ 								error = err;
/******/ 						}
/******/ 					}
/******/ 				}
/******/ 			}
/******/ 		}
/******/ 	
/******/ 		// Load self accepted modules
/******/ 		for(i = 0; i < outdatedSelfAcceptedModules.length; i++) {
/******/ 			var item = outdatedSelfAcceptedModules[i];
/******/ 			moduleId = item.module;
/******/ 			hotCurrentParents = [moduleId];
/******/ 			try {
/******/ 				__webpack_require__(moduleId);
/******/ 			} catch(err) {
/******/ 				if(typeof item.errorHandler === "function") {
/******/ 					try {
/******/ 						item.errorHandler(err);
/******/ 					} catch(err2) {
/******/ 						if(options.onErrored) {
/******/ 							options.onErrored({
/******/ 								type: "self-accept-error-handler-errored",
/******/ 								moduleId: moduleId,
/******/ 								error: err2,
/******/ 								orginalError: err
/******/ 							});
/******/ 						}
/******/ 						if(!options.ignoreErrored) {
/******/ 							if(!error)
/******/ 								error = err2;
/******/ 						}
/******/ 						if(!error)
/******/ 							error = err;
/******/ 					}
/******/ 				} else {
/******/ 					if(options.onErrored) {
/******/ 						options.onErrored({
/******/ 							type: "self-accept-errored",
/******/ 							moduleId: moduleId,
/******/ 							error: err
/******/ 						});
/******/ 					}
/******/ 					if(!options.ignoreErrored) {
/******/ 						if(!error)
/******/ 							error = err;
/******/ 					}
/******/ 				}
/******/ 			}
/******/ 		}
/******/ 	
/******/ 		// handle errors in accept handlers and self accepted module load
/******/ 		if(error) {
/******/ 			hotSetStatus("fail");
/******/ 			return Promise.reject(error);
/******/ 		}
/******/ 	
/******/ 		hotSetStatus("idle");
/******/ 		return new Promise(function(resolve) {
/******/ 			resolve(outdatedModules);
/******/ 		});
/******/ 	}
/******/
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {},
/******/ 			hot: hotCreateModule(moduleId),
/******/ 			parents: (hotCurrentParentsTemp = hotCurrentParents, hotCurrentParents = [], hotCurrentParentsTemp),
/******/ 			children: []
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, hotCreateRequire(moduleId));
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// identity function for calling harmony imports with the correct context
/******/ 	__webpack_require__.i = function(value) { return value; };
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// __webpack_hash__
/******/ 	__webpack_require__.h = function() { return hotCurrentHash; };
/******/
/******/ 	// Load entry module and return exports
/******/ 	return hotCreateRequire(496)(__webpack_require__.s = 496);
/******/ })
/************************************************************************/
/******/ ({

/***/ 2:
/***/ (function(module, exports) {

var g;

// This works in non-strict mode
g = (function() {
	return this;
})();

try {
	// This works if eval is allowed (see CSP)
	g = g || Function("return this")() || (1,eval)("this");
} catch(e) {
	// This works if the window reference is available
	if(typeof window === "object")
		g = window;
}

// g can still be undefined, but nothing to do about it...
// We return undefined, instead of nothing here, so it's
// easier to handle this case. if(!global) { ...}

module.exports = g;


/***/ }),

/***/ 245:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__app_utils_datepicker__ = __webpack_require__(48);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__components_translatable_input__ = __webpack_require__(34);
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */




var $ = window.$;

$(function () {
  __webpack_require__.i(__WEBPACK_IMPORTED_MODULE_0__app_utils_datepicker__["a" /* default */])();
  new __WEBPACK_IMPORTED_MODULE_1__components_translatable_input__["a" /* default */]();
});

/***/ }),

/***/ 34:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

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

var $ = window.$;

var TranslatableInput = function () {
    function TranslatableInput(options) {
        _classCallCheck(this, TranslatableInput);

        options = options || {};

        this.localeItemSelector = options.localeItemSelector || '.js-locale-item';
        this.localeButtonSelector = options.localeButtonSelector || '.js-locale-btn';
        this.localeInputSelector = options.localeInputSelector || '.js-locale-input';

        $('body').on('click', this.localeItemSelector, this.toggleInputs.bind(this));
    }

    /**
     * Toggle all translatable inputs in form in which locale was changed
     *
     * @param {Event} event
     */


    _createClass(TranslatableInput, [{
        key: 'toggleInputs',
        value: function toggleInputs(event) {
            var localeItem = $(event.target);
            var form = localeItem.closest('form');
            var selectedLocale = localeItem.data('locale');

            form.find(this.localeButtonSelector).text(selectedLocale);
            form.find(this.localeInputSelector).addClass('d-none');
            form.find(this.localeInputSelector + '.js-locale-' + selectedLocale).removeClass('d-none');
        }
    }]);

    return TranslatableInput;
}();

/* harmony default export */ __webpack_exports__["a"] = (TranslatableInput);

/***/ }),

/***/ 48:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(global) {/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_url_polyfill__ = __webpack_require__(75);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_url_polyfill___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_url_polyfill__);
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */


var $ = global.$;

/**
 * Enable all datepickers.
 */
var init = function initDatePickers() {
  $('.datepicker input[type="text"]').datetimepicker({
    locale: global.full_language_code,
    format: 'YYYY-MM-DD'
  });
};

/* harmony default export */ __webpack_exports__["a"] = (init);
/* WEBPACK VAR INJECTION */}.call(__webpack_exports__, __webpack_require__(2)))

/***/ }),

/***/ 496:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(245);


/***/ }),

/***/ 75:
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(global) {(function(global) {
  /**
   * Polyfill URLSearchParams
   *
   * Inspired from : https://github.com/WebReflection/url-search-params/blob/master/src/url-search-params.js
   */

  var checkIfIteratorIsSupported = function() {
    try {
      return !!Symbol.iterator;
    } catch(error) {
      return false;
    }
  };


  var iteratorSupported = checkIfIteratorIsSupported();

  var createIterator = function(items) {
    var iterator = {
      next: function() {
        var value = items.shift();
        return { done: value === void 0, value: value };
      }
    };

    if(iteratorSupported) {
      iterator[Symbol.iterator] = function() {
        return iterator;
      };
    }

    return iterator;
  };

  var polyfillURLSearchParams= function() {

    var URLSearchParams = function(searchString) {
      Object.defineProperty(this, '_entries', { value: {} });

      if(typeof searchString === 'string') {
        if(searchString !== '') {
          searchString = searchString.replace(/^\?/, '');
          var attributes = searchString.split('&');
          var attribute;
          for(var i = 0; i < attributes.length; i++) {
            attribute = attributes[i].split('=');
            this.append(
              decodeURIComponent(attribute[0]),
              (attribute.length > 1) ? decodeURIComponent(attribute[1]) : ''
            );
          }
        }
      } else if(searchString instanceof URLSearchParams) {
        var _this = this;
        searchString.forEach(function(value, name) {
          _this.append(value, name);
        });
      }
    };

    var proto = URLSearchParams.prototype;

    proto.append = function(name, value) {
      if(name in this._entries) {
        this._entries[name].push(value.toString());
      } else {
        this._entries[name] = [value.toString()];
      }
    };

    proto.delete = function(name) {
      delete this._entries[name];
    };

    proto.get = function(name) {
      return (name in this._entries) ? this._entries[name][0] : null;
    };

    proto.getAll = function(name) {
      return (name in this._entries) ? this._entries[name].slice(0) : [];
    };

    proto.has = function(name) {
      return (name in this._entries);
    };

    proto.set = function(name, value) {
      this._entries[name] = [value.toString()];
    };

    proto.forEach = function(callback, thisArg) {
      var entries;
      for(var name in this._entries) {
        if(this._entries.hasOwnProperty(name)) {
          entries = this._entries[name];
          for(var i = 0; i < entries.length; i++) {
            callback.call(thisArg, entries[i], name, this);
          }
        }
      }
    };

    proto.keys = function() {
      var items = [];
      this.forEach(function(value, name) { items.push(name); });
      return createIterator(items);
    };

    proto.values = function() {
      var items = [];
      this.forEach(function(value) { items.push(value); });
      return createIterator(items);
    };

    proto.entries = function() {
      var items = [];
      this.forEach(function(value, name) { items.push([name, value]); });
      return createIterator(items);
    };

    if(iteratorSupported) {
      proto[Symbol.iterator] = proto.entries;
    }

    proto.toString = function() {
      var searchString = '';
      this.forEach(function(value, name) {
        if(searchString.length > 0) searchString+= '&';
        searchString += encodeURIComponent(name) + '=' + encodeURIComponent(value);
      });
      return searchString;
    };

    global.URLSearchParams = URLSearchParams;
  };

  if(!('URLSearchParams' in global) || (new URLSearchParams('?a=1').toString() !== 'a=1')) {
    polyfillURLSearchParams();
  }

  // HTMLAnchorElement

})(
  (typeof global !== 'undefined') ? global
    : ((typeof window !== 'undefined') ? window
    : ((typeof self !== 'undefined') ? self : this))
);

(function(global) {
  /**
   * Polyfill URL
   *
   * Inspired from : https://github.com/arv/DOM-URL-Polyfill/blob/master/src/url.js
   */

  var checkIfURLIsSupported = function() {
    try {
      var u = new URL('b', 'http://a');
      u.pathname = 'c%20d';
      return (u.href === 'http://a/c%20d') && u.searchParams;
    } catch(e) {
      return false;
    }
  };


  var polyfillURL = function() {
    var _URL = global.URL;

    var URL = function(url, base) {
      if(typeof url !== 'string') url = String(url);

      var doc = document.implementation.createHTMLDocument('');
      window.doc = doc;
      if(base) {
        var baseElement = doc.createElement('base');
        baseElement.href = base;
        doc.head.appendChild(baseElement);
      }

      var anchorElement = doc.createElement('a');
      anchorElement.href = url;
      doc.body.appendChild(anchorElement);
      anchorElement.href = anchorElement.href; // force href to refresh

      if(anchorElement.protocol === ':' || !/:/.test(anchorElement.href)) {
        throw new TypeError('Invalid URL');
      }

      Object.defineProperty(this, '_anchorElement', {
        value: anchorElement
      });
    };

    var proto = URL.prototype;

    var linkURLWithAnchorAttribute = function(attributeName) {
      Object.defineProperty(proto, attributeName, {
        get: function() {
          return this._anchorElement[attributeName];
        },
        set: function(value) {
          this._anchorElement[attributeName] = value;
        },
        enumerable: true
      });
    };

    ['hash', 'host', 'hostname', 'port', 'protocol', 'search']
    .forEach(function(attributeName) {
      linkURLWithAnchorAttribute(attributeName);
    });

    Object.defineProperties(proto, {

      'toString': {
        get: function() {
          var _this = this;
          return function() {
            return _this.href;
          };
        }
      },

      'href' : {
        get: function() {
          return this._anchorElement.href.replace(/\?$/,'');
        },
        set: function(value) {
          this._anchorElement.href = value;
        },
        enumerable: true
      },

      'pathname' : {
        get: function() {
          return this._anchorElement.pathname.replace(/(^\/?)/,'/');
        },
        set: function(value) {
          this._anchorElement.pathname = value;
        },
        enumerable: true
      },

      'origin': {
        get: function() {
          return this._anchorElement.protocol + '//' + this._anchorElement.hostname + (this._anchorElement.port ? (':' + this._anchorElement.port) : '');
        },
        enumerable: true
      },

      'password': { // TODO
        get: function() {
          return '';
        },
        set: function(value) {
        },
        enumerable: true
      },

      'username': { // TODO
        get: function() {
          return '';
        },
        set: function(value) {
        },
        enumerable: true
      },

      'searchParams': {
        get: function() {
          var searchParams = new URLSearchParams(this.search);
          var _this = this;
          ['append', 'delete', 'set'].forEach(function(methodName) {
            var method = searchParams[methodName];
            searchParams[methodName] = function() {
              method.apply(searchParams, arguments);
              _this.search = searchParams.toString();
            };
          });
          return searchParams;
        },
        enumerable: true
      }
    });

    URL.createObjectURL = function(blob) {
      return _URL.createObjectURL.apply(_URL, arguments);
    };

    URL.revokeObjectURL = function(url) {
      return _URL.revokeObjectURL.apply(_URL, arguments);
    };

    global.URL = URL;

  };

  if(!checkIfURLIsSupported()) {
    polyfillURL();
  }

  if((global.location !== void 0) && !('origin' in global.location)) {
    var getOrigin = function() {
      return global.location.protocol + '//' + global.location.hostname + (global.location.port ? (':' + global.location.port) : '');
    };

    try {
      Object.defineProperty(global.location, 'origin', {
        get: getOrigin,
        enumerable: true
      });
    } catch(e) {
      setInterval(function() {
        global.location.origin = getOrigin();
      }, 100);
    }
  }

})(
  (typeof global !== 'undefined') ? global
    : ((typeof window !== 'undefined') ? window
    : ((typeof self !== 'undefined') ? self : this))
);

/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(2)))

/***/ })

/******/ });
>>>>>>> 9e2c9a6ce2... finalize UI for 500 error
>>>>>>> 603f702084... finalize UI for 500 error
>>>>>>> finalize UI for 500 error
=======
window.invoices=function(e){function t(r){if(n[r])return n[r].exports;var o=n[r]={i:r,l:!1,exports:{}};return e[r].call(o.exports,o,o.exports,t),o.l=!0,o.exports}var n={};return t.m=e,t.c=n,t.i=function(e){return e},t.d=function(e,n,r){t.o(e,n)||Object.defineProperty(e,n,{configurable:!1,enumerable:!0,get:r})},t.n=function(e){var n=e&&e.__esModule?function(){return e.default}:function(){return e};return t.d(n,"a",n),n},t.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},t.p="",t(t.s=328)}({1:function(e,t){var n;n=function(){return this}();try{n=n||Function("return this")()||(0,eval)("this")}catch(e){"object"==typeof window&&(n=window)}e.exports=n},15:function(e,t,n){"use strict";function r(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(t,"__esModule",{value:!0});var o=function(){function e(e,t){for(var n=0;n<t.length;n++){var r=t[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}return function(t,n,r){return n&&e(t.prototype,n),r&&e(t,r),t}}(),i=n(18),s=window.$,a=function(){function e(t){r(this,e),t=t||{},this.localeItemSelector=t.localeItemSelector||".js-locale-item",this.localeButtonSelector=t.localeButtonSelector||".js-locale-btn",this.localeInputSelector=t.localeInputSelector||".js-locale-input",s("body").on("click",this.localeItemSelector,this.toggleLanguage.bind(this)),i.EventEmitter.on("languageSelected",this.toggleInputs.bind(this))}return o(e,[{key:"toggleLanguage",value:function(e){var t=s(e.target),n=t.closest("form");i.EventEmitter.emit("languageSelected",{selectedLocale:t.data("locale"),form:n})}},{key:"toggleInputs",value:function(e){var t=e.form,n=e.selectedLocale,r=t.find(this.localeButtonSelector),o=r.data("change-language-url");r.text(n),t.find(this.localeInputSelector).addClass("d-none"),t.find(this.localeInputSelector+".js-locale-"+n).removeClass("d-none"),o&&this._saveSelectedLanguage(o,n)}},{key:"_saveSelectedLanguage",value:function(e,t){s.post({url:e,data:{language_iso_code:t}})}}]),e}();t.default=a},18:function(e,t,n){"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.EventEmitter=void 0;var r=n(20),o=function(e){return e&&e.__esModule?e:{default:e}}(r);t.EventEmitter=new o.default},20:function(e,t,n){"use strict";function r(e){console&&console.warn&&console.warn(e)}function o(){o.init.call(this)}function i(e){return void 0===e._maxListeners?o.defaultMaxListeners:e._maxListeners}function s(e,t,n,o){var s,a,u;if("function"!=typeof n)throw new TypeError('The "listener" argument must be of type Function. Received type '+typeof n);if(a=e._events,void 0===a?(a=e._events=Object.create(null),e._eventsCount=0):(void 0!==a.newListener&&(e.emit("newListener",t,n.listener?n.listener:n),a=e._events),u=a[t]),void 0===u)u=a[t]=n,++e._eventsCount;else if("function"==typeof u?u=a[t]=o?[n,u]:[u,n]:o?u.unshift(n):u.push(n),(s=i(e))>0&&u.length>s&&!u.warned){u.warned=!0;var c=new Error("Possible EventEmitter memory leak detected. "+u.length+" "+String(t)+" listeners added. Use emitter.setMaxListeners() to increase limit");c.name="MaxListenersExceededWarning",c.emitter=e,c.type=t,c.count=u.length,r(c)}return e}function a(){for(var e=[],t=0;t<arguments.length;t++)e.push(arguments[t]);this.fired||(this.target.removeListener(this.type,this.wrapFn),this.fired=!0,m(this.listener,this.target,e))}function u(e,t,n){var r={fired:!1,wrapFn:void 0,target:e,type:t,listener:n},o=a.bind(r);return o.listener=n,r.wrapFn=o,o}function c(e,t,n){var r=e._events;if(void 0===r)return[];var o=r[t];return void 0===o?[]:"function"==typeof o?n?[o.listener||o]:[o]:n?p(o):l(o,o.length)}function f(e){var t=this._events;if(void 0!==t){var n=t[e];if("function"==typeof n)return 1;if(void 0!==n)return n.length}return 0}function l(e,t){for(var n=new Array(t),r=0;r<t;++r)n[r]=e[r];return n}function h(e,t){for(;t+1<e.length;t++)e[t]=e[t+1];e.pop()}function p(e){for(var t=new Array(e.length),n=0;n<t.length;++n)t[n]=e[n].listener||e[n];return t}var v,d="object"==typeof Reflect?Reflect:null,m=d&&"function"==typeof d.apply?d.apply:function(e,t,n){return Function.prototype.apply.call(e,t,n)};v=d&&"function"==typeof d.ownKeys?d.ownKeys:Object.getOwnPropertySymbols?function(e){return Object.getOwnPropertyNames(e).concat(Object.getOwnPropertySymbols(e))}:function(e){return Object.getOwnPropertyNames(e)};var y=Number.isNaN||function(e){return e!==e};e.exports=o,o.EventEmitter=o,o.prototype._events=void 0,o.prototype._eventsCount=0,o.prototype._maxListeners=void 0;var g=10;Object.defineProperty(o,"defaultMaxListeners",{enumerable:!0,get:function(){return g},set:function(e){if("number"!=typeof e||e<0||y(e))throw new RangeError('The value of "defaultMaxListeners" is out of range. It must be a non-negative number. Received '+e+".");g=e}}),o.init=function(){void 0!==this._events&&this._events!==Object.getPrototypeOf(this)._events||(this._events=Object.create(null),this._eventsCount=0),this._maxListeners=this._maxListeners||void 0},o.prototype.setMaxListeners=function(e){if("number"!=typeof e||e<0||y(e))throw new RangeError('The value of "n" is out of range. It must be a non-negative number. Received '+e+".");return this._maxListeners=e,this},o.prototype.getMaxListeners=function(){return i(this)},o.prototype.emit=function(e){for(var t=[],n=1;n<arguments.length;n++)t.push(arguments[n]);var r="error"===e,o=this._events;if(void 0!==o)r=r&&void 0===o.error;else if(!r)return!1;if(r){var i;if(t.length>0&&(i=t[0]),i instanceof Error)throw i;var s=new Error("Unhandled error."+(i?" ("+i.message+")":""));throw s.context=i,s}var a=o[e];if(void 0===a)return!1;if("function"==typeof a)m(a,this,t);else for(var u=a.length,c=l(a,u),n=0;n<u;++n)m(c[n],this,t);return!0},o.prototype.addListener=function(e,t){return s(this,e,t,!1)},o.prototype.on=o.prototype.addListener,o.prototype.prependListener=function(e,t){return s(this,e,t,!0)},o.prototype.once=function(e,t){if("function"!=typeof t)throw new TypeError('The "listener" argument must be of type Function. Received type '+typeof t);return this.on(e,u(this,e,t)),this},o.prototype.prependOnceListener=function(e,t){if("function"!=typeof t)throw new TypeError('The "listener" argument must be of type Function. Received type '+typeof t);return this.prependListener(e,u(this,e,t)),this},o.prototype.removeListener=function(e,t){var n,r,o,i,s;if("function"!=typeof t)throw new TypeError('The "listener" argument must be of type Function. Received type '+typeof t);if(void 0===(r=this._events))return this;if(void 0===(n=r[e]))return this;if(n===t||n.listener===t)0==--this._eventsCount?this._events=Object.create(null):(delete r[e],r.removeListener&&this.emit("removeListener",e,n.listener||t));else if("function"!=typeof n){for(o=-1,i=n.length-1;i>=0;i--)if(n[i]===t||n[i].listener===t){s=n[i].listener,o=i;break}if(o<0)return this;0===o?n.shift():h(n,o),1===n.length&&(r[e]=n[0]),void 0!==r.removeListener&&this.emit("removeListener",e,s||t)}return this},o.prototype.off=o.prototype.removeListener,o.prototype.removeAllListeners=function(e){var t,n,r;if(void 0===(n=this._events))return this;if(void 0===n.removeListener)return 0===arguments.length?(this._events=Object.create(null),this._eventsCount=0):void 0!==n[e]&&(0==--this._eventsCount?this._events=Object.create(null):delete n[e]),this;if(0===arguments.length){var o,i=Object.keys(n);for(r=0;r<i.length;++r)"removeListener"!==(o=i[r])&&this.removeAllListeners(o);return this.removeAllListeners("removeListener"),this._events=Object.create(null),this._eventsCount=0,this}if("function"==typeof(t=n[e]))this.removeListener(e,t);else if(void 0!==t)for(r=t.length-1;r>=0;r--)this.removeListener(e,t[r]);return this},o.prototype.listeners=function(e){return c(this,e,!0)},o.prototype.rawListeners=function(e){return c(this,e,!1)},o.listenerCount=function(e,t){return"function"==typeof e.listenerCount?e.listenerCount(t):f.call(e,t)},o.prototype.listenerCount=f,o.prototype.eventNames=function(){return this._eventsCount>0?v(this._events):[]}},328:function(e,t,n){"use strict";function r(e){return e&&e.__esModule?e:{default:e}}var o=n(54),i=r(o),s=n(15),a=r(s);(0,window.$)(function(){(0,i.default)(),new a.default})},54:function(e,t,n){"use strict";(function(e){Object.defineProperty(t,"__esModule",{value:!0}),n(72);var r=e.$,o=function(){r('.datepicker input[type="text"]').datetimepicker({locale:e.full_language_code,format:"YYYY-MM-DD"})};t.default=o}).call(t,n(1))},72:function(e,t,n){(function(e){!function(e){var t=function(){try{return!!Symbol.iterator}catch(e){return!1}}(),n=function(e){var n={next:function(){var t=e.shift();return{done:void 0===t,value:t}}};return t&&(n[Symbol.iterator]=function(){return n}),n},r=function(e){return encodeURIComponent(e).replace(/%20/g,"+")},o=function(e){return decodeURIComponent(e).replace(/\+/g," ")};"URLSearchParams"in e&&"a=1"===new URLSearchParams("?a=1").toString()||function(){var o=function(e){Object.defineProperty(this,"_entries",{writable:!0,value:{}});var t=typeof e;if("undefined"===t);else if("string"===t)""!==e&&this._fromString(e);else if(e instanceof o){var n=this;e.forEach(function(e,t){n.append(t,e)})}else{if(null===e||"object"!==t)throw new TypeError("Unsupported input's type for URLSearchParams");if("[object Array]"===Object.prototype.toString.call(e))for(var r=0;r<e.length;r++){var i=e[r];if("[object Array]"!==Object.prototype.toString.call(i)&&2===i.length)throw new TypeError("Expected [string, any] as entry at index "+r+" of URLSearchParams's input");this.append(i[0],i[1])}else for(var s in e)e.hasOwnProperty(s)&&this.append(s,e[s])}},i=o.prototype;i.append=function(e,t){e in this._entries?this._entries[e].push(String(t)):this._entries[e]=[String(t)]},i.delete=function(e){delete this._entries[e]},i.get=function(e){return e in this._entries?this._entries[e][0]:null},i.getAll=function(e){return e in this._entries?this._entries[e].slice(0):[]},i.has=function(e){return e in this._entries},i.set=function(e,t){this._entries[e]=[String(t)]},i.forEach=function(e,t){var n;for(var r in this._entries)if(this._entries.hasOwnProperty(r)){n=this._entries[r];for(var o=0;o<n.length;o++)e.call(t,n[o],r,this)}},i.keys=function(){var e=[];return this.forEach(function(t,n){e.push(n)}),n(e)},i.values=function(){var e=[];return this.forEach(function(t){e.push(t)}),n(e)},i.entries=function(){var e=[];return this.forEach(function(t,n){e.push([n,t])}),n(e)},t&&(i[Symbol.iterator]=i.entries),i.toString=function(){var e=[];return this.forEach(function(t,n){e.push(r(n)+"="+r(t))}),e.join("&")},e.URLSearchParams=o}();var i=URLSearchParams.prototype;"function"!=typeof i.sort&&(i.sort=function(){var e=this,t=[];this.forEach(function(n,r){t.push([r,n]),e._entries||e.delete(r)}),t.sort(function(e,t){return e[0]<t[0]?-1:e[0]>t[0]?1:0}),e._entries&&(e._entries={});for(var n=0;n<t.length;n++)this.append(t[n][0],t[n][1])}),"function"!=typeof i._fromString&&Object.defineProperty(i,"_fromString",{enumerable:!1,configurable:!1,writable:!1,value:function(e){if(this._entries)this._entries={};else{var t=[];this.forEach(function(e,n){t.push(n)});for(var n=0;n<t.length;n++)this.delete(t[n])}e=e.replace(/^\?/,"");for(var r,i=e.split("&"),n=0;n<i.length;n++)r=i[n].split("="),this.append(o(r[0]),r.length>1?o(r[1]):"")}})}(void 0!==e?e:"undefined"!=typeof window?window:"undefined"!=typeof self?self:this),function(e){if(function(){try{var e=new URL("b","http://a");return e.pathname="c%20d","http://a/c%20d"===e.href&&e.searchParams}catch(e){return!1}}()||function(){var t=e.URL,n=function(t,n){"string"!=typeof t&&(t=String(t));var r,o=document;if(n&&(void 0===e.location||n!==e.location.href)){o=document.implementation.createHTMLDocument(""),r=o.createElement("base"),r.href=n,o.head.appendChild(r);try{if(0!==r.href.indexOf(n))throw new Error(r.href)}catch(e){throw new Error("URL unable to set base "+n+" due to "+e)}}var i=o.createElement("a");if(i.href=t,r&&(o.body.appendChild(i),i.href=i.href),":"===i.protocol||!/:/.test(i.href))throw new TypeError("Invalid URL");Object.defineProperty(this,"_anchorElement",{value:i});var s=new URLSearchParams(this.search),a=!0,u=!0,c=this;["append","delete","set"].forEach(function(e){var t=s[e];s[e]=function(){t.apply(s,arguments),a&&(u=!1,c.search=s.toString(),u=!0)}}),Object.defineProperty(this,"searchParams",{value:s,enumerable:!0});var f=void 0;Object.defineProperty(this,"_updateSearchParams",{enumerable:!1,configurable:!1,writable:!1,value:function(){this.search!==f&&(f=this.search,u&&(a=!1,this.searchParams._fromString(this.search),a=!0))}})},r=n.prototype,o=function(e){Object.defineProperty(r,e,{get:function(){return this._anchorElement[e]},set:function(t){this._anchorElement[e]=t},enumerable:!0})};["hash","host","hostname","port","protocol"].forEach(function(e){o(e)}),Object.defineProperty(r,"search",{get:function(){return this._anchorElement.search},set:function(e){this._anchorElement.search=e,this._updateSearchParams()},enumerable:!0}),Object.defineProperties(r,{toString:{get:function(){var e=this;return function(){return e.href}}},href:{get:function(){return this._anchorElement.href.replace(/\?$/,"")},set:function(e){this._anchorElement.href=e,this._updateSearchParams()},enumerable:!0},pathname:{get:function(){return this._anchorElement.pathname.replace(/(^\/?)/,"/")},set:function(e){this._anchorElement.pathname=e},enumerable:!0},origin:{get:function(){var e={"http:":80,"https:":443,"ftp:":21}[this._anchorElement.protocol],t=this._anchorElement.port!=e&&""!==this._anchorElement.port;return this._anchorElement.protocol+"//"+this._anchorElement.hostname+(t?":"+this._anchorElement.port:"")},enumerable:!0},password:{get:function(){return""},set:function(e){},enumerable:!0},username:{get:function(){return""},set:function(e){},enumerable:!0}}),n.createObjectURL=function(e){return t.createObjectURL.apply(t,arguments)},n.revokeObjectURL=function(e){return t.revokeObjectURL.apply(t,arguments)},e.URL=n}(),void 0!==e.location&&!("origin"in e.location)){var t=function(){return e.location.protocol+"//"+e.location.hostname+(e.location.port?":"+e.location.port:"")};try{Object.defineProperty(e.location,"origin",{get:t,enumerable:!0})}catch(n){setInterval(function(){e.location.origin=t()},100)}}}(void 0!==e?e:"undefined"!=typeof window?window:"undefined"!=typeof self?self:this)}).call(t,n(1))}});
>>>>>>> Builds assets
