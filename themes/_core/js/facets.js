/**
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
import $ from 'jquery';

let pendingQuery = false;

function updateResults (data) {
    pendingQuery = false;
    prestashop.emit('updateProductList', data);
    window.history.pushState(data, document.title, data.current_url);
}

function handleError () {
    // TODO: feedback
    pendingQuery = false;
}

function makeQuery (url) {
    if (pendingQuery) {
        // wait for current results
    } else {

        // We need to add a parameter to the URL
        // to make it different from the one we're on,
        // otherwise when you do "duplicate tab" under chrome
        // it mixes up the cache between the AJAX request (that
        // returns JSON) and the non-AJAX request (that returns
        // HTML) and you just get a mess of JSON on the duplicated tab.

        const slightlyDifferentURL = [
            url,
            url.indexOf('?') >= 0 ? '&' : '?',
            'from-xhr'
        ].join('');

        $
          .get(slightlyDifferentURL, null, null, 'json')
          .then(updateResults)
          .fail(handleError)
        ;
    }
}

$(document).ready(function () {
    prestashop.on('updateFacets', (param) => {
      makeQuery(param);
    });
});
