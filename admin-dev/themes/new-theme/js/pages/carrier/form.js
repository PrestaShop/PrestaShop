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
import CarrierMap from './carrier-map.js';
import FreeShippingToggleHandler from './free-shipping-toggle-handler.js';
import SummaryContentHandler from './summary-content-handler';
import TranslatableInput from './../../components/translatable-input';

const $ = window.$;

$(document).ready(() => {
  new TranslatableInput();
  new StepVisibilityHandler(CarrierMap.formWrapper);
  new AddRangeHandler(
    CarrierMap.rangesTable,
    CarrierMap.rangesTemplate,
    CarrierMap.appendButtons
  );
  new RangesContentSwitcher(
    CarrierMap.rangePriceLabel,
    CarrierMap.rangeWeightLabel,
    CarrierMap.billingChoice
  );
  new FreeShippingToggleHandler(
    CarrierMap.freeShippingChoice,
    CarrierMap.handlingCostChoice,
    CarrierMap.rangesTable,
    CarrierMap.addRangeBtn,
    CarrierMap.rangeRow
  );

  new SummaryContentHandler(
    CarrierMap.formWrapper,
    CarrierMap.freeShippingChoice,
    CarrierMap.transitTimeInput,
    CarrierMap.billingChoice,
    CarrierMap.taxRuleSelect,
    CarrierMap.rangeRow,
    CarrierMap.rangesSummaryWrapper,
    CarrierMap.outrangedSelect,
    CarrierMap.zoneCheck,
    CarrierMap.zonesSummaryTarget,
    CarrierMap.groupChecks,
    CarrierMap.groupsSummaryTarget
  );
});
