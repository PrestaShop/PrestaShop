/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import Routing from 'fos-routing';
import routes from '@js/fos_js_routes.json';

const {$} = window;

/* eslint-disable */
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
 * And run `bin/console fos:js-routing:dump --format=json --target=admin-dev/themes/new-theme/js/fos_js_routes.json`
 */
/* eslint-enable */
export default class Router {
  constructor() {
    if (window.prestashop && window.prestashop.customRoutes) {
      Object.assign(routes.routes, window.prestashop.customRoutes);
    }

    Routing.setData(routes);
    Routing.setBaseUrl(
      $(document)
        .find('body')
        .data('base-url'),
    );
  }

  /**
   * Decorated "generate" method, with predefined security token in params
   *
   * @param route
   * @param params
   *
   * @returns {String}
   */
  generate(route: string, params: Record<string, unknown> = {}): string {
    const tokenizedParams = Object.assign(params, {
      _token: $(document)
        .find('body')
        .data('token'),
    });

    return Routing.generate(route, tokenizedParams);
  }
}
