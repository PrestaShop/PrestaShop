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

namespace PrestaShop\PrestaShop\Core\Domain\Category\Exception;

/**
 * Class CategoryConstraintException.
 */
class CategoryConstraintException extends CategoryException
{
    /**
     * Code is used when category does not have name.
     */
    public const EMPTY_NAME = 1;

    /**
     * Code is used when category does not have link rewrite.
     */
    public const EMPTY_LINK_REWRITE = 2;

    /**
     * Code is used when invalid status is set to category.
     */
    public const INVALID_STATUS = 4;

    /**
     * Code is used when invalid delete mode is used to delete a category.
     */
    public const INVALID_DELETE_MODE = 5;

    /**
     * Code is used when invalid parent id is supplied.
     */
    public const INVALID_PARENT_ID = 6;

    /**
     * Code is used when invalid id is supplied.
     */
    public const INVALID_ID = 10;

    /**
     * Code is used when performing bulk delete of categories with empty data.
     */
    public const EMPTY_BULK_DELETE_DATA = 12;

    /**
     * When category redirect type is invalid
     */
    public const INVALID_REDIRECT_TYPE = 14;
}
