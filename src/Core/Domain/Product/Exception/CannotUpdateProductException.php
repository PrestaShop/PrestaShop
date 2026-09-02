<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\Exception;

/**
 * Is thrown when updating a product failed
 */
class CannotUpdateProductException extends ProductException
{
    /**
     * When generic product update fails
     */
    public const FAILED_UPDATE_PRODUCT = 1;

    /**
     * When basic information update fails
     */
    public const FAILED_UPDATE_BASIC_INFO = 10;

    /**
     * When updating product fields associated with price fails
     */
    public const FAILED_UPDATE_PRICES = 20;

    /**
     * When product options update fails
     */
    public const FAILED_UPDATE_OPTIONS = 30;

    /**
     * When product details update fails
     */
    public const FAILED_UPDATE_DETAILS = 40;

    /**
     * When product tags update fails
     */
    public const FAILED_UPDATE_TAGS = 50;

    /**
     * When product categories update fails
     */
    public const FAILED_UPDATE_CATEGORIES = 60;

    /**
     * When product properties associated with customization fields update fails
     */
    public const FAILED_UPDATE_CUSTOMIZATION_FIELDS = 70;

    /**
     * When product shipping options update fails
     */
    public const FAILED_UPDATE_SHIPPING_OPTIONS = 80;

    /**
     * When product default supplier update fails
     */
    public const FAILED_UPDATE_DEFAULT_SUPPLIER = 90;

    /**
     * When product default category update fails
     */
    public const FAILED_UPDATE_DEFAULT_CATEGORY = 100;

    /**
     * When product seo options update fails
     */
    public const FAILED_UPDATE_SEO = 110;

    /**
     * When product attachments association update fails
     */
    public const FAILED_UPDATE_ATTACHMENTS = 120;

    /**
     * When product default combination update fails
     */
    public const FAILED_UPDATE_DEFAULT_ATTRIBUTE = 130;

    /**
     * When search indexation update for product fails
     */
    public const FAILED_UPDATE_SEARCH_INDEXATION = 140;

    /**
     * When stock update fails
     */
    public const FAILED_UPDATE_STOCK = 150;

    /**
     * When type update fails
     */
    public const FAILED_UPDATE_TYPE = 160;

    /**
     * When product status update fails
     */
    public const FAILED_UPDATE_STATUS = 170;

    /**
     * When copying from shop to shop fails
     */
    public const FAILED_SHOP_COPY = 170;

    /**
     * When product duplication fails
     */
    public const FAILED_DUPLICATION = 180;
}
