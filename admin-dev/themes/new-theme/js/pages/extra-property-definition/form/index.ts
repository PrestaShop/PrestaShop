/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import ExtraPropertyFormMap from '@pages/extra-property-definition/form/extra-property-form-map';
import AnchorTreeEnhancer from '@pages/extra-property-definition/form/anchor-tree';
import ApiMethodChips from '@pages/extra-property-definition/form/api-method-chips';
import ConstraintBuilder from '@pages/extra-property-definition/form/constraint-builder';
import DeveloperSettings from '@pages/extra-property-definition/form/developer-settings';
import EndpointSearch from '@pages/extra-property-definition/form/endpoint-search';
import PlacementList from '@pages/extra-property-definition/form/placement-list';
import {
  ConstraintCatalog,
  ExtraPropertyCatalogs,
  readJsonPayload,
} from '@pages/extra-property-definition/form/types';

const {$} = window;

/**
 * Entry point of the extra property definition form page (create / edit / read-only view):
 * enhances the row collections (the mapped form data) with pickers, chips, typed option inputs
 * and add/remove affordances. Progressive enhancement — without this bundle the rows stay plain,
 * editable Symfony collection entries (adding rows needs the prototype, hence JS).
 */
$(() => {
  // The read-only view page renders the same widgets without a <form> element, so the page is
  // detected through the builder containers themselves.
  if (
    !document.querySelector(ExtraPropertyFormMap.placementList)
    && !document.querySelector(ExtraPropertyFormMap.constraintBuilder)
  ) {
    return;
  }

  const catalogs = readJsonPayload<ExtraPropertyCatalogs>(ExtraPropertyFormMap.catalogsPayload) ?? {
    forms: [],
    grids: [],
    apis: [],
    defaultFormTypes: {},
  };

  document.querySelectorAll<HTMLElement>(ExtraPropertyFormMap.placementList).forEach((container) => {
    const editable = container.dataset.readOnly !== 'true';
    const isApis = container.dataset.listType === 'apis';
    const chips = isApis && editable ? new ApiMethodChips(catalogs) : undefined;
    const list = new PlacementList(container, catalogs, chips ? (row) => chips.enhance(row) : undefined);

    if (container.dataset.listType === 'forms' && editable) {
      new AnchorTreeEnhancer(container);
    }

    if (isApis && editable) {
      new EndpointSearch(container, catalogs, list);
    }
  });

  const constraintContainer = document.querySelector<HTMLElement>(ExtraPropertyFormMap.constraintBuilder);

  if (constraintContainer) {
    const constraintCatalog = readJsonPayload<ConstraintCatalog>(ExtraPropertyFormMap.constraintCatalogPayload) ?? {};
    new ConstraintBuilder(constraintContainer, constraintCatalog);
  }

  const developerFields = document.querySelector<HTMLElement>(ExtraPropertyFormMap.developerFields);

  if (developerFields) {
    new DeveloperSettings(developerFields, catalogs);
  }
});
