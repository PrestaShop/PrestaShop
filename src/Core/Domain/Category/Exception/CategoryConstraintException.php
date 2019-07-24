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

namespace PrestaShop\PrestaShop\Core\Domain\Category\Exception;

/**
 * Class CategoryConstraintException.
 */
class CategoryConstraintException extends CategoryException
{
    /**
     * Code is used when category does not have name.
     */
    const EMPTY_NAME = 1;

    /**
     * Code is used when category does not have link rewrite.
     */
    const EMPTY_LINK_REWRITE = 2;

    /**
     * Code is used when invalid status is set to category.
     */
    const INVALID_STATUS = 4;

    /**
     * Code is used when invalid delete mode is used to delete a category.
     */
    const INVALID_DELETE_MODE = 5;

    /**
     * Code is used when invalid parent id is supplied.
     */
    const INVALID_PARENT_ID = 6;

    /**
     * Code is used when too many menu thumbnails is being set for category.
     */
    const TOO_MANY_MENU_THUMBNAILS = 8;

    /**
     * Code is used when performing bulk delete of categories with empty data.
     */
    const EMPTY_BULK_DELETE_DATA = 12;
}
