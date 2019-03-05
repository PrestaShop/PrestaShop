!function(r){function o(t){if(e[t])return e[t].exports;var n=e[t]={i:t,l:!1,exports:{}};return r[t].call(n.exports,n,n.exports,o),n.l=!0,n.exports}var e={};o.m=r,o.c=e,o.i=function(r){return r},o.d=function(r,e,t){o.o(r,e)||Object.defineProperty(r,e,{configurable:!1,enumerable:!0,get:t})},o.n=function(r){var e=r&&r.__esModule?function(){return r.default}:function(){return r};return o.d(e,"a",e),e},o.o=function(r,o){return Object.prototype.hasOwnProperty.call(r,o)},o.p="",o(o.s=277)}({277:function(r,o){/**
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
var e=window.$;e(function(){e('[data-toggle="form-popover-error"]').popover();var r=function(r){var t=e(r.currentTarget),n=t.closest(".form-group"),f=n.find(".invalid-feedback-container"),u=n.find(".form-popover-error"),c=f.width();u.css("width",c);var i=o(f,u);u.css("left",i+"px")},o=function(r,o){return r.offset().left-o.offset().left};e(document).on("shown.bs.popover",'[data-toggle="form-popover-error"]',function(o){return r(o)})})}});