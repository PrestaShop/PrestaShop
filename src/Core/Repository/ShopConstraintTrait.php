<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Repository;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopCollection;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

trait ShopConstraintTrait
{
    protected function applyShopConstraint(
        QueryBuilder $queryBuilder,
        ?ShopConstraint $shopConstraint = null
    ): QueryBuilder {
        if (null === $shopConstraint) {
            return $queryBuilder;
        }

        if ($shopConstraint->getShopId() !== null) {
            $queryBuilder
                ->andWhere('id_shop = :shop')
                ->setParameter('shop', $shopConstraint->getShopId()->getValue())
            ;
        }

        if ($shopConstraint->getShopGroupId() !== null) {
            $queryBuilder
                ->andWhere('id_shop_group = :shop_group')
                ->setParameter('shop_group', $shopConstraint->getShopGroupId()->getValue())
            ;
        }

        if ($shopConstraint instanceof ShopCollection && $shopConstraint->hasShopIds()) {
            $queryBuilder
                ->andWhere('id_shop IN (:shopIds)')
                ->setParameter(
                    'shopIds',
                    array_map(fn (ShopId $shopId) => $shopId->getValue(), $shopConstraint->getShopIds()),
                    ArrayParameterType::INTEGER
                )
            ;
        }

        return $queryBuilder;
    }
}
