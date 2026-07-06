/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import ExtraPropertyFormMap from '@pages/extra-property-definition/form/extra-property-form-map';
import FormCollection from '@pages/extra-property-definition/form/form-collection';
import SuggestionDropdown, {SuggestionItem} from '@pages/extra-property-definition/form/suggestion-dropdown';
import {ExtraPropertyCatalogs} from '@pages/extra-property-definition/form/types';

type PlacementVariant = 'forms' | 'grids' | 'apis';

export type PlacementRowValues = Record<string, string>;

interface VariantConfig {
  fields: string[];
  idField: string;
  knownIds: (catalogs: ExtraPropertyCatalogs) => string[];
  /** Catalog entries suggested on the id input (empty = no dropdown, e.g. APIs have search-to-add). */
  idSuggestions: (catalogs: ExtraPropertyCatalogs) => SuggestionItem[];
  /** Anchor field fed from the inline catalog based on the row's id (grids: columns of the grid). */
  anchorSuggestions?: {
    field: string;
    items: (catalogs: ExtraPropertyCatalogs, idValue: string) => SuggestionItem[];
  };
}

const VARIANTS: Record<PlacementVariant, VariantConfig> = {
  forms: {
    fields: ['form_id', 'path', 'mode'],
    idField: 'form_id',
    knownIds: (catalogs) => catalogs.forms.map((entry) => entry.id),
    idSuggestions: (catalogs) => catalogs.forms.map(
      (entry) => ({value: entry.id, label: entry.label, detail: entry.id}),
    ),
  },
  grids: {
    fields: ['grid_id', 'column_id', 'mode'],
    idField: 'grid_id',
    knownIds: (catalogs) => catalogs.grids.map((entry) => entry.id),
    idSuggestions: (catalogs) => catalogs.grids.map(
      (entry) => ({value: entry.id, label: entry.label, detail: entry.id}),
    ),
    anchorSuggestions: {
      field: 'column_id',
      items: (catalogs, gridId) => (catalogs.grids.find((entry) => entry.id === gridId)?.columns ?? []).map(
        (column) => ({value: column.id, label: column.label, detail: column.id}),
      ),
    },
  },
  apis: {
    fields: ['uri', 'methods'],
    idField: 'uri',
    knownIds: (catalogs) => catalogs.apis.map((entry) => entry.uriTemplate),
    idSuggestions: () => [],
  },
};

/**
 * One placement subsection (Forms / Grids / Admin API). The rows ARE the mapped collection
 * entries submitted as form data (the server splits the stored entries into rows and serializes
 * them back — no client-side grammar involved); this class only adds the affordances on top:
 * add/remove against the collection prototype, catalog suggestion dropdowns, the amber
 * not-in-catalog pill and the subsection chrome (count badge, empty state).
 */
export default class PlacementList {
  private container: HTMLElement;

  private config: VariantConfig;

  private knownIds: Set<string>;

  private collection: FormCollection;

  private readOnly: boolean;

  private rowEnhancer?: (row: HTMLElement) => void;

