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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product;

/**
 * Defines settings for product.
 * If related Value Object does not exist, then various settings (e.g. regex, length constraints) are saved here
 */
class ProductSettings
{
    /**
     * Class not supposed to be initialized, it only serves as static storage
     */
    private function __construct()
    {
    }

    /**
     * Bellow constants define maximum allowed length of product properties
     */
    public const MAX_NAME_LENGTH = 128;
    public const MAX_MPN_LENGTH = 40;
    public const MAX_META_TITLE_LENGTH = 128;
    public const MAX_META_DESCRIPTION_LENGTH = 512;

    /**
     * This is the default value for short description (a.k.a. summary) maximum length,
     * but this value is configurable,
     * it is saved in configuration named PS_PRODUCT_SHORT_DESC_LIMIT
     */
    public const MAX_DESCRIPTION_SHORT_LENGTH = 800;
    public const MAX_DESCRIPTION_LENGTH = 21844;
    public const MAX_LINK_REWRITE_LENGTH = 128;
    public const MAX_AVAILABLE_NOW_LABEL_LENGTH = 255;
    public const MAX_AVAILABLE_LATER_LABEL_LENGTH = 255;
}
