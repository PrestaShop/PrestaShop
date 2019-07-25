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

namespace PrestaShop\PrestaShop\Core\Domain\Validation;

/**
 * Provides reusable regex patterns for validation
 */
final class RegexPattern
{
    const NAME = '/^[^0-9!<>,;?=+()@#"°{}_$%:¤|]*$/u';

    const CATALOG_NAME = '/^[^<>;=#{}]*$/u';

    const GENERIC_NAME = '/^[^<>={}]*$/u';

    const CITY_NAME = '/^[^!<>;?=+@#"°{}_$%]*$/u';

    const FILE_NAME = '/^[a-zA-Z0-9_.-]+$/';

    const MODULE_NAME = '/^[a-zA-Z0-9_-]+$/';

    const ADDRESS = '/^[^!<>?=+@{}_$%]*$/u';

    const POST_CODE = '/^[a-zA-Z 0-9-]+$/';

    const PHONE_NUMBER = '/^[+0-9. ()\/-]*$/';

    const MESSAGE = '/^[^\[<>{}\]]+$/i';

    const LANG_CODE = '/^[a-zA-Z]{2}(-[a-zA-Z]{2})?$/';
}
