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

export default class DraggableFileUploader {
  constructor(elementSelector, optionalProperties) {
    const $element = $(elementSelector);

    const withDefaultProperties = this.getWithDefaultProperties(optionalProperties);

    const properties = Object.assign(
      {},
      this.getRequiredProperties($element),
      withDefaultProperties);

    const dropzone = new Dropzone(elementSelector, properties);

    this._$fileUploader = dropzone;
    this._$element = $element;
  }

  getDoesContainImages($element) {
    return $element.find('img').length > 0;
  }

  getDropzoneInstance() {
    return this._$fileUploader;
  }

  getElementInstance() {
    return this._$element;
  }

  addExtension(extension) {
    extension.extend(this);
  }

  /**
   *
   * @param $element
   * @return {{acceptedFiles: *, maxFilesize: *, paramName: *, url: *}}
   * @private
   */
  getRequiredProperties($element) {
    return {
      url: $element.data('url'),
      maxFilesize: $element.data('maxFileSize'),
      acceptedFiles: $element.data('acceptedFiles'),
      paramName: $element.data('paramName'),
    };
  }

  /**
   *
   * @param properties
   * @return {any}
   * @private
   */
  getWithDefaultProperties(properties) {
    const defaultProperties = {
      thumbnailWidth: 250,
      addRemoveLinks: true,
      thumbnailHeight: null,
      clickable: '.js-clickable-file-uploader',
    };

    return Object.assign({}, defaultProperties, properties);
  }
}
