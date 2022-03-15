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

// External components
import {EventEmitter} from '@components/event-emitter';

// Core components
import ChoiceTable from '@js/components/choice-table';
import ChoiceTree from '@js/components/form/choice-tree';
import ColorPicker from '@js/app/utils/colorpicker';
import CountryDniRequiredToggler from '@components/country-dni-required-toggler';
import CountryStateSelectionToggler from '@components/country-state-selection-toggler';
import DateRange from '@js/components/form/date-range';
import DeltaQuantityInput from '@components/form/delta-quantity-input';
import DisablingSwitch from '@components/form/disabling-switch';
import GeneratableInput from '@js/components/generatable-input';
import Grid from '@components/grid/grid';
import ModifyAllShopsCheckbox from '@components/modify-all-shops-checkbox';
import MultipleChoiceTable from '@js/components/multiple-choice-table';
import MultistoreConfigField from '@js/components/form/multistore-config-field';
import PreviewOpener from '@components/form/preview-opener';
import Router from '@components/router';
import ShopSelector from '@components/shop-selector/shop-selector';
import TaggableField from '@js/components/taggable-field';
import TextWithLengthCounter from '@components/form/text-with-length-counter';
import TinyMCEEditor from '@js/components/tinymce-editor';
import TranslatableField from '@js/components/translatable-field';
import TranslatableInput from '@js/components/translatable-input';

// Grid extensions
import AsyncToggleColumnExtension from '@components/grid/extension/column/common/async-toggle-column-extension';
import BulkActionCheckboxExtension from '@components/grid/extension/bulk-action-checkbox-extension';
import BulkOpenTabsExtension from '@components/grid/extension/bulk-open-tabs-extension';
import ChoiceExtension from '@components/grid/extension/choice-extension';
import ColumnTogglingExtension from '@components/grid/extension/column-toggling-extension';
import ExportToSqlManagerExtension from '@components/grid/extension/export-to-sql-manager-extension';
import FiltersResetExtension from '@components/grid/extension/filters-reset-extension';
import FiltersSubmitButtonEnablerExtension from '@components/grid/extension/filters-submit-button-enabler-extension';
import IframeClient from '@components/modal/iframe-client';
import LinkRowActionExtension from '@components/grid/extension/link-row-action-extension';
import ModalFormSubmitExtension from '@components/grid/extension/modal-form-submit-extension';
import PositionExtension from '@components/grid/extension/position-extension';
import PreviewExtension from '@components/grid/extension/preview-extension';
import ReloadListExtension from '@components/grid/extension/reload-list-extension';
import SortingExtension from '@components/grid/extension/sorting-extension';
import SubmitBulkActionExtension from '@components/grid/extension/submit-bulk-action-extension';
import AjaxBulkActionExtension from '@components/grid/extension/ajax-bulk-action-extension';
import SubmitGridActionExtension from '@components/grid/extension/submit-grid-action-extension';
import SubmitRowActionExtension from '@components/grid/extension/action/row/submit-row-action-extension';
import FormFieldToggler from '@components/form/form-field-toggler';

const GridExtensions = {
  AjaxBulkActionExtension,
  AsyncToggleColumnExtension,
  BulkActionCheckboxExtension,
  BulkOpenTabsExtension,
  ChoiceExtension,
  ColumnTogglingExtension,
  ExportToSqlManagerExtension,
  FiltersResetExtension,
  FiltersSubmitButtonEnablerExtension,
  LinkRowActionExtension,
  ModalFormSubmitExtension,
  PositionExtension,
  PreviewExtension,
  ReloadListExtension,
  SortingExtension,
  SubmitBulkActionExtension,
  SubmitGridActionExtension,
  SubmitRowActionExtension,
};

const initPrestashopComponents = (): void => {
  window.prestashop = {...window.prestashop};

  if (!window.prestashop.instance) {
    window.prestashop.instance = {};
  }

  window.prestashop.component = {
    initComponents(components: string[]) {
      components.forEach((component: string): void => {
        if (window.prestashop.component[component] === undefined) {
          console.error(`Failed to initialize PrestaShop component "${component}". This component doesn't exist.`);

          return;
        }

        const componentInstanceName = component.charAt(0).toLowerCase() + component.slice(1);

        if (window.prestashop.instance[componentInstanceName] !== undefined) {
          console.warn(
            `Failed to initialize PrestaShop component "${component}". This component is already initialized.`,
          );

          return;
        }

        // EventEmitter is a special case it has no constructor and could be used via
        // window.prestashop.component.EventEmitter straight away
        if (component === 'EventEmitter') {
          window.prestashop.instance[componentInstanceName] = window.prestashop.component[component];

          return;
        }

        window.prestashop.instance[componentInstanceName] = new window.prestashop.component[component]();
      });

      // Send an event so external users can initiate their own components
      EventEmitter.emit('PSComponentsInitiated');
    },
    // @todo: add all standard components in this list
    ChoiceTable,
    ChoiceTree,
    ColorPicker,
    CountryDniRequiredToggler,
    CountryStateSelectionToggler,
    DeltaQuantityInput,
    DisablingSwitch,
    EventEmitter,
    FormFieldToggler,
    GeneratableInput,
    DateRange,
    Grid,
    GridExtensions,
    IframeClient,
    ModifyAllShopsCheckbox,
    MultipleChoiceTable,
    MultistoreConfigField,
    PreviewOpener,
    Router,
    ShopSelector,
    TaggableField,
    TextWithLengthCounter,
    TinyMCEEditor,
    TranslatableField,
    TranslatableInput,
  };
};
export default initPrestashopComponents;
