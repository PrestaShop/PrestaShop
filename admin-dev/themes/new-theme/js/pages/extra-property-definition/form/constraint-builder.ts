/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import ExtraPropertyFormMap from '@pages/extra-property-definition/form/extra-property-form-map';
import FormCollection from '@pages/extra-property-definition/form/form-collection';
import groupConstraintNames from '@pages/extra-property-definition/form/constraint-groups';
import SuggestionDropdown, {SuggestionItem} from '@pages/extra-property-definition/form/suggestion-dropdown';
import cloneTemplate from '@pages/extra-property-definition/form/templates';
import {
  quoteValue,
  parseTail,
  serializeTail,
  TailOption,
  unquoteValue,
} from '@pages/extra-property-definition/form/grammar/constraint-dsl';
import {ConstraintCatalog} from '@pages/extra-property-definition/form/types';

/**
 * The Validation card's constraint builder. The rows ARE the mapped collection entries submitted
 * as form data (name + verbatim options tail + per_language flag — the server folds them back
 * into the DSL); this class only adds the affordances on top: grouped name picker, typed option
 * inputs driven by the constraint catalog, add/remove against the collection prototype, and the
 * per-language zone chrome. Rows created in the zone carry per_language=1 — the submitted truth;
 * the zone's visibility reacts to the field-definition scope. A row whose argument tail the
 * lexer cannot represent keeps a raw monospace tail input instead of typed inputs — nothing is
 * ever lost.
 */
export default class ConstraintBuilder {
  private container: HTMLElement;

  private catalog: ConstraintCatalog;

  private collection: FormCollection;

  private rowsContainer: HTMLElement;

  private langRowsContainer: HTMLElement;

  private zone: HTMLElement;

  private scopeSelect: HTMLSelectElement | null;

  private readOnly: boolean;

  constructor(container: HTMLElement, catalog: ConstraintCatalog) {
    this.container = container;
    this.catalog = catalog;
    this.rowsContainer = <HTMLElement>container.querySelector(ExtraPropertyFormMap.subsection.rows);
    this.zone = <HTMLElement>container.querySelector(ExtraPropertyFormMap.constraint.perLanguageZone);
    this.langRowsContainer = <HTMLElement> this.zone.querySelector(ExtraPropertyFormMap.constraint.langRows);
    this.scopeSelect = document.querySelector<HTMLSelectElement>(ExtraPropertyFormMap.scopeSelect);
    this.readOnly = container.dataset.readOnly === 'true';
    this.collection = new FormCollection(this.rowsContainer);

    this.allRows().forEach((row) => this.renderOptionsEditor(row));

    if (this.readOnly) {
      return;
    }

    this.wireEvents();
    this.updateZone();
  }

  private allRows(): HTMLElement[] {
    const rowSelector = ExtraPropertyFormMap.subsection.row;

    return [
      ...Array.from(this.rowsContainer.querySelectorAll<HTMLElement>(rowSelector)),
      ...Array.from(this.langRowsContainer.querySelectorAll<HTMLElement>(rowSelector)),
    ];
  }

  // ── Events ────────────────────────────────────────────────────────────────────────────────

  private wireEvents(): void {
    const builderView = <HTMLElement> this.container.querySelector(ExtraPropertyFormMap.subsection.builderView);

    builderView.addEventListener('input', (event) => {
      const target = <HTMLElement>event.target;

      if (target.matches(ExtraPropertyFormMap.rowField('name'))) {
        const row = target.closest<HTMLElement>(ExtraPropertyFormMap.subsection.row);

        if (row) {
          this.renderOptionsEditor(row);
        }
      }
    });

    builderView.addEventListener('click', (event) => {
      const removeButton = (<HTMLElement>event.target).closest(ExtraPropertyFormMap.subsection.removeRow);

      if (removeButton) {
        removeButton.closest<HTMLElement>(ExtraPropertyFormMap.subsection.row)?.remove();
        this.updateChrome();
        this.updateZone();
      }
    });

    // Grouped constraint name picker (Presence / Text / Number / ... headings); picking a name
    // fires "input", which re-renders the row's typed option editor through the listener above.
    new SuggestionDropdown(builderView, ExtraPropertyFormMap.rowField('name'), {
      items: () => this.nameSuggestions(),
    });

    this.container
      .querySelector(ExtraPropertyFormMap.subsection.addRow)
      ?.addEventListener('click', () => this.addRow('0'));
    this.container
      .querySelector(ExtraPropertyFormMap.constraint.addLangRow)
      ?.addEventListener('click', () => this.addRow('1'));

    this.scopeSelect?.addEventListener('change', () => this.updateZone());
  }

