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

import StepVisibilityHandler from './step-visibility-handler.js';
import AddRangeHandler from './add-range-handler.js';
import RangesContentSwitcher from './ranges-content-switcher.js';
import CarrierFormMap from './carrier-form-map.js';
import FreeShippingToggleHandler from './free-shipping-toggle-handler.js';
import SummaryContentHandler from './summary-content-handler.js';
import TranslatableInput from './../../components/translatable-input.js';
import ChoiceTree from '../../components/form/choice-tree.js';
import ChoiceTable from '../../components/choice-table.js';
import ZonesCheckHandler from './zones-check-handler.js';

const $ = window.$;

$(() => {
  new TranslatableInput();
  new StepVisibilityHandler(CarrierFormMap.formWrapper);
  new ChoiceTree(CarrierFormMap.shopAssociation).enableAutoCheckChildren();
  new ChoiceTable();
  new ZonesCheckHandler(CarrierFormMap.zoneCheckbox);

  new AddRangeHandler(
    CarrierFormMap.rangesTable,
    CarrierFormMap.rangePriceTemplate,
    CarrierFormMap.rangeFromTemplate,
    CarrierFormMap.rangeToTemplate,
    CarrierFormMap.addRangeBtn,
    CarrierFormMap.removeRangeBtn,
    CarrierFormMap.rangeRemovingBtnRow,
  );

  new RangesContentSwitcher(
    CarrierFormMap.rangePriceLabel,
    CarrierFormMap.rangeWeightLabel,
    CarrierFormMap.billingChoice,
  );

  new FreeShippingToggleHandler(
    CarrierFormMap.freeShippingChoice,
    CarrierFormMap.handlingCostChoice,
    CarrierFormMap.rangesTable,
    CarrierFormMap.addRangeBtn,
    CarrierFormMap.rangeRow,
  );

  new SummaryContentHandler(
    CarrierFormMap.formWrapper,
    CarrierFormMap.carrierNameInput,
    CarrierFormMap.freeShippingChoice,
    CarrierFormMap.transitTimeInput,
    CarrierFormMap.billingChoice,
    CarrierFormMap.taxRuleSelect,
    CarrierFormMap.rangeRow,
    CarrierFormMap.rangesSummaryWrapper,
    CarrierFormMap.outrangedBehaviorSelect,
    CarrierFormMap.zoneCheckbox,
    CarrierFormMap.zonesSummaryTarget,
    CarrierFormMap.groupAccessTable,
    CarrierFormMap.groupsSummaryTarget,
    CarrierFormMap.shopAssociation,
    CarrierFormMap.shopsSummaryTarget,
  );
});