  constructor(container: HTMLElement, catalogs: ExtraPropertyCatalogs, rowEnhancer?: (row: HTMLElement) => void) {
    this.container = container;
    this.config = VARIANTS[<PlacementVariant>container.dataset.listType];
    this.knownIds = new Set(this.config.knownIds(catalogs));
    this.rowEnhancer = rowEnhancer;
    this.readOnly = container.dataset.readOnly === 'true';

    const rowsContainer = <HTMLElement>container.querySelector(ExtraPropertyFormMap.subsection.rows);
    this.collection = new FormCollection(rowsContainer);

    if (this.readOnly) {
      this.collection.rows().forEach((row) => this.refreshUnknownPill(row));

      return;
    }

    this.wireEvents(rowsContainer);
    this.collection.rows().forEach((row) => {
      this.refreshUnknownPill(row);
      this.rowEnhancer?.(row);
    });

    const idSuggestions = this.config.idSuggestions(catalogs);

    if (idSuggestions.length > 0) {
      new SuggestionDropdown(rowsContainer, ExtraPropertyFormMap.rowField(this.config.idField), {
        items: () => idSuggestions,
      });
    }

    const {anchorSuggestions} = this.config;

    if (anchorSuggestions) {
      new SuggestionDropdown(rowsContainer, ExtraPropertyFormMap.rowField(anchorSuggestions.field), {
        items: (input) => {
          const row = input.closest<HTMLElement>(ExtraPropertyFormMap.subsection.row);
          const idValue = row ? FormCollection.read(row, [this.config.idField])[this.config.idField] : '';

          return anchorSuggestions.items(catalogs, idValue);
        },
        // Picking an anchor preselects the runtime's fallback position (same as the forms tree).
        onSelect: (value, input) => {
          const row = input.closest<HTMLElement>(ExtraPropertyFormMap.subsection.row);
          const modeSelect = row?.querySelector<HTMLSelectElement>(ExtraPropertyFormMap.rowField('mode'));

          if (modeSelect && modeSelect.value === '') {
            modeSelect.value = 'after';
            modeSelect.dispatchEvent(new Event('change', {bubbles: true}));
          }
        },
      });
    }
  }

  /**
   * Adds a structured row programmatically (search-to-add affordances).
   */
  addRowWithValues(values: PlacementRowValues): HTMLElement {
    const row = this.collection.add({...this.emptyRow(), ...values});
    this.refreshUnknownPill(row);
    this.rowEnhancer?.(row);
    this.updateChrome();

    return row;
  }

  private wireEvents(rowsContainer: HTMLElement): void {
    ['input', 'change'].forEach((eventName) => {
      rowsContainer.addEventListener(eventName, (event) => {
        const row = (<HTMLElement>event.target).closest<HTMLElement>(ExtraPropertyFormMap.subsection.row);

        if (row) {
          this.refreshUnknownPill(row);
        }
      });
    });

    rowsContainer.addEventListener('click', (event) => {
      const removeButton = (<HTMLElement>event.target).closest(ExtraPropertyFormMap.subsection.removeRow);

      if (removeButton) {
        removeButton.closest<HTMLElement>(ExtraPropertyFormMap.subsection.row)?.remove();
        this.updateChrome();
      }
    });

    this.container
      .querySelector(ExtraPropertyFormMap.subsection.addRow)
      ?.addEventListener('click', () => this.addEmptyRow());
  }

  private addEmptyRow(): void {
    const row = this.collection.add(this.emptyRow());
    this.rowEnhancer?.(row);
    this.updateChrome();
    row.querySelector<HTMLInputElement>(ExtraPropertyFormMap.rowField(this.config.idField))?.focus();
  }

  private emptyRow(): PlacementRowValues {
    const row: PlacementRowValues = {};
    this.config.fields.forEach((field) => {
      row[field] = '';
    });

    return row;
  }

  /**
   * Amber "not in catalog" pill on rows whose id is unknown — a well-formed manual placement is
   * supported and never blocks saving, the pill only pre-warns. The pill is rendered hidden by
   * the form theme on every row; this only toggles it.
   */
  private refreshUnknownPill(row: HTMLElement): void {
    const pill = row.querySelector<HTMLElement>(ExtraPropertyFormMap.subsection.unknownPill);

    if (!pill) {
      return;
    }

    const idValue = FormCollection.read(row, [this.config.idField])[this.config.idField];
    pill.classList.toggle('d-none', idValue === '' || this.knownIds.has(idValue));
  }

  private updateChrome(): void {
    const count = this.collection.rows().length;
    const badge = this.container.querySelector<HTMLElement>(ExtraPropertyFormMap.subsection.countBadge);
    const emptyState = this.container.querySelector<HTMLElement>(ExtraPropertyFormMap.subsection.emptyState);

    if (badge) {
      badge.textContent = String(count);
      badge.classList.toggle('d-none', count === 0);
    }

    emptyState?.classList.toggle('d-none', count > 0);
  }
}
