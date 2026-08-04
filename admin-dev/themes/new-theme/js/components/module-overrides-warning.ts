/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

const {$} = window;

const moduleItemSelector = '.module-item-list';

export interface ModuleOverrides {
  displayName: string;
  files: string[];
}

/**
 * Reads the override data exposed by the module card the given element belongs to,
 * or null when the module is not overridden.
 */
export function getModuleOverrides(element: JQuery): ModuleOverrides | null {
  const moduleItem = element.closest(moduleItemSelector);

  if (!moduleItem.data('has-overrides')) {
    return null;
  }

  return {
    displayName: moduleItem.data('name') || moduleItem.data('tech-name'),
    files: String(moduleItem.data('overridden-files') ?? '')
      .split('|')
      .filter((file) => file !== ''),
  };
}

/**
 * Warning displayed before updating a single module customized by override files:
 * the update may silently break them.
 */
export function getModuleOverridesWarning(element: JQuery): string {
  const overrides = getModuleOverrides(element);

  if (overrides === null) {
    return '';
  }

  const {overridesUpdateWarning, overridesUpdateFilesIntro} = window.moduleTranslations;

  return buildWarning(overridesUpdateWarning, overridesUpdateFilesIntro, overrides.files);
}

/**
 * Same warning for a bulk update, listing every overridden module of the selection.
 */
export function getModulesOverridesWarning(elements: JQuery): string {
  const overriddenModules: string[] = [];

  elements.each((index: number, element: HTMLElement) => {
    const overrides = getModuleOverrides($(element));

    if (overrides !== null && !overriddenModules.includes(overrides.displayName)) {
      overriddenModules.push(overrides.displayName);
    }
  });

  if (overriddenModules.length === 0) {
    return '';
  }

  const {overridesUpdateAllWarning, overridesUpdateModulesIntro} = window.moduleTranslations;

  return buildWarning(overridesUpdateAllWarning, overridesUpdateModulesIntro, overriddenModules);
}

function buildWarning(message: string, listIntro: string, items: string[]): string {
  const list = items.length
    ? `${escapeHtml(listIntro)}<ul>${items.map((item) => `<li>${escapeHtml(item)}</li>`).join('')}</ul>`
    : '';

  return `<div class="alert alert-warning module-overrides-warning" role="alert">
    <p class="alert-text">${escapeHtml(message)}</p>
    ${list}
  </div>`;
}

/**
 * Override file paths and module names are injected through innerHTML, they come from the
 * filesystem and from module manifests, so they must not be trusted as markup.
 */
function escapeHtml(value: string): string {
  const container = document.createElement('div');
  container.textContent = value;

  return container.innerHTML;
}
