/******/!function(t){// webpackBootstrap
/******/var n={};function i(o){if(n[o])return n[o].exports;var e=n[o]={i:o,l:!1,exports:{}};return t[o].call(e.exports,e,e.exports,i),e.l=!0,e.exports}i.m=t,i.c=n,i.d=function(o,e,t){i.o(o,e)||Object.defineProperty(o,e,{enumerable:!0,get:t})},i.r=function(o){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(o,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(o,"__esModule",{value:!0})},i.t=function(e,o){if(1&o&&(e=i(e)),8&o)return e;if(4&o&&"object"==typeof e&&e&&e.__esModule)return e;var t=Object.create(null);if(i.r(t),Object.defineProperty(t,"default",{enumerable:!0,value:e}),2&o&&"string"!=typeof e)for(var n in e)i.d(t,n,function(o){return e[o]}.bind(null,n));return t},i.n=function(o){var e=o&&o.__esModule?function(){return o.default}:function(){return o};return i.d(e,"a",e),e},i.o=function(o,e){return Object.prototype.hasOwnProperty.call(o,e)},i.p="",i(i.s=276)}({276:function(o,e,t){"use strict";function l(o,e){return function(o){if(Array.isArray(o))return o}(o)||function(o,e){var t=[],n=!0,i=!1,l=void 0;try{for(var s,a=o[Symbol.iterator]();!(n=(s=a.next()).done)&&(t.push(s.value),!e||t.length!==e);n=!0);}catch(o){i=!0,l=o}finally{try{n||null==a.return||a.return()}finally{if(i)throw l}}return t}(o,e)||function(){throw new TypeError("Invalid attempt to destructure non-iterable instance")}()}function i(o,e){for(var t=0;t<e.length;t++){var n=e[t];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(o,n.key,n)}}t.r(e);
/**
 * 2007-2019 PrestaShop and Contributors
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
var u=window.$,n=function(){function e(){if(function(o,e){if(!(o instanceof e))throw new TypeError("Cannot call a class as a function")}(this,e),0!==u("#position-filters").length){var o=this;o.$panelSelection=u("#modules-position-selection-panel"),o.$panelSelectionSingleSelection=u("#modules-position-single-selection"),o.$panelSelectionMultipleSelection=u("#modules-position-multiple-selection"),o.$panelSelectionOriginalY=o.$panelSelection.offset().top,o.$showModules=u("#show-modules"),o.$modulesList=u(".modules-position-checkbox"),o.$hookPosition=u("#hook-position"),o.$hookSearch=u("#hook-search"),o.$modulePositionsForm=u("#module-positions-form"),o.$moduleUnhookButton=u("#unhook-button-position-bottom"),o.$moduleButtonsUpdate=u(".module-buttons-update .btn"),o.handleList(),o.handleSortable(),u('input[name="form[general][enable_tos]"]').on("change",function(){return o.handle()})}}var o,t,n;return o=e,(t=[{key:"handleList",value:function(){var n=this;u(window).on("scroll",function(){var o=u(window).scrollTop();n.$panelSelection.css("top",o<20?0:o-n.$panelSelectionOriginalY)}),n.$modulesList.on("change",function(){var o=n.$modulesList.filter(":checked").length;0===o?(n.$moduleUnhookButton.hide(),n.$panelSelection.hide(),n.$panelSelectionSingleSelection.hide(),n.$panelSelectionMultipleSelection.hide()):1===o?(n.$moduleUnhookButton.show(),n.$panelSelection.show(),n.$panelSelectionSingleSelection.show(),n.$panelSelectionMultipleSelection.hide()):(n.$moduleUnhookButton.show(),n.$panelSelection.show(),n.$panelSelectionSingleSelection.hide(),n.$panelSelectionMultipleSelection.show(),u("#modules-position-selection-count").html(o))}),n.$panelSelection.find("button").click(function(){u('button[name="unhookform"]').trigger("click")}),n.$hooksList=[],u("section.hook-panel .hook-name").each(function(){var o=u(this);n.$hooksList.push({title:o.html(),element:o,container:o.parents(".hook-panel")})}),n.$showModules.select2(),n.$showModules.on("change",function(){n.modulesPositionFilterHooks()}),n.$hookPosition.on("change",function(){n.modulesPositionFilterHooks()}),n.$hookSearch.on("input",function(){n.modulesPositionFilterHooks()}),u(".hook-checker").on("click",function(){u(".hook".concat(u(this).data("hook-id"))).prop("checked",u(this).prop("checked"))}),n.$modulesList.on("click",function(){u("#Ghook".concat(u(this).data("hook-id"))).prop("checked",0===u(".hook".concat(u(this).data("hook-id"),":not(:checked)")).length)}),n.$moduleButtonsUpdate.on("click",function(){var o,e=u(this),t=e.closest(".module-item");return 0===(o=e.data("way")?t.next(".module-item"):t.prev(".module-item")).length||(e.data("way")?t.insertAfter(o):t.insertBefore(o),n.updatePositions({hookId:e.data("hook-id"),moduleId:e.data("module-id"),way:e.data("way"),positions:[]},e.closest("ul"))),!1})}},{key:"handleSortable",value:function(){var i=this;u(".sortable").sortable({forcePlaceholderSize:!0,start:function(o,e){u(this).data("previous-index",e.item.index())},update:function(o,e){var t=l(e.item.attr("id").split("_"),2),n={hookId:t[0],moduleId:t[1],way:u(this).data("previous-index")<e.item.index()?1:0,positions:[]};i.updatePositions(n,u(o.target))}})}},{key:"updatePositions",value:function(t,o){u.each(o.children(),function(o,e){t.positions.push(u(e).attr("id"))}),u.ajax({type:"POST",headers:{"cache-control":"no-cache"},url:this.$modulePositionsForm.data("update-url"),data:t,success:function(){var t=0;u.each(o.children(),function(o,e){u(e).find(".index-position").html(++t)}),window.showSuccessMessage(window.update_success_msg)}})}},{key:"modulesPositionFilterHooks",value:function(){for(var o=this,e=o.$hookSearch.val(),t=o.$showModules.val(),n=new RegExp("(".concat(e,")"),"gi"),i=0;i<o.$hooksList.length;i++)o.$hooksList[i].container.toggle(""===e&&"all"===t),o.$hooksList[i].element.html(o.$hooksList[i].title),o.$hooksList[i].container.find(".module-item").removeClass("highlight");if(""!==e||"all"!==t){for(var l,s=u(),a=u(),r=0;r<o.$hooksList.length;r++)"all"!==t&&0<(l=o.$hooksList[r].container.find(".module-position-".concat(t))).length&&(s=s.add(o.$hooksList[r].container),l.addClass("highlight")),""!==e&&-1!==o.$hooksList[r].title.toLowerCase().search(e.toLowerCase())&&(a=a.add(o.$hooksList[r].container),o.$hooksList[r].element.html(o.$hooksList[r].title.replace(n,'<span class="highlight">$1</span>')));"all"===t&&""!==e?a.show():""===e&&"all"!==t?s.show():a.filter(s).show()}if(!o.$hookPosition.prop("checked"))for(var c=0;c<o.$hooksList.length;c++)o.$hooksList[c].container.is(".hook-position")&&o.$hooksList[c].container.hide()}}])&&i(o.prototype,t),n&&i(o,n),e}();(0,window.$)(function(){new n})}});