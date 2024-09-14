/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
