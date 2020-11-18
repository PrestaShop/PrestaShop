/**
 * 2007-2019 PrestaShop and Contributors
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
import SortingExtension from '../../components/grid/extension/sorting-extension';
import FiltersResetExtension from '../../components/grid/extension/filters-reset-extension';
import ReloadListActionExtension from '../../components/grid/extension/reload-list-extension';
import ColumnTogglingExtension from '../../components/grid/extension/column-toggling-extension';
import SubmitRowActionExtension from '../../components/grid/extension/action/row/submit-row-action-extension';
import ChoiceTree from '../../components/form/choice-tree';
import ExchangeRatesUpdateScheduler from './ExchangeRatesUpdateScheduler';
import LinkRowActionExtension from '../../components/grid/extension/link-row-action-extension';

const $ = window.$;

$(() => {
  const currency = new Grid('currency');
  currency.addExtension(new SortingExtension());
  currency.addExtension(new FiltersResetExtension());
  currency.addExtension(new ReloadListActionExtension());
  currency.addExtension(new ColumnTogglingExtension());
  currency.addExtension(new SubmitRowActionExtension());
  currency.addExtension(new LinkRowActionExtension());

  const choiceTree = new ChoiceTree('#currency_shop_association');
  choiceTree.enableAutoCheckChildren();

  new ExchangeRatesUpdateScheduler();
});
