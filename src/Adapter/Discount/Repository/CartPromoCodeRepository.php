<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Discount\Repository;

use Doctrine\DBAL\Connection;

class CartPromoCodeRepository
{
    public function __construct(
        private readonly Connection $connection,
        private readonly string $dbPrefix
    ) {
    }

    /**
     * Retrieve customer-entered promo codes currently attached to a cart.
     *
     * @return array<int, array{id: int, discount_type: string|null, priority: int, date_add: string}>
     */
    public function getPromoCodesForCart(int $cartId): array
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->select(
                'cr.id_cart_rule AS id',
                'crt.discount_type AS discount_type',
                'cr.priority AS priority',
                'cr.date_add AS date_add'
            )
            ->from($this->dbPrefix . 'cart_cart_rule', 'ccr')
            ->innerJoin(
                'ccr',
                $this->dbPrefix . 'cart_rule',
                'cr',
                'cr.id_cart_rule = ccr.id_cart_rule'
            )
            ->leftJoin(
                'cr',
                $this->dbPrefix . 'cart_rule_type',
                'crt',
                'crt.id_cart_rule_type = cr.id_cart_rule_type'
            )
            ->where('ccr.id_cart = :cartId')
            ->andWhere("TRIM(cr.code) <> ''")
            ->setParameter('cartId', $cartId)
            ->orderBy('cr.priority', 'ASC')
            ->addOrderBy('cr.gift_product', 'DESC')
            ->addOrderBy('cr.date_add', 'ASC')
            ->addOrderBy('cr.id_cart_rule', 'ASC')
        ;

        $promoCodes = $queryBuilder->executeQuery()->fetchAllAssociative();

        return array_map(static function (array $promoCode): array {
            return [
                'id' => (int) $promoCode['id'],
                'discount_type' => $promoCode['discount_type'] !== null ? (string) $promoCode['discount_type'] : null,
                'priority' => (int) $promoCode['priority'],
                'date_add' => (string) $promoCode['date_add'],
            ];
        }, $promoCodes);
    }
}
