<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Discount;

use PrestaShopBundle\Form\Admin\Type\FormattedTextareaType;

class DiscountSettings
{
    public const MAX_NAME_LENGTH = 255;
    public const MAX_DESCRIPTION_LENGTH = FormattedTextareaType::LIMIT_MEDIUMTEXT_UTF8_MB4;
    public const AMOUNT = 'amount';
    public const PERCENT = 'percentage';

    // Period filter values
    public const PERIOD_FILTER_ALL = 'all';
    public const PERIOD_FILTER_ACTIVE = 'active';
    public const PERIOD_FILTER_SCHEDULED = 'scheduled';
    public const PERIOD_FILTER_EXPIRED = 'expired';

    // Special values for CartRule::reduction_product (used for product level discount)
    // Previously known as order without shipping
    public const PRODUCTS_TOTAL = 0;
    public const CHEAPEST_PRODUCT = -1;
    public const PRODUCT_SEGMENT = -2;
}
