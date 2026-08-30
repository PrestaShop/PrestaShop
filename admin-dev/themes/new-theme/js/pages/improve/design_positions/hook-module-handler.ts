/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

const {$} = window;

interface HookableInfo {
  id: number;
  name: string;
  title: string;
  registered: boolean;
}

/**
 * Handles dynamic hook selector on the "Hook a module" form.
 * When the module select changes, fetches possible hooks via AJAX and
 * repopulates the hook dropdown.
 */
export default class HookModuleHandler {
  private readonly moduleSelector: HTMLSelectElement | null | undefined;

  private readonly hookSelector: HTMLSelectElement | null | undefined;

  private readonly hookUrl: string | null | undefined;

  private readonly availableLabel: string | undefined;

  private readonly registeredLabel: string | undefined;

  // Shared select2 config so both selectors match the rest of the BO
  // (bootstrap4 theme) and always expose the search box, even for short lists.
  private readonly select2Options = {
    theme: 'bootstrap4',
    minimumResultsForSearch: 0,
    width: '100%',
  };

  constructor() {
    const form = document.querySelector<HTMLFormElement>('[data-hook-url]');

    if (!form) {
      return;
    }

    this.hookUrl = form.dataset.hookUrl ?? null;
    this.availableLabel = form.dataset.labelAvailable ?? 'Available hooks';
    this.registeredLabel = form.dataset.labelRegistered ?? 'Already registered hooks';
    this.moduleSelector = form.querySelector<HTMLSelectElement>('[data-module-selector="true"]');
    this.hookSelector = form.querySelector<HTMLSelectElement>('[data-hook-selector="true"]');

    if (!this.moduleSelector || !this.hookSelector || !this.hookUrl) {
      return;
    }

    // Enhance both selects with select2 (search helper). The hook select keeps
    // its initial disabled state until a module is chosen.
    $(this.moduleSelector).select2(this.select2Options);
    this.refreshHookSelect2();

    // select2 emits a jQuery "change" event, which a native addEventListener
    // does not reliably catch — bind through jQuery instead.
    $(this.moduleSelector).on('change', () => this.onModuleChange());
  }

  private async onModuleChange(): Promise<void> {
    if (!this.moduleSelector || !this.hookSelector || !this.hookUrl) {
      return;
    }

    const moduleId = Number(this.moduleSelector.value);

    if (!moduleId) {
      this.clearHookSelector();

      return;
    }

    try {
      const response = await fetch(this.hookUrl, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({module_id: String(moduleId)}),
      });

      const json = await response.json();

      if (json.hasError || !Array.isArray(json.hooks)) {
        this.clearHookSelector();

        return;
      }

      this.populateHookSelector(json.hooks as HookableInfo[]);
    } catch {
      this.clearHookSelector();
    }
  }

  private populateHookSelector(hooks: HookableInfo[]): void {
    if (!this.hookSelector) {
      return;
    }

    const currentValue = this.hookSelector.value;
    this.clearHookSelector();

    // Sort by technical name (case-insensitive) so each group is alphabetical,
    // matching the order the option label leads with.
    const byName = (a: HookableInfo, b: HookableInfo): number => a.name.localeCompare(b.name, undefined, {sensitivity: 'base'});

    const available = hooks.filter((hook) => !hook.registered).sort(byName);
    const registered = hooks.filter((hook) => hook.registered).sort(byName);

    const buildOption = (hook: HookableInfo): HTMLOptionElement => {
      const option = document.createElement('option');
      option.value = String(hook.id);
      option.text = hook.title ? `${hook.name} (${hook.title})` : hook.name;

      if (String(hook.id) === currentValue) {
        option.selected = true;
      }

      return option;
    };

    if (available.length > 0) {
      const availableGroup = document.createElement('optgroup');

      if (this.availableLabel != null) {
        availableGroup.label = this.availableLabel;
      }
      available.forEach((hook) => availableGroup.appendChild(buildOption(hook)));
      this.hookSelector.appendChild(availableGroup);
    }

    if (registered.length > 0) {
      const registeredGroup = document.createElement('optgroup');

      if (this.registeredLabel != null) {
        registeredGroup.label = this.registeredLabel;
      }
      registeredGroup.disabled = true;
      registered.forEach((hook) => registeredGroup.appendChild(buildOption(hook)));
      this.hookSelector.appendChild(registeredGroup);
    }

    // Re-enable the selector now that choices are available.
    this.setHookSelectorEnabled(true);
  }

  private clearHookSelector(): void {
    if (!this.hookSelector) {
      return;
    }

    // Keep only the placeholder option (the first direct <option> child if any)
    const placeholder = this.hookSelector.querySelector<HTMLOptionElement>(':scope > option');
    this.hookSelector.innerHTML = '';
    if (placeholder) {
      this.hookSelector.appendChild(placeholder);
    }

    // Without choices the selector is unusable — disable it.
    this.setHookSelectorEnabled(false);
  }

  /**
   * Toggles the disabled state of the hook selector.
   * The Symfony form theme renders the field disabled by adding a `disabled`
   * CSS class on both the wrapping `.input-container` and the label. Toggling
   * the `disabled` property on the <select> alone leaves those classes behind,
   * which keeps the field visually greyed out — so they must be synced too.
   */
  private setHookSelectorEnabled(enabled: boolean): void {
    if (!this.hookSelector) {
      return;
    }

    this.hookSelector.disabled = !enabled;

    const container = this.hookSelector.closest<HTMLElement>('.input-container');
    container?.classList.toggle('disabled', !enabled);

    const formGroup = this.hookSelector.closest<HTMLElement>('.form-group');
    formGroup
      ?.querySelector<HTMLElement>('.form-control-label')
      ?.classList.toggle('disabled', !enabled);

    // The options and the disabled state were changed programmatically: rebuild
    // select2 so the widget reflects the new choices and enabled/disabled state.
    this.refreshHookSelect2();
  }

  /**
   * (Re)initializes select2 on the hook selector. select2 caches the original
   * options at init time, so after repopulating or toggling the field we must
   * destroy and recreate it for the widget to pick up the changes.
   */
  private refreshHookSelect2(): void {
    if (!this.hookSelector) {
      return;
    }

    const $hookSelector = $(this.hookSelector);

    if ($hookSelector.hasClass('select2-hidden-accessible')) {
      $hookSelector.select2('destroy');
    }
    $hookSelector.select2(this.select2Options);
  }
}
