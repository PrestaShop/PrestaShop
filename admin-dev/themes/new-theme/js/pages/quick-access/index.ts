/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

class QuickAccessPage {
  constructor() {
    const quickAccessGrid = new window.prestashop.component.Grid('quick_access');
    quickAccessGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersResetExtension());
    quickAccessGrid.addExtension(new window.prestashop.component.GridExtensions.SortingExtension());
    quickAccessGrid.addExtension(new window.prestashop.component.GridExtensions.BulkActionCheckboxExtension());
    quickAccessGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitBulkActionExtension());
    quickAccessGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitRowActionExtension());
    quickAccessGrid.addExtension(new window.prestashop.component.GridExtensions.LinkRowActionExtension());
    quickAccessGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersSubmitButtonEnablerExtension());
    quickAccessGrid.addExtension(new window.prestashop.component.GridExtensions.AsyncToggleColumnExtension());

    window.prestashop.component.initComponents(
      [
        'TranslatableInput',
      ],
    );
  }
}

$(() => {
  new QuickAccessPage();

  $(document).ajaxComplete((_event, xhr, settings) => {
    if (settings.url && String(settings.url).includes('toggle-new-window')) {
      try {
        const response = JSON.parse(xhr.responseText);

        if (response.status) {
          setTimeout(() => window.location.reload(), 1500);
        }
      } catch (_e) {
        // ignore non-JSON responses
      }
    }
  });
});
