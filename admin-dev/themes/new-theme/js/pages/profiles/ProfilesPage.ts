/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * Responsible for actions in Profiles listing page.
 */
export default class ProfilesPage {
  constructor() {
    const profilesGrid = new window.prestashop.component.Grid('profile');

    profilesGrid.addExtension(new window.prestashop.component.GridExtensions.ReloadListExtension());
    profilesGrid.addExtension(new window.prestashop.component.GridExtensions.ExportToSqlManagerExtension());
    profilesGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersResetExtension());
    profilesGrid.addExtension(new window.prestashop.component.GridExtensions.SortingExtension());
    profilesGrid.addExtension(new window.prestashop.component.GridExtensions.LinkRowActionExtension());
    profilesGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitGridActionExtension());
    profilesGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitBulkActionExtension());
    profilesGrid.addExtension(new window.prestashop.component.GridExtensions.BulkActionCheckboxExtension());
    profilesGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitRowActionExtension());
    profilesGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersSubmitButtonEnablerExtension());

    window.prestashop.component.initComponents(
      [
        'TranslatableInput',
      ],
    );
  }
}
