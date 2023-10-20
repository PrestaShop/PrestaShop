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

namespace PrestaShop\PrestaShop\Core\Domain\Product\Exception;

/**
 * Is thrown when product duplication fails
 */
class CannotDuplicateProductException extends ProductException
{
    /**
     * When product categories duplication fails
     */
    public const FAILED_DUPLICATE_CATEGORIES = 10;

    /**
     * When product suppliers duplication fails
     */
    public const FAILED_DUPLICATE_SUPPLIERS = 20;

    /**
     * When product attributes duplication fails
     */
    public const FAILED_DUPLICATE_COMBINATIONS = 30;

    /**
     * When product group reduction duplication fails
     */
    public const FAILED_DUPLICATE_GROUP_REDUCTION = 40;

    /**
     * When product related product duplication fails
     */
    public const FAILED_DUPLICATE_RELATED_PRODUCTS = 50;

    /**
     * When product features duplication fails
     */
    public const FAILED_DUPLICATE_FEATURES = 60;

    /**
     * When product specific prices duplication fails
     */
    public const FAILED_DUPLICATE_SPECIFIC_PRICES = 70;

    /**
     * When packed products duplication fails
     */
    public const FAILED_DUPLICATE_PACKED_PRODUCTS = 80;

    /**
     * When product customization fields duplication fails
     */
    public const FAILED_DUPLICATE_CUSTOMIZATION_FIELDS = 90;

    /**
     * When product tags duplication fails
     */
    public const FAILED_DUPLICATE_TAGS = 100;

    /**
     * When product downloads duplication fails
     */
    public const FAILED_DUPLICATE_DOWNLOADS = 110;

    /**
     * When product images duplication fails
     */
    public const FAILED_DUPLICATE_IMAGES = 120;

    /**
     * When product taxes duplication fails
     */
    public const FAILED_DUPLICATE_TAXES = 130;

    /**
     * When product prices duplication fails
     */
    public const FAILED_DUPLICATE_PRICES = 140;

    /**
     * When product carriers duplication fails
     */
    public const FAILED_DUPLICATE_CARRIERS = 150;

    /**
     * When product attachment association duplication fails
     */
    public const FAILED_DUPLICATE_ATTACHMENT_ASSOCIATION = 160;
}