  private addRow(perLanguage: string): void {
    const target = perLanguage === '1' ? this.langRowsContainer : this.rowsContainer;
    const row = this.collection.add({name: '', options: '', per_language: perLanguage}, target);
    this.renderOptionsEditor(row);
    this.updateChrome();
    this.updateZone();
    row.querySelector<HTMLInputElement>(ExtraPropertyFormMap.rowField('name'))?.focus();
  }

  // ── Typed option editor ───────────────────────────────────────────────────────────────────

  /**
   * Fills the row's editor container (rendered empty by the form theme) with catalog-driven
   * typed inputs cloned from the option-* templates: one field per option present in the tail
   * (label + int/bool/string-typed input + remove), an "add option" select for the remaining
   * catalog options, a single value input for default-option constraints, and a raw monospace
   * tail input when the tail cannot be lexed (or for composites, whose tail is nested
   * constraints). The server-rendered tail display is dropped once the editor takes over.
   */
  private renderOptionsEditor(row: HTMLElement): void {
    const optionsInput = row.querySelector<HTMLInputElement>(ExtraPropertyFormMap.rowField('options'));
    const structuredFields = row.querySelector<HTMLElement>(ExtraPropertyFormMap.subsection.structuredFields);
    const editor = structuredFields?.querySelector<HTMLElement>(ExtraPropertyFormMap.constraint.optionsEditor);

    if (!optionsInput || !structuredFields || !editor) {
      return;
    }

    structuredFields.querySelector(ExtraPropertyFormMap.constraint.optionsDisplay)?.remove();
    editor.replaceChildren();

    const {name} = FormCollection.read(row, ['name']);
    const entry = this.catalog[name];

    if (!entry) {
      return;
    }

    const writeTail = (options: TailOption[]): void => {
      optionsInput.value = serializeTail(options);
      optionsInput.dispatchEvent(new Event('input', {bubbles: true}));
    };

    if (entry.composite) {
      this.rawTailInput(editor, optionsInput, 'option-nested');

      return;
    }

    const lexed = parseTail(optionsInput.value);

    if (lexed === null) {
      this.rawTailInput(editor, optionsInput, 'option-raw');

      return;
    }

    const options: TailOption[] = [...lexed];

    // A default-option constraint gets its single value input right away.
    if (options.length === 0 && entry.defaultOption !== null && !this.readOnly) {
      options.push({key: null, value: ''});
    }

    this.renderOptionFields(editor, entry, options, writeTail);
  }

  /**
   * Renders the typed option fields from the in-memory options model (NOT from the serialized
   * tail — empty pending values are part of the model but not of the tail), plus the
   * "+ Add an option" select over the not-yet-present catalog options. Add/remove mutate the
   * model and re-render, so the select always reflects what is left to offer.
   */
  private renderOptionFields(
    editor: HTMLElement,
    entry: {defaultOption: string | null; options: Record<string, {type: string}>},
    options: TailOption[],
    writeTail: (tailOptions: TailOption[]) => void,
    focusKey?: string | null,
  ): void {
    editor.replaceChildren();

    // No configured value on the read-only view, or nothing configurable at all: plain text.
    if (options.length === 0 && (this.readOnly || Object.keys(entry.options).length === 0)) {
      editor.appendChild(cloneTemplate('option-none'));

      return;
    }

    const rerender = (focus?: string | null): void => this.renderOptionFields(editor, entry, options, writeTail, focus);

    options.forEach((option) => {
      editor.appendChild(this.optionField(entry, option, options, writeTail, rerender));
    });

    // A positional value IS the default option: exclude it from the addable list too.
    const remaining = Object.keys(entry.options).filter(
      (optionName) => !options.some(
        (option) => option.key === optionName || (option.key === null && optionName === entry.defaultOption),
      ),
    );

    if (remaining.length > 0 && !this.readOnly) {
      const addSelect = <HTMLSelectElement>cloneTemplate('option-add');
      remaining.forEach((optionName) => {
        // Bare data elements (not layout) — no template needed.
        addSelect.appendChild(new Option(optionName, optionName));
      });
      addSelect.addEventListener('change', () => {
        if (addSelect.value !== '') {
          options.push({key: addSelect.value, value: ''});
          rerender(addSelect.value);
        }
      });
      editor.appendChild(addSelect);
    }

    if (focusKey !== undefined) {
      const focusField = Array.from(editor.querySelectorAll<HTMLElement>(ExtraPropertyFormMap.constraint.optionField))
        .find((field) => field.dataset.optionKey === String(focusKey));
      focusField?.querySelector<HTMLInputElement>(ExtraPropertyFormMap.constraint.optionInput)?.focus();
    }
  }

