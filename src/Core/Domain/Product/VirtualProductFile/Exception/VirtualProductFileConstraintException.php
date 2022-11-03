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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Exception;

/**
 * Thrown when virtual product file constraints are violated
 * Each constant represents related error code
 */
class VirtualProductFileConstraintException extends VirtualProductFileException
{
    /**
     * Each of following constants respectively represents invalid entity properties
     */
    public const INVALID_ID = 10;
    public const INVALID_DISPLAY_NAME = 20;
    public const INVALID_FILENAME = 30;
    public const INVALID_CREATION_DATE = 40;
    public const INVALID_EXPIRATION_DATE = 50;
    public const INVALID_ACCESS_DAYS = 60;
    public const INVALID_DOWNLOAD_TIMES_LIMIT = 70;
    public const INVALID_ACTIVE = 80;
    public const INVALID_SHAREABLE = 90;

    /** Is thrown when file already exists for given product */
    public const ALREADY_HAS_A_FILE = 100;
}
