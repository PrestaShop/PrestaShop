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

namespace PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception;

/**
 * Is thrown when cms page category constraints are violated
 */
class CmsPageCategoryConstraintException extends CmsPageCategoryException
{
    /**
     * @var int is used when incorrect values supplied for bulk cms categories operations
     */
    const INVALID_BULK_DATA = 1;

    /**
     * @var int is used when cms page category is moved to the same category as it is
     *          or if it is moved to its child category
     */
    const CANNOT_MOVE_CATEGORY_TO_PARENT = 2;

    /**
     * @var int is used to raise an error when default language is missing for the field
     */
    const MISSING_DEFAULT_LANGUAGE_FOR_NAME = 3;

    /**
     * @var int is used to raise an error when friendly url is missing for the field
     */
    const MISSING_DEFAULT_LANGUAGE_FOR_FRIENDLY_URL = 4;

    /**
     * @var int is used to validate category name to match the specific pattern
     */
    const INVALID_CATEGORY_NAME = 5;

    /**
     * @var int is used to validate link rewrite that matches specific regex pattern
     */
    const INVALID_LINK_REWRITE = 6;

    /**
     * @var int is used to validate meta title for specific regex pattern
     */
    const INVALID_META_TITLE = 7;

    /**
     * @var int Is used to validate meta description for specific regex pattern
     */
    const INVALID_META_DESCRIPTION = 8;

    /**
     * @var int is used to validate meta keywords for specific regex pattern
     */
    const INVALID_META_KEYWORDS = 9;

    /**
     * @var int Is used to validate description according to clean html standard/
     */
    const INVALID_DESCRIPTION = 10;
}
