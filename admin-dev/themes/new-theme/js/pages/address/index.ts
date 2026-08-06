/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

$(() => {
  const addressGrid = new window.prestashop.component.Grid('address');

  addressGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersResetExtension());
  addressGrid.addExtension(new window.prestashop.component.GridExtensions.SortingExtension());
  addressGrid.addExtension(new window.prestashop.component.GridExtensions.ExportToSqlManagerExtension());
  addressGrid.addExtension(new window.prestashop.component.GridExtensions.ReloadListExtension());
  addressGrid.addExtension(new window.prestashop.component.GridExtensions.BulkActionCheckboxExtension());
  addressGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitBulkActionExtension());
  addressGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitRowActionExtension());
  addressGrid.addExtension(new window.prestashop.component.GridExtensions.LinkRowActionExtension());

  window.prestashop.component.initComponents(
    [
      'ChoiceTable',
    ],
  );

  // When deep-linked from the country address-format builder
  // (e.g. /admin/sell/addresses#addressRequiredFieldsContainer), expand the
  // required-fields collapse panel and scroll it into view so the merchant
  // lands directly on the form.
  if (window.location.hash === '#addressRequiredFieldsContainer') {
    const target = document.querySelector<HTMLElement>(window.location.hash);

    if (target) {
      $(target).collapse('show');
      target.scrollIntoView({behavior: 'smooth', block: 'start'});
    }
  }
});
