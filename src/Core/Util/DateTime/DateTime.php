<?php
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

namespace PrestaShop\PrestaShop\Core\Util\DateTime;

use RuntimeException;

/**
 * Defines reusable values for DateTime
 */
final class DateTime
{
    /**
     * Default format for date string
     */
    public const DEFAULT_DATE_FORMAT = 'Y-m-d';

    /**
     * Default format for date time string
     */
    public const DEFAULT_DATETIME_FORMAT = 'Y-m-d H:i:s';

    /**
     * DateTime value which should be considered same as null
     */
    public const NULL_DATETIME = '0000-00-00 00:00:00';

    /**
     * @deprecated use NULL_DATETIME or NULL_DATE depending on usecase
     */
    public const NULL_VALUE = '0000-00-00 00:00:00';

    /**
     * Date value which should be considered same as null
     */
    public const NULL_DATE = '0000-00-00';

    /**
     * This class only defines constants and has no reason to be initialized
     */
    public function __construct()
    {
        throw new RuntimeException(sprintf('This class purpose is to define constants only. You might have mistaken it with "%s"', \DateTime::class));
    }
}
