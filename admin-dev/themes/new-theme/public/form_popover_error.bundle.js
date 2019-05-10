!function(r){function t(n){if(o[n])return o[n].exports;var e=o[n]={i:n,l:!1,exports:{}};return r[n].call(e.exports,e,e.exports,t),e.l=!0,e.exports}var o={};t.m=r,t.c=o,t.i=function(r){return r},t.d=function(r,o,n){t.o(r,o)||Object.defineProperty(r,o,{configurable:!1,enumerable:!0,get:n})},t.n=function(r){var o=r&&r.__esModule?function(){return r.default}:function(){return r};return t.d(o,"a",o),o},t.o=function(r,t){return Object.prototype.hasOwnProperty.call(r,t)},t.p="",t(t.s=294)}({294:function(r,t){/**
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
var o=window.$;o(function(){o('[data-toggle="form-popover-error"]').popover({html:!0,content:function(){return n(this)}});var r=function(r){var n=o(r.currentTarget),e=n.closest(".form-group"),f=e.find(".invalid-feedback-container"),u=e.find(".form-popover-error"),i=f.width();u.css("width",i);var c=t(f,u);u.css("left",c+"px")},t=function(r,t){return r.offset().left-t.offset().left},n=function(r){var t=o(r).data("id");return o('.js-popover-error-content[data-id="'+t+'"]').html()};o(document).on("shown.bs.popover",'[data-toggle="form-popover-error"]',function(t){return r(t)})})}});