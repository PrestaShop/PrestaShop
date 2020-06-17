<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Util\Number;

use PrestaShop\PrestaShop\Core\Exception\CoreException;

/**
 * Exception thrown when something goes wrong in @var NumberExtractor service
 */
class NumberExtractorException extends CoreException
{
    /**
     * When provided property path is not valid
     */
    const INVALID_PROPERTY_PATH = 10;

    /**
     * When the resource/property from which value is being extracted is of invalid type
     */
    const INVALID_RESOURCE_TYPE = 20;

    /**
     * When property is not accessible or doesn't exist
     */
    const NOT_ACCESSIBLE = 30;

    /**
     * When property type is not numeric therefore it cannot be converted to number
     */
    const NON_NUMERIC_PROPERTY = 40;
}
