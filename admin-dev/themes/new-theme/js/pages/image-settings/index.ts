/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
