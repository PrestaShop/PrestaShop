<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Exception;

class InvalidProductShopAssociationException extends ProductException
{
    public const EMPTY_SHOPS_ASSOCIATION = 1;

    public const SOURCE_SHOP_MISSING_IN_SHOP_ASSOCIATION = 2;
    public const SOURCE_SHOP_NOT_ASSOCIATED = 3;
}
