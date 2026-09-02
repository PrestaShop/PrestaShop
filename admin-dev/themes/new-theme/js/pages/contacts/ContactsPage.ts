/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * Responsible for actions in Contacts listing page.
 */
export default class ContactsPage {
  constructor() {
    const contactGrid = new window.prestashop.component.Grid('contact');

    contactGrid.addExtension(new window.prestashop.component.GridExtensions.ReloadListExtension());
    contactGrid.addExtension(new window.prestashop.component.GridExtensions.ExportToSqlManagerExtension());
    contactGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersResetExtension());
    contactGrid.addExtension(new window.prestashop.component.GridExtensions.SortingExtension());
    contactGrid.addExtension(new window.prestashop.component.GridExtensions.LinkRowActionExtension());
    contactGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitGridActionExtension());
    contactGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitBulkActionExtension());
    contactGrid.addExtension(new window.prestashop.component.GridExtensions.BulkActionCheckboxExtension());
    contactGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitRowActionExtension());

    window.prestashop.component.initComponents(
      [
        'TranslatableInput',
      ],
    );

    new window.prestashop.component.ChoiceTree('#contact_shop_association').enableAutoCheckChildren();
  }
}
