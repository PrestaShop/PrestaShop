window.form_popover_error=function(r){function t(n){if(o[n])return o[n].exports;var e=o[n]={i:n,l:!1,exports:{}};return r[n].call(e.exports,e,e.exports,t),e.l=!0,e.exports}var o={};return t.m=r,t.c=o,t.i=function(r){return r},t.d=function(r,o,n){t.o(r,o)||Object.defineProperty(r,o,{configurable:!1,enumerable:!0,get:n})},t.n=function(r){var o=r&&r.__esModule?function(){return r.default}:function(){return r};return t.d(o,"a",o),o},t.o=function(r,t){return Object.prototype.hasOwnProperty.call(r,t)},t.p="",t(t.s=305)}({305:function(r,t,o){"use strict";/**
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
var n=window.$;n(function(){n('[data-toggle="form-popover-error"]').popover({html:!0,content:function(){return o(this)}});var r=function(r){var o=n(r.currentTarget),e=o.closest(".form-group"),u=e.find(".invalid-feedback-container"),f=e.find(".form-popover-error"),i=u.width();f.css("width",i);var c=t(u,f);f.css("left",c+"px")},t=function(r,t){return r.offset().left-t.offset().left},o=function(r){var t=n(r).data("id");return n('.js-popover-error-content[data-id="'+t+'"]').html()};n(document).on("shown.bs.popover",'[data-toggle="form-popover-error"]',function(t){return r(t)})})}});