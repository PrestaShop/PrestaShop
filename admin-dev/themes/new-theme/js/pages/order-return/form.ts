/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * Merchandise return edit page — deferred deletion of returned products (Issue #27628).
 *
 * When the merchant clicks the per-row "Delete" button we open a confirmation modal but
 * do NOT call the back end. Instead the row is visually marked as staged-for-deletion and
 * the row's (id_order_detail, id_customization) pair is appended to a hidden JSON field.
 * The actual removal happens server-side when the form is submitted via the main "Save"
 * button, processed by OrderReturnFormDataHandler.
 */

type StagedRow = {
  order_detail_id: number;
  customization_id: number;
};

const SELECTORS = {
  deleteButton: '[data-role="order-return-delete-product"]',
  productRow: '[data-role="order-return-product-row"]',
  stagedField: '[data-role="order-return-staged-deletions"]',
  modal: '#orderReturnDeleteProductModal',
  modalConfirmButton: '[data-role="order-return-confirm-delete-product"]',
} as const;

const STAGED_ROW_CSS_CLASS = 'order-return-row-staged-for-deletion';

class OrderReturnDeletionStager {
  private readonly $form: JQuery;

  private readonly $modal: JQuery;

  private $pendingRow: JQuery | null = null;

  constructor($form: JQuery) {
    this.$form = $form;
    this.$modal = $(SELECTORS.modal);
    this.bind();
  }

  private bind(): void {
    this.$form.on('click', SELECTORS.deleteButton, (event) => {
      event.preventDefault();
      this.$pendingRow = $(event.currentTarget).closest(SELECTORS.productRow);
      this.$modal.modal('show');
    });

    this.$modal.on('click', SELECTORS.modalConfirmButton, () => {
      if (this.$pendingRow !== null) {
        this.stageRow(this.$pendingRow);
        this.$pendingRow = null;
      }
      this.$modal.modal('hide');
    });

    this.$modal.on('hidden.bs.modal', () => {
      this.$pendingRow = null;
    });
  }

  private stageRow($row: JQuery): void {
    const orderDetailId = parseInt($row.data('order-detail-id'), 10);
    const customizationId = parseInt($row.data('customization-id'), 10);

    if (Number.isNaN(orderDetailId) || orderDetailId <= 0) {
      return;
    }

    // Visual feedback — strike-through + dim, plus disable any further interaction in the row.
    $row.addClass(STAGED_ROW_CSS_CLASS);
    $row.find(SELECTORS.deleteButton).attr('disabled', 'disabled');

    // Update the hidden JSON payload that OrderReturnFormDataHandler reads on submit.
    const $stagedField = this.$form.find(SELECTORS.stagedField);
    const existing: StagedRow[] = OrderReturnDeletionStager.parseField($stagedField.val());

    if (!existing.some((row) => row.order_detail_id === orderDetailId
        && row.customization_id === (Number.isNaN(customizationId) ? 0 : customizationId))) {
      existing.push({
        order_detail_id: orderDetailId,
        customization_id: Number.isNaN(customizationId) ? 0 : customizationId,
      });
    }
    $stagedField.val(JSON.stringify(existing));
  }

  private static parseField(raw: unknown): StagedRow[] {
    if (typeof raw !== 'string' || raw === '') {
      return [];
    }
    try {
      const decoded = JSON.parse(raw);

      return Array.isArray(decoded) ? decoded as StagedRow[] : [];
    } catch (_error) {
      return [];
    }
  }
}

$(() => {
  const $form = $(SELECTORS.stagedField).closest('form');

  if ($form.length === 0) {
    return;
  }
  // eslint-disable-next-line no-new
  new OrderReturnDeletionStager($form);
});
