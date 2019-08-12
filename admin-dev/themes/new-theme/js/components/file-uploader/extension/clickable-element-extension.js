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

export default class ClickableElementExtension {
  /**
   * @param {DraggableFileUploader} fileUploader
   */
  extend(fileUploader) {
    const selector = '.js-clickable-file-uploader';

    const hasClickable = fileUploader.getElementInstance().find(selector).length > 0;

    if (!hasClickable) {
      throw new Error(`Missing element "${selector}" in your draggable file uploader container`);
    }

    fileUploader.getDropzoneInstance().on('thumbnail', () => {
      this.toggle(fileUploader.getElementInstance());
    });

    fileUploader.getDropzoneInstance().on('removedfile', () => {
      this.toggle(fileUploader.getElementInstance());
    });
  }

  toggle($element) {
    const containsImages = $element.find('img').length > 0;
    $element.toggleClass('dz-max-files-reached', containsImages);
    $element.find('.js-clickable-file-uploader').toggleClass('d-none', !containsImages);
  }
}
