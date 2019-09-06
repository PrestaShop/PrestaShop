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

import StepVisibilityHandler from './components/step-visibility-handler.js';
import AddRangeHandler from './components/add-range-handler.js';
import BillingChoiceHandler from './components/billing-choice-handler.js';
import CarrierFormMap from './carrier-form-map.js';
import FreeShippingHandler from './components/free-shipping-handler.js';
import SummaryContentHandler from './components/summary-content-handler.js';
import TranslatableInput from './../../components/translatable-input.js';
import ChoiceTree from '../../components/form/choice-tree.js';
import ChoiceTable from '../../components/choice-table.js';
import ZonesCheckHandler from './components/zones-check-handler.js';
import ImageUploader from './components/image-uploader.js';
import ImageRemover from './components/image-remover';
import UnsavedFormWarning from './components/unsaved-form-warning';
import AllZonesPriceFiller from './components/all-zones-price-filler';
import FormStepValidator from './components/form-step-validator';
import RangesTable from "./ranges-table";

const $ = window.$;

$(() => {

  const rangesTable = new RangesTable(
    CarrierFormMap.rangesTable,
    CarrierFormMap.rangeRow,
    CarrierFormMap.rangePriceTemplate,
    CarrierFormMap.rangeFromTemplate,
    CarrierFormMap.rangeToTemplate,
    CarrierFormMap.addRangeBtn,
    CarrierFormMap.removeRangeBtn,
    CarrierFormMap.rangeRemovingBtnRow,
    CarrierFormMap.zoneCheckbox,
  );

  new TranslatableInput();
  new StepVisibilityHandler(CarrierFormMap.formWrapper);
  new FormStepValidator(CarrierFormMap.formWrapper);
  new ChoiceTree(CarrierFormMap.shopAssociation).enableAutoCheckChildren();
  new ChoiceTable();
  new ZonesCheckHandler(CarrierFormMap.zoneCheckbox);

  new AddRangeHandler(
    rangesTable,
    CarrierFormMap.addRangeBtn,
    CarrierFormMap.removeRangeBtn,
  );

  new BillingChoiceHandler(
    CarrierFormMap.rangePriceLabel,
    CarrierFormMap.rangeWeightLabel,
    CarrierFormMap.billingChoice,
  );

  new FreeShippingHandler(
    rangesTable,
    CarrierFormMap.freeShippingChoice,
    CarrierFormMap.handlingCostChoice,
    CarrierFormMap.rangesTable,
    CarrierFormMap.addRangeBtn,
    CarrierFormMap.rangeRow,
    CarrierFormMap.billingChoice,
    CarrierFormMap.taxRuleSelect,
    CarrierFormMap.outrangedBehaviorSelect,
  );

  new SummaryContentHandler(
    CarrierFormMap.nameSummary,
    CarrierFormMap.formWrapper,
    CarrierFormMap.nameInput,
    CarrierFormMap.freeShippingChoice,
    CarrierFormMap.transitTimeInput,
    CarrierFormMap.billingChoice,
    CarrierFormMap.taxRuleSelect,
    CarrierFormMap.rangeRow,
    CarrierFormMap.rangesSummaryWrapper,
    CarrierFormMap.rangeSummary,
    CarrierFormMap.outrangedBehaviorSelect,
    CarrierFormMap.zoneCheckbox,
    CarrierFormMap.zonesSummaryTarget,
    CarrierFormMap.groupAccessTable,
    CarrierFormMap.groupsSummaryTarget,
    CarrierFormMap.shopAssociation,
    CarrierFormMap.shopsSummaryTarget,
    CarrierFormMap.transitSummaryCaseFree,
    CarrierFormMap.transitSummaryCasePriced,
    CarrierFormMap.shippingCostSummaryCasePrice,
    CarrierFormMap.shippingCostSummaryCaseWeight,
    CarrierFormMap.outrangedBehaviorSummaryCaseHighest,
    CarrierFormMap.outrangedBehaviorSummaryCaseDisable,
  );

  new ImageUploader(
    CarrierFormMap.imageUploadBlock,
    CarrierFormMap.imageTarget,
    CarrierFormMap.formWrapper,
    CarrierFormMap.imageRemoveBtn,
  );

  new ImageRemover(
    CarrierFormMap.imageUploadBlock,
    CarrierFormMap.imageTarget,
    CarrierFormMap.imageRemoveBtn,
  );

  new AllZonesPriceFiller(
    CarrierFormMap.rangesTable,
    CarrierFormMap.rangeRow,
  );

  new UnsavedFormWarning();
});
