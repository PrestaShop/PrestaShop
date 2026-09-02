/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

interface StateData {
  id_state: string;
  name: string;
}

/**
 * Dynamically loads states for the selected country in the tax rule form.
 * Reads the states API URL from data-states-url on the form element.
 */
export default class CountryStateSelector {
  private readonly countrySelect: HTMLSelectElement;

  private readonly stateSelect: HTMLSelectElement;

  private readonly stateFormGroup: HTMLElement;

  private readonly statesUrlTemplate: string;

  private initialStateValue: string | null;

  constructor(formSelector: string = 'form[data-states-url]') {
    const form = document.querySelector<HTMLFormElement>(formSelector);

    if (!form) {
      throw new Error(`CountryStateSelector: form "${formSelector}" not found`);
    }

    this.statesUrlTemplate = form.dataset.statesUrl || '';

    const countrySelect = form.querySelector<HTMLSelectElement>('[name$="[country]"]');
    const stateSelect = form.querySelector<HTMLSelectElement>('[name$="[state]"]');

    if (!countrySelect || !stateSelect) {
      throw new Error('CountryStateSelector: country or state select not found');
    }

    this.countrySelect = countrySelect;
    this.stateSelect = stateSelect;
    this.stateFormGroup = stateSelect.closest('.form-group') as HTMLElement;
    this.initialStateValue = stateSelect.value || null;

    this.init();
  }

  private init(): void {
    this.countrySelect.addEventListener('change', () => {
      this.loadStates(this.countrySelect.value);
    });

    if (this.countrySelect.value && this.countrySelect.value !== '0') {
      this.loadStates(this.countrySelect.value);
    } else {
      this.toggleStateVisibility(false);
    }
  }

  private async loadStates(countryId: string): Promise<void> {
    while (this.stateSelect.options.length > 1) {
      this.stateSelect.remove(1);
    }

    if (!countryId || countryId === '0') {
      this.toggleStateVisibility(false);

      return;
    }

    const url = this.statesUrlTemplate.replace('/0', `/${countryId}`);

    try {
      const response = await fetch(url, {
        headers: {'X-Requested-With': 'XMLHttpRequest'},
      });
      const states: StateData[] = await response.json();

      if (states.length === 0) {
        this.toggleStateVisibility(false);

        return;
      }

      this.toggleStateVisibility(true);

      states.forEach((state: StateData) => {
        const option = document.createElement('option');
        option.value = state.id_state;
        option.textContent = state.name;

        if (this.initialStateValue && String(state.id_state) === String(this.initialStateValue)) {
          option.selected = true;
        }

        this.stateSelect.appendChild(option);
      });

      this.initialStateValue = null;
    } catch {
      this.toggleStateVisibility(false);
    }
  }

  private toggleStateVisibility(visible: boolean): void {
    if (this.stateFormGroup) {
      this.stateFormGroup.style.display = visible ? '' : 'none';
    }
  }
}
