/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import ShowcaseCardCloseExtension from '@components/showcase-card/extension/showcase-card-close-extension';
import ShowcaseCard from '@components/showcase-card/showcase-card';

const {$} = window;

$(() => {
  const grid = new window.prestashop.component.Grid('feature');

  grid.addExtension(new window.prestashop.component.GridExtensions.ExportToSqlManagerExtension());
  grid.addExtension(new window.prestashop.component.GridExtensions.ReloadListExtension());
  grid.addExtension(new window.prestashop.component.GridExtensions.SortingExtension());
  grid.addExtension(new window.prestashop.component.GridExtensions.FiltersResetExtension());
  grid.addExtension(new window.prestashop.component.GridExtensions.SubmitRowActionExtension());
  grid.addExtension(new window.prestashop.component.GridExtensions.SubmitBulkActionExtension());
  grid.addExtension(new window.prestashop.component.GridExtensions.BulkActionCheckboxExtension());
  grid.addExtension(new window.prestashop.component.GridExtensions.FiltersSubmitButtonEnablerExtension());
  grid.addExtension(new window.prestashop.component.GridExtensions.LinkRowActionExtension());
  grid.addExtension(new window.prestashop.component.GridExtensions.PositionExtension(grid));

  const showcaseCard = new ShowcaseCard('featuresShowcaseCard');
  showcaseCard.addExtension(new ShowcaseCardCloseExtension());
});