  private optionField(
    entry: {defaultOption: string | null; options: Record<string, {type: string}>},
    option: TailOption,
    options: TailOption[],
    writeTail: (tailOptions: TailOption[]) => void,
    rerender: (focus?: string | null) => void,
  ): HTMLElement {
    const field = cloneTemplate('option-field');
    field.dataset.optionKey = String(option.key);

    // A positional value feeds the constraint's default option: show that name so the user
    // knows which option is being edited.
    const labelText = option.key ?? entry.defaultOption;
    const label = <HTMLElement>field.querySelector(ExtraPropertyFormMap.constraint.optionLabel);

    if (labelText === null) {
      label.remove();
    } else {
      label.textContent = labelText;
    }

    const type = entry.options[option.key ?? entry.defaultOption ?? '']?.type ?? '';
    const isNumber = ['int', 'integer', 'float', 'number'].includes(type);
    const isString = ['string'].includes(type);
    const input = <HTMLInputElement>field.querySelector(ExtraPropertyFormMap.constraint.optionInput);
    input.type = isNumber ? 'number' : 'text';
    input.disabled = this.readOnly;
    input.value = isString ? unquoteValue(option.value) : option.value;

    if (labelText !== null) {
      input.setAttribute('aria-label', labelText);
    }

    if (!isNumber && !isString) {
      input.classList.add('text-monospace');
    }

    input.addEventListener('input', () => {
      const raw = input.value.trim();
      /* eslint-disable no-param-reassign */
      option.value = isString && raw !== '' ? quoteValue(input.value) : raw;
      /* eslint-enable no-param-reassign */
      writeTail(options);
    });

    const remove = <HTMLButtonElement>field.querySelector(ExtraPropertyFormMap.constraint.optionRemove);

    if (this.readOnly) {
      remove.remove();
    } else {
      remove.setAttribute('aria-label', `${remove.getAttribute('aria-label') ?? ''} ${labelText ?? ''}`.trim());
      remove.addEventListener('click', () => {
        options.splice(options.indexOf(option), 1);
        writeTail(options);
        rerender();
      });
    }

    return field;
  }

  private rawTailInput(editor: HTMLElement, optionsInput: HTMLInputElement, template: 'option-raw' | 'option-nested'): void {
    const input = <HTMLInputElement>cloneTemplate(template);
    input.value = optionsInput.value;
    input.disabled = this.readOnly;
    input.addEventListener('input', () => {
      /* eslint-disable no-param-reassign */
      optionsInput.value = input.value;
      /* eslint-enable no-param-reassign */
      optionsInput.dispatchEvent(new Event('input', {bubbles: true}));
    });
    editor.appendChild(input);
  }

  private nameSuggestions(): SuggestionItem[] {
    return groupConstraintNames(Object.keys(this.catalog)).flatMap(({group, names}) => names.map((name) => ({
      value: name,
      label: name,
      detail: Object.keys(this.catalog[name]?.options ?? {}).slice(0, 3).join(', '),
      group,
    })));
  }

  // ── Chrome ────────────────────────────────────────────────────────────────────────────────

  private updateChrome(): void {
    const count = this.allRows().length;
    const badge = this.container.querySelector<HTMLElement>(ExtraPropertyFormMap.subsection.countBadge);
    const emptyState = this.container.querySelector<HTMLElement>(ExtraPropertyFormMap.subsection.emptyState);

    if (badge) {
      badge.textContent = String(count);
      badge.classList.toggle('d-none', count === 0);
    }

    emptyState?.classList.toggle('d-none', count > 0);
  }

  /**
   * The zone shows for per-language scope or whenever it still holds rows; leaving the
   * per-language scope keeps the rows (inactive styling), never destroys them.
   */
  private updateZone(): void {
    const hasLangRows = this.langRowsContainer.querySelector(ExtraPropertyFormMap.subsection.row) !== null;
    const isLangScope = (this.scopeSelect?.value ?? '') === 'lang';

    this.zone.classList.toggle('d-none', !isLangScope && !hasLangRows);
    this.zone.classList.toggle(ExtraPropertyFormMap.constraint.inactiveZoneClass, hasLangRows && !isLangScope);
  }
}
