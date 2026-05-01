/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

$(() => {
  window.prestashop.component.initComponents([
    'TranslatableField',
    'TinyMCEEditor',
    'TranslatableInput',
  ]);

  new window.prestashop.component.CountryStateSelectionToggler(
    '[data-states-url]',
    '[data-country-id]',
    '.js-store-state-row',
  );
});
