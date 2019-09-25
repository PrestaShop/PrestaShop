/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

import Grid from '../../components/grid/grid';
import FiltersResetExtension from '../../components/grid/extension/filters-reset-extension';
import ReloadListActionExtension from '../../components/grid/extension/reload-list-extension';
import ExportToSqlManagerExtension from '../../components/grid/extension/export-to-sql-manager-extension';
import SortingExtension from '../../components/grid/extension/sorting-extension';
import LinkRowActionExtension from '../../components/grid/extension/link-row-action-extension';
import SubmitGridExtension from '../../components/grid/extension/submit-grid-action-extension';
import SubmitBulkExtension from '../../components/grid/extension/submit-bulk-action-extension';
import BulkActionCheckboxExtension from '../../components/grid/extension/bulk-action-checkbox-extension';
import FiltersSubmitButtonEnablerExtension
  from '../../components/grid/extension/filters-submit-button-enabler-extension';
import ChoiceExtension from '../../components/grid/extension/choice-extension';
import ModalFormSubmitExtension from '../../components/grid/extension/modal-form-submit-extension';
import PreviewExtension from '../../components/grid/extension/preview-extension';

const $ = window.$;

$(() => {
  // @todo: I think previewRenderer() should accept `id` and some kind of `params` argument,
  // so grid can pass other data (like URL) to preview renderer
  // in case it uses ajax to load content.
  // @todo: Extract into separate file.
  function previewRenderer(id) {
    return new Promise((resolve, reject) => {
      $.ajax({
        url: 'https://jsonplaceholder.typicode.com/posts/1',
        method: 'GET',
        dataType: 'html',
        data: {
          id_order: id,
        },
      }).then((response) => {
        resolve(response);
      }).fail(() => {
        reject();
      });
    });
  }

  const orderGrid = new Grid('order');
  orderGrid.addExtension(new ReloadListActionExtension());
  orderGrid.addExtension(new ExportToSqlManagerExtension());
  orderGrid.addExtension(new FiltersResetExtension());
  orderGrid.addExtension(new SortingExtension());
  orderGrid.addExtension(new LinkRowActionExtension());
  orderGrid.addExtension(new SubmitGridExtension());
  orderGrid.addExtension(new SubmitBulkExtension());
  orderGrid.addExtension(new BulkActionCheckboxExtension());
  orderGrid.addExtension(new FiltersSubmitButtonEnablerExtension());
  orderGrid.addExtension(new ModalFormSubmitExtension());
  orderGrid.addExtension(new ChoiceExtension());
  orderGrid.addExtension(new PreviewExtension(previewRenderer));
});
