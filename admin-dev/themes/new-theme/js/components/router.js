/**
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

import Routing from 'fos-routing';
import routes from '@js/fos_js_routes.json';

const {$} = window;

/**
 * Wraps FOSJsRoutingbundle with exposed routes.
 * To expose route add option `expose: true` in .yml routing config
 *
 * e.g.
 *
 * `my_route
 *    path: /my-path
 *    options:
 *      expose: true
 * `
 * And run `bin/console fos:js-routing:dump --format=json --target=admin-dev/themes/new-theme/js/fos_js_routes.json`
 */
export default class Router {
  constructor() {
    Routing.setData(routes);
    Routing.setBaseUrl($(document).find('body').data('base-url'));

    return this;
  }

  /**
   * Decorated "generate" method, with predefined security token in params
   *
   * @param route
   * @param params
   *
   * @returns {String}
   */
  generate(route, params = {}) {
    const tokenizedParams = Object.assign(params, {_token: $(document).find('body').data('token')});

    return Routing.generate(route, tokenizedParams);
  }
}
