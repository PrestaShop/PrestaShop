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

/**
 *
 * @param {jQuery} $element - a jquery element which is being loaded with plugin
 * @param {string} url - an url which is being called for file upload
 * @param {number} maxFilesize - adds maximum filesize for single file
 * @param {string} acceptedFiles - mime types of available types. E.g image/* allows all image formats.
 * @param {object} optionalProperties - other properties which can be used with the dropzone plugin
 * @param {string} paramName - a file name which is being sent to the server
 *
 * @return jQuery
 */

/**
 * @param {object} properties
 * @return {object}
 */
const getDefaultProperties = (properties) => {
  const defaultProperties = {
    thumbnailWidth: 250,
  };

  return Object.assign({}, defaultProperties, properties);
};

const fileUploader = ({$element, url, maxFilesize, acceptedFiles, optionalProperties, paramName = 'file'}) => {
  const properties = Object.assign(
    {},
    {url, paramName, maxFilesize, acceptedFiles},
    optionalProperties);

  const withDefaultProperties = getDefaultProperties(properties);

  return $element.dropzone(withDefaultProperties);
};

export default fileUploader;
