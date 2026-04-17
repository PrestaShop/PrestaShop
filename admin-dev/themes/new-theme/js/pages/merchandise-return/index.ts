/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

$(() => {
  window.prestashop.component.initComponents(
    [
      'MultistoreConfigField',
      'TranslatableInput',
    ],
  );

  const grid = new window.prestashop.component.Grid('merchandise_return');
  grid.addExtension(new window.prestashop.component.GridExtensions.SortingExtension());
  grid.addExtension(new window.prestashop.component.GridExtensions.FiltersResetExtension());
  grid.addExtension(new window.prestashop.component.GridExtensions.FiltersSubmitButtonEnablerExtension());
});
