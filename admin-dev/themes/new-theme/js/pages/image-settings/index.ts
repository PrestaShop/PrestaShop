/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import DeleteImageTypeRowActionExtension
  from '@components/grid/extension/action/row/image_type/delete-image-type-row-action-extension';
import ConfirmModal from '@components/modal/confirm-modal';

const {$} = window;

$(() => {
  // Init image type grid
  const grid = new window.prestashop.component.Grid('image_type');
  grid.addExtension(new window.prestashop.component.GridExtensions.FiltersResetExtension());
  grid.addExtension(new window.prestashop.component.GridExtensions.ReloadListExtension());
  grid.addExtension(new window.prestashop.component.GridExtensions.ExportToSqlManagerExtension());
  grid.addExtension(new window.prestashop.component.GridExtensions.SortingExtension());
  grid.addExtension(new window.prestashop.component.GridExtensions.LinkRowActionExtension());
  grid.addExtension(new window.prestashop.component.GridExtensions.SubmitBulkActionExtension());
  grid.addExtension(new window.prestashop.component.GridExtensions.BulkActionCheckboxExtension());
  grid.addExtension(new window.prestashop.component.GridExtensions.FiltersSubmitButtonEnablerExtension());
  grid.addExtension(new window.prestashop.component.GridExtensions.ChoiceExtension());
  grid.addExtension(new window.prestashop.component.GridExtensions.ColumnTogglingExtension());
  grid.addExtension(new window.prestashop.component.GridExtensions.SubmitRowActionExtension());
  grid.addExtension(new DeleteImageTypeRowActionExtension());

  // Regenerate thumbnails system
  const $regenerateThumbnailsForm = $('form[name=regenerate_thumbnails]');
  const $regenerateThumbnailsButton = $('#regenerate-thumbnails-button');
  const $selectImage = $('#regenerate_thumbnails_image');
  const $selectImageType = $('#regenerate_thumbnails_image-type');
  const $parentImageFormat = $selectImageType.parents('.form-group');
  const formatsByTypes = $selectImage.data('formats');

  // First hide the image format select
  $parentImageFormat.hide();

  // On image type change, show the image format by the type selected
  $selectImage.on('change', () => {
    const selectedImage: string = ($selectImage.val() ?? 'all').toString();

    // Reset format selector
    $selectImageType.val(0);
    $selectImageType.children('option').hide();

    // If all is selected, hide the format selector
    if (selectedImage === 'all') {
      $parentImageFormat.hide();
    } else {
      // Else show the format selector...
      $parentImageFormat.show();
      // and the formats by the type selected
      formatsByTypes[selectedImage].forEach((formatId: number) => {
        $selectImageType.children(`option[value="${formatId}"]`).show();
      });
      // Don't forget to show the "all" option
      $selectImageType.children('option[value="0"]').show();
    }
  });

  // On submit regenerate thumbnails form, show a confirmation modal.
  $regenerateThumbnailsButton.on('click', (event) => {
    event.preventDefault();

    // Display confirmation modal
    const modal = new (ConfirmModal as any)(
      {
        id: 'regeneration-confirm-modal',
        confirmTitle: $regenerateThumbnailsButton.data('confirm-title'),
        confirmMessage: $regenerateThumbnailsButton.data('confirm-message'),
        closeButtonLabel: $regenerateThumbnailsButton.data('confirm-cancel'),
        confirmButtonLabel: $regenerateThumbnailsButton.data('confirm-apply'),
        closable: true,
      },
      () => {
        // If ok, submit the form
        $regenerateThumbnailsForm.submit();
      },
    );
    modal.show();
  });
});
