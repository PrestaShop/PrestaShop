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

import TranslatableField from '@js/components/translatable-field.js';
import TranslatableInput from '@js/components/translatable-input.js';
import TinyMCEEditor from '@js/components/tinymce-editor.js';
import TaggableField from '@js/components/taggable-field.js';
import ChoiceTable from '@js/components/choice-table.js';
import ChoiceTree from '@js/components/form/choice-tree.js';
import MultipleChoiceTable from '@js/components/multiple-choice-table.js';
import GeneratableInput from '@js/components/generatable-input.js';
import CountryStateSelectionToggler from '@components/country-state-selection-toggler';
import CountryDniRequiredToggler from '@components/country-dni-required-toggler';
import TextWithLengthCounter from '@components/form/text-with-length-counter';
import PreviewOpener from '@components/form/preview-opener';
import MultistoreConfigField from '@js/components/form/multistore-config-field.js';
import {EventEmitter} from '@components/event-emitter';
import GridComponent from '@components/grid/grid';

// Grid extensions
import FiltersResetExtension from '@components/grid/extension/filters-reset-extension';

const Grid = {
  instance: GridComponent,
  extension: {
    FiltersResetExtension,
  },
};

const initPrestashopComponents = () => {
  window.prestashop = {...window.prestashop};

  if (!window.prestashop.instance) {
    window.prestashop.instance = {};
  }

  window.prestashop.component = {
    initComponents(components) {
      components.forEach((component) => {
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
    TranslatableField,
    TinyMCEEditor,
    TranslatableInput,
    TaggableField,
    ChoiceTable,
    EventEmitter,
    ChoiceTree,
    MultipleChoiceTable,
    GeneratableInput,
    CountryStateSelectionToggler,
    CountryDniRequiredToggler,
    TextWithLengthCounter,
    MultistoreConfigField,
    PreviewOpener,
    Grid,
  };
};
export default initPrestashopComponents;
