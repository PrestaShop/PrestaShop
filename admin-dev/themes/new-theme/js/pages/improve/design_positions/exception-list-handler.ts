/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * Handles bidirectional synchronisation between the "exceptions" text field
 * (comma-separated filenames) and the adjacent multi-select listing all
 * available front-controllers / module controllers, mirroring the legacy
 * AdminModulesPositionsController::displayModuleExceptionList behaviour.
 */
export default class ExceptionListHandler {
  private readonly textField: HTMLInputElement | null = null;

  private readonly listField: HTMLSelectElement | null = null;

  private readonly customGroup: HTMLOptGroupElement | null = null;

  constructor() {
    const form = document.querySelector<HTMLFormElement>('[data-hook-url]');

    if (!form) {
      return;
    }

    this.textField = form.querySelector<HTMLInputElement>('input[name$="[exceptions]"]');
    this.listField = form.querySelector<HTMLSelectElement>('[data-exception-list="true"]');

    if (!this.textField || !this.listField) {
      return;
    }

    this.customGroup = this.listField.querySelector<HTMLOptGroupElement>('[data-exception-group="custom"]');

    this.syncTextToList();
    this.textField.addEventListener('change', () => this.syncTextToList());
    this.listField.addEventListener('change', () => this.syncListToText());
    // Force a final sync at submit time — guarantees the text field carries the current
    // multi-select state even if the user clicks Save without ever firing a `change` event
    // on the list (e.g. browsers that defer change on <select multiple> until blur).
    form.addEventListener('submit', () => this.syncListToText());
  }

  private parseTextValues(): string[] {
    if (!this.textField) {
      return [];
    }

    return this.textField.value
      .split(',')
      .map((value) => value.trim())
      .filter((value) => value !== '');
  }

  private knownValues(): Set<string> {
    if (!this.listField) {
      return new Set();
    }

    const values = new Set<string>();
    this.listField
      .querySelectorAll<HTMLOptionElement>('optgroup:not([data-exception-group="custom"]) > option')
      .forEach((option) => values.add(option.value));

    return values;
  }

  private syncTextToList(): void {
    if (!this.listField) {
      return;
    }

    const values = this.parseTextValues();
    const known = this.knownValues();

    if (this.customGroup) {
      this.customGroup.innerHTML = '';
      values
        .filter((value) => !known.has(value))
        .forEach((value) => {
          const option = document.createElement('option');
          option.value = value;
          option.text = value;
          this.customGroup!.appendChild(option);
        });
    }

    const selected = new Set(values);
    this.listField.querySelectorAll<HTMLOptionElement>('option').forEach((option) => {
      // eslint-disable-next-line no-param-reassign
      option.selected = selected.has(option.value);
    });
  }

  private syncListToText(): void {
    if (!this.textField || !this.listField) {
      return;
    }

    const selected: string[] = [];
    this.listField.querySelectorAll<HTMLOptionElement>('option:checked').forEach((option) => {
      if (option.value) {
        selected.push(option.value);
      }
    });

    this.textField.value = selected.join(', ');
  }
}
