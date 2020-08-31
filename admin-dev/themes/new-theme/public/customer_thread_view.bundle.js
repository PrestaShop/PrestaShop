window.customer_thread_view=function(e){function r(t){if(o[t])return o[t].exports;var n=o[t]={i:t,l:!1,exports:{}};return e[t].call(n.exports,n,n.exports,r),n.l=!0,n.exports}var o={};return r.m=e,r.c=o,r.i=function(e){return e},r.d=function(e,o,t){r.o(e,o)||Object.defineProperty(e,o,{configurable:!1,enumerable:!0,get:t})},r.n=function(e){var o=e&&e.__esModule?function(){return e.default}:function(){return e};return r.d(o,"a",o),o},r.o=function(e,r){return Object.prototype.hasOwnProperty.call(e,r)},r.p="",r(r.s=484)}({402:function(e,r,o){"use strict";Object.defineProperty(r,"__esModule",{value:!0}),/**
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
r.default={forwardCustomerThreadModal:"#forwardThreadModal",forwardSomeoneElseEmailInput:"#forward_customer_thread_someone_else_email",forwardEmployeeInput:"#forward_customer_thread_employee_id"}},484:function(e,r,o){"use strict";var t=o(402),n=function(e){return e&&e.__esModule?e:{default:e}}(t),u=window.$;/**
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
u(function(){u(n.default.forwardEmployeeInput).on("change",function(e){var r=u(n.default.forwardSomeoneElseEmailInput),o=r.closest(".form-group"),t=u(e.currentTarget).val();0===parseInt(t,10)?o.removeClass("d-none"):o.addClass("d-none")})})}});