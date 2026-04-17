<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
