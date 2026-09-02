/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

$(() => {
  const outstandingGrid = new window.prestashop.component.Grid('outstanding');
  outstandingGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersResetExtension());
  outstandingGrid.addExtension(new window.prestashop.component.GridExtensions.ReloadListExtension());
  outstandingGrid.addExtension(new window.prestashop.component.GridExtensions.SortingExtension());
  outstandingGrid.addExtension(new window.prestashop.component.GridExtensions.LinkRowActionExtension());
  outstandingGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersSubmitButtonEnablerExtension());
});
