/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * Both grids (violations csp_log, allow-list csp_rule) need the client-side extensions so their row and
 * bulk actions show the confirm dialog, plus refresh/sort/filter behaviour.
 */
const initCspGrid = (gridId: string): void => {
  const grid = new window.prestashop.component.Grid(gridId);

  grid.addExtension(new window.prestashop.component.GridExtensions.ReloadListExtension());
  grid.addExtension(new window.prestashop.component.GridExtensions.FiltersResetExtension());
  grid.addExtension(new window.prestashop.component.GridExtensions.SortingExtension());
  grid.addExtension(new window.prestashop.component.GridExtensions.FiltersSubmitButtonEnablerExtension());
  grid.addExtension(new window.prestashop.component.GridExtensions.LinkRowActionExtension());
  grid.addExtension(new window.prestashop.component.GridExtensions.SubmitRowActionExtension());
  grid.addExtension(new window.prestashop.component.GridExtensions.SubmitGridActionExtension());
  grid.addExtension(new window.prestashop.component.GridExtensions.BulkActionCheckboxExtension());
  grid.addExtension(new window.prestashop.component.GridExtensions.SubmitBulkActionExtension());
};

/**
 * Everything below "Enable CSP" is dormant while CSP is off (no header is sent, and the prune command
 * skips disabled surfaces), so dim and lock those fields when it is off. Locked via pointer-events/readonly,
 * not `disabled`: a disabled control is not submitted and would wipe the stored value on save.
 */
const initEnabledDependency = (): void => {
  const $form = $('#configuration_fieldset_csp');

  if ($form.length === 0) {
    return;
  }

  const dependentFields = ['report_only', 'report_uri', 'retention_days'];

  const sync = (): void => {
    const enabledOn = $form.find('input[name$="[enabled]"][value="1"]').is(':checked');

    dependentFields.forEach((field) => {
      const $inputs = $form.find(`[name$="[${field}]"]`);
      $inputs.closest('.form-group').css('opacity', enabledOn ? '' : '0.5');

      const $switch = $inputs.closest('.ps-switch');

      if ($switch.length) {
        $switch.css('pointer-events', enabledOn ? '' : 'none');
      } else {
        $inputs.prop('readonly', !enabledOn);
      }
    });
  };

  $form.on('change', 'input[name$="[enabled]"]', sync);
  sync();
};

$(() => {
  initCspGrid('csp_log');
  initCspGrid('csp_rule');
  initEnabledDependency();
});
