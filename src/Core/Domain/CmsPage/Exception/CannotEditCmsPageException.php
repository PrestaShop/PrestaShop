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

namespace PrestaShop\PrestaShop\Core\Domain\CmsPage\Exception;

/**
 * Is thrown when cms page cannot be edited
 */
class CannotEditCmsPageException extends CmsPageException
{
    /**
     * A CMS page that is the same everywhere must be edited in all shop context
     */
    public const EDIT_IN_ALL_SHOPS = 1;

    /**
     * A CMS page that is different from shop to shop must be edited in specific shop context
     */
    public const EDIT_IN_ONE_SHOP = 2;

    /**
     * To edit a page in all shop context, one must have edit permissions in all shops where the page is used.
     */
    public const INSUFFICIENT_PERMISSION_ALL_SHOPS = 3;

    /**
     * To edit a page in all shop context, one must have edit permissions in all shops where the page is used.
     */
    public const INSUFFICIENT_PERMISSION_ASSOCIATIONS = 4;
}
