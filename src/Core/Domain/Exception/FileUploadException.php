<?php
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

namespace PrestaShop\PrestaShop\Core\Domain\Exception;

/**
 * This exception on success case has code 0 which is equal to constant UPLOAD_ERR_OK.
 * On every other case it has one of the following error code constants:
 *
 * UPLOAD_ERR_INI_SIZE,
 * UPLOAD_ERR_FORM_SIZE,
 * UPLOAD_ERR_PARTIAL,
 * UPLOAD_ERR_NO_FILE,
 * UPLOAD_ERR_NO_TMP_DIR,
 * UPLOAD_ERR_CANT_WRITE,
 * UPLOAD_ERR_EXTENSION
 */
class FileUploadException extends DomainException
{
}
