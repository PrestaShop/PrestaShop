/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import FormSubmitButton from '@components/form-submit-button';

const {$} = window;

$(() => {
  ['manufacturer', 'manufacturer_address'].forEach((gridName) => {
    const grid = new window.prestashop.component.Grid(gridName);
    grid.addExtension(new window.prestashop.component.GridExtensions.ExportToSqlManagerExtension());
    grid.addExtension(new window.prestashop.component.GridExtensions.ReloadListExtension());
    grid.addExtension(new window.prestashop.component.GridExtensions.SortingExtension());
    grid.addExtension(new window.prestashop.component.GridExtensions.FiltersResetExtension());
    grid.addExtension(new window.prestashop.component.GridExtensions.ColumnTogglingExtension());
    grid.addExtension(new window.prestashop.component.GridExtensions.SubmitRowActionExtension());
    grid.addExtension(new window.prestashop.component.GridExtensions.SubmitBulkActionExtension());
    grid.addExtension(new window.prestashop.component.GridExtensions.BulkActionCheckboxExtension());
    grid.addExtension(new window.prestashop.component.GridExtensions.FiltersSubmitButtonEnablerExtension());
    grid.addExtension(new window.prestashop.component.GridExtensions.LinkRowActionExtension());
  });

  window.prestashop.component.initComponents(
    [
      'TranslatableInput',
      'TranslatableField',
      'TinyMCEEditor',
    ],
  );

  new window.prestashop.component.TaggableField({
    tokenFieldSelector: 'input.js-taggable-field',
    options: {
      createTokensOnBlur: true,
    },
  });

  new window.prestashop.component.ChoiceTree('#manufacturer_shop_association').enableAutoCheckChildren();

  new FormSubmitButton();
});
