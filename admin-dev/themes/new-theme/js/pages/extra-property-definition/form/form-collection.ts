/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import ExtraPropertyFormMap from '@pages/extra-property-definition/form/extra-property-form-map';

/**
 * Minimal prototype-based Symfony collection driver: adds/removes rows against the standard
 * data-prototype attribute rendered by the collection widget. Rows are the MAPPED form data —
 * whatever the inputs carry on submit is what the data handler serializes back into the stored
 * entries.
 */
export default class FormCollection {
  private container: HTMLElement;

  private prototypeHtml: string;

  private prototypeName: string;

  private index: number;

  constructor(container: HTMLElement) {
    this.container = container;
    this.prototypeHtml = container.dataset.prototype ?? '';
    this.prototypeName = container.dataset.prototypeName ?? '__name__';
    this.index = parseInt(container.dataset.rowIndex ?? '0', 10) || 0;
  }

  rows(): HTMLElement[] {
    return Array.from(this.container.querySelectorAll<HTMLElement>(ExtraPropertyFormMap.subsection.row));
  }

  add(values: Record<string, string>, target?: HTMLElement): HTMLElement {
    const template = document.createElement('template');
    template.innerHTML = this.prototypeHtml.split(this.prototypeName).join(String(this.index)).trim();
    this.index += 1;

    const row = <HTMLElement>template.content.firstElementChild;
    FormCollection.fill(row, values);
    (target ?? this.container).appendChild(row);

    return row;
  }

  static remove(row: HTMLElement): void {
    row.remove();
  }

  clear(): void {
    this.rows().forEach((row) => FormCollection.remove(row));
  }

  /**
   * Sets row field values by their Symfony field name suffix ("...[grid_id]").
   */
  static fill(row: HTMLElement, values: Record<string, string>): void {
    Object.entries(values).forEach(([field, value]) => {
      const input = row.querySelector<HTMLInputElement | HTMLSelectElement>(ExtraPropertyFormMap.rowField(field));

      if (input) {
        input.value = value;
      }
    });
  }

  /**
   * Reads row field values back, "" for a missing/blank input.
   */
  static read(row: HTMLElement, fields: string[]): Record<string, string> {
    const values: Record<string, string> = {};

    fields.forEach((field) => {
      const input = row.querySelector<HTMLInputElement | HTMLSelectElement>(ExtraPropertyFormMap.rowField(field));
      values[field] = input ? input.value.trim() : '';
    });

    return values;
  }
}
