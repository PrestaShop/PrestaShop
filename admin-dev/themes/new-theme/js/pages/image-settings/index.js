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

import Grid from '@components/grid/grid';
import BulkActionCheckboxExtension from '@components/grid/extension/bulk-action-checkbox-extension';
import SubmitBulkExtension from '@components/grid/extension/submit-bulk-action-extension';
import ExportToSqlManagerExtension from '@components/grid/extension/export-to-sql-manager-extension';
import SortingExtension from '@components/grid/extension/sorting-extension';
import FiltersResetExtension from '@components/grid/extension/filters-reset-extension';
import ReloadListActionExtension from '@components/grid/extension/reload-list-extension';
import SubmitRowActionExtension from '@components/grid/extension/action/row/submit-row-action-extension';
import FiltersSubmitButtonEnablerExtension from '@components/grid/extension/filters-submit-button-enabler-extension';
import LinkRowActionExtension from '@components/grid/extension/link-row-action-extension';
import ImageSettingsIndexMap from '@pages/image-settings/index-map';

const {$} = window;

$(() => {
  $(ImageSettingsIndexMap.secondSelect).addClass('d-none');
  $(ImageSettingsIndexMap.format(
    $(ImageSettingsIndexMap.thumbnailRegenerationImageCategory).val()),
  ).removeClass('d-none');

  const grid = new Grid('image_type');

  grid.addExtension(new BulkActionCheckboxExtension());
  grid.addExtension(new SubmitBulkExtension());
  grid.addExtension(new ExportToSqlManagerExtension());
  grid.addExtension(new SortingExtension());
  grid.addExtension(new FiltersResetExtension());
  grid.addExtension(new ReloadListActionExtension());
  grid.addExtension(new SubmitRowActionExtension());
  grid.addExtension(new FiltersSubmitButtonEnablerExtension());
  grid.addExtension(new LinkRowActionExtension());

  $(ImageSettingsIndexMap.thumbnailRegenerationImageCategory).change(() => {
    $(ImageSettingsIndexMap.secondSelect).addClass('d-none');
    $(ImageSettingsIndexMap.format(
      $(ImageSettingsIndexMap.thumbnailRegenerationImageCategory).val()),
    ).removeClass('d-none');
  });
});
