/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

$(() => {
  window.prestashop.component.initComponents([
    'TinyMCEEditor',
    'TranslatableInput',
  ]);

  new window.prestashop.component.CountryStateSelectionToggler(
    '#store_id_country',
    '#store_id_state',
    '.js-store-state-row',
  );
});
