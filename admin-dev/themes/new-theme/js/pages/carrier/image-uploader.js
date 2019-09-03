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
export default class ImageUploader {
  constructor(
    imageUploadBlock,
    imageTarget,
    formWrapper,
    imageRemovalBtn,
  ) {
    this.$imageUploadBlock = $(imageUploadBlock);
    this.$imageTarget = $(imageTarget);
    this.$removalBtn = $(imageRemovalBtn);
    this.form = document.querySelector(`${formWrapper} form`);

    this.handle();

    return {};
  }

  /**
   * Initiates the handler
   */
  handle() {
    $(this.$imageUploadBlock.find('input[type="file"]')).on('change', (e) => {
      if (e.target.files.length !== 0) {
        this.uploadImage();
      }
    });
  }

  /**
   * Uploads the temporary image to file system
   */
  uploadImage() {
    $.ajax({
      url: this.$imageUploadBlock.data('upload-image-url'),
      method: 'POST',
      processData: false,
      contentType: false,
      context: this,
      dataType: 'json',
      data: new FormData(this.form),
    }).then((response) => {
      this.presentImage(response.img_path);
      this.showRemovalBtn();
      this.logImgName(response.img_path);
    }).catch((e) => {
      showErrorMessage(e.responseJSON.message);
    });
  }

  /**
   * Presents the image in browser
   *
   * @param imagePath
   */
  presentImage(imagePath) {
    this.$imageTarget.prop('src', imagePath);
    $(this.$imageUploadBlock).find('input[type="hidden"]').val(imagePath);
  }

  /**
   * Shows button which is responsible for logo removal
   */
  showRemovalBtn() {
    this.$removalBtn.show();
  }

  /**
   * Saves image name in browser
   *
   * @param imgPath
   */
  logImgName(imgPath) {
    const fileName = imgPath.substr(imgPath.lastIndexOf('/') + 1);
    $(this.$imageUploadBlock).find('input[type="hidden"]').val(fileName);
  }
}

