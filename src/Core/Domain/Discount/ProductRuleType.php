<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Discount;

/**
 * Product rules target a type of entities, which are identified via this enum.
 */
enum ProductRuleType: string
{
    case CATEGORIES = 'categories';
    case PRODUCTS = 'products';
    case COMBINATIONS = 'combinations';
    case MANUFACTURERS = 'manufacturers';
    case SUPPLIERS = 'suppliers';
    case ATTRIBUTES = 'attributes';
    case FEATURES = 'features';
}
