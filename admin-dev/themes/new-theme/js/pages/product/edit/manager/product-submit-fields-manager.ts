/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

export default class ProductSubmitFieldsManager {
  private readonly $productForm: JQuery;

  constructor($productForm: JQuery) {
    this.$productForm = $productForm;
    this.init();
  }

  private init(): void {
    this.$productForm.on('submit', () => {
      const formEl = this.$productForm.get(0);

      if (!formEl) {
        return;
      }

      const fields = formEl.querySelectorAll<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>(
        'input[data-reenable-on-submit], select[data-reenable-on-submit], textarea[data-reenable-on-submit]',
      );

      fields.forEach((field) => {
        if (field.disabled) {
          field.toggleAttribute('disabled', false);
        }
      });
    });
  }
}
