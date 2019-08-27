/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

const $ = window.$;

/**
 * Responsible uploading and showing carrier temporary image
 */
export default class ImageRemover {
  constructor(
    imageUploadBlock,
    imageTarget,
    removalBtn,
  ) {
    this.$imageUploadBlock = $(imageUploadBlock);
    this.$imageTarget = $(imageTarget);
    this.$removalBtn = $(removalBtn);

    this.handle();

    return {};
  }

  /**
   * Initiates the handler
   */
  handle() {
    const isTmp = this.$removalBtn.data('is-tmp-image') === true;
    this.removeImgOnPageReload();
    this.$removalBtn.on('click', () => {
      this.removeImage(isTmp);
    })
  }

  removeImage(isTmp) {
    const src = this.$imageTarget.attr('src');
    const imgName = src.substring(src.lastIndexOf('/') + 1);

    $.ajax({
      url: this.$removalBtn.data('remove-image-url'),
      method: 'POST',
      processData: false,
      contentType: 'application/json; charset=utf-8',
      context: this,
      dataType: 'json',
      data: JSON.stringify({
        image_name: imgName,
        is_tmp_image: isTmp,
      })
    }).then((response) => {
      this.clearImagePresentation(response.file_label);
      this.hideRemoveBtn();
    }).catch((e) => {
      showErrorMessage(e.responseJSON.message);
    });
  }

  clearImagePresentation(emptyFileLabel) {
    this.$imageTarget.attr('src', this.$imageTarget.data('default-logo'));
    this.$imageUploadBlock.find('input[type="file"]').parent().find('label').text(emptyFileLabel);
    this.$imageUploadBlock.find('input[type="hidden"]').val('');
  }

  hideRemoveBtn() {
    this.$removalBtn.hide();
  }

  removeImgOnPageReload() {
    window.onbeforeunload = () => {
      const prevImg = this.$imageUploadBlock.find('input[type="hidden"]').val();
      if (prevImg !== null && prevImg !== '') {
        this.removeImage(true);
      }
    };
  }
}

