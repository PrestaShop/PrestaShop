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

namespace PrestaShop\PrestaShop\Core\Product;

use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\RedirectType;

/**
 * @deprecated
 * @see RedirectType instead
 */
interface ProductInterface
{
    public const REDIRECT_TYPE_CATEGORY_MOVED_PERMANENTLY = RedirectType::TYPE_CATEGORY_PERMANENT;
    public const REDIRECT_TYPE_CATEGORY_FOUND = RedirectType::TYPE_CATEGORY_TEMPORARY;
    public const REDIRECT_TYPE_PRODUCT_MOVED_PERMANENTLY = RedirectType::TYPE_PRODUCT_PERMANENT;
    public const REDIRECT_TYPE_PRODUCT_FOUND = RedirectType::TYPE_PRODUCT_TEMPORARY;
    public const REDIRECT_TYPE_NOT_FOUND = RedirectType::TYPE_NOT_FOUND;
    public const REDIRECT_TYPE_GONE = RedirectType::TYPE_GONE;
    public const REDIRECT_TYPE_DEFAULT = RedirectType::TYPE_DEFAULT;
    public const REDIRECT_TYPE_NOT_FOUND_DISPLAYED = RedirectType::TYPE_NOT_FOUND_DISPLAYED;
    public const REDIRECT_TYPE_GONE_DISPLAYED = RedirectType::TYPE_GONE_DISPLAYED;
    public const REDIRECT_TYPE_SUCCESS_DISPLAYED = RedirectType::TYPE_SUCCESS_DISPLAYED;
}
