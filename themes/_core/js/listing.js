/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import $ from 'jquery';
import prestashop from 'prestashop';

$(() => {
  $('body').on('click', prestashop.selectors.listing.quickview, (event) => {
    prestashop.emit('clickQuickView', {
      dataset: $(event.target).closest(prestashop.selectors.product.miniature).data(),
    });
    event.preventDefault();
  });
});
