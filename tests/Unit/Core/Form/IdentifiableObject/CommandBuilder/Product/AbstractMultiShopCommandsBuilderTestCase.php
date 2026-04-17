<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Core\Form\IdentifiableObject\CommandBuilder\Product;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

abstract class AbstractMultiShopCommandsBuilderTestCase extends TestCase
{
    protected const SHOP_ID = 1;

    protected const MODIFY_ALL_SHOPS_PREFIX = 'modify_all_shops_';

    protected function getSingleShopConstraint(): ShopConstraint
    {
        return ShopConstraint::shop(self::SHOP_ID);
    }
}
