/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import ShowcaseCard from '@components/showcase-card/showcase-card';
import ShowcaseCardCloseExtension from '@components/showcase-card/extension/showcase-card-close-extension';

const {$} = window;

$(() => {
  const carrierGrid = new window.prestashop.component.Grid('carrier');

  carrierGrid.addExtension(new window.prestashop.component.GridExtensions.SortingExtension());
  carrierGrid.addExtension(new window.prestashop.component.GridExtensions.ReloadListExtension());
  carrierGrid.addExtension(new window.prestashop.component.GridExtensions.PositionExtension(carrierGrid));
  carrierGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersSubmitButtonEnablerExtension());
  carrierGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersResetExtension());
  carrierGrid.addExtension(new window.prestashop.component.GridExtensions.ExportToSqlManagerExtension());
  carrierGrid.addExtension(new window.prestashop.component.GridExtensions.ColumnTogglingExtension());
  carrierGrid.addExtension(new window.prestashop.component.GridExtensions.LinkRowActionExtension());
  carrierGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitRowActionExtension());
  carrierGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitBulkActionExtension());
  carrierGrid.addExtension(new window.prestashop.component.GridExtensions.BulkActionCheckboxExtension());

  const showcaseCard = new ShowcaseCard('carriersShowcaseCard');
  showcaseCard.addExtension(new ShowcaseCardCloseExtension());
});
