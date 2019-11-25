<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\Cart\QueryHandler;

use Doctrine\DBAL\Connection;
use PDO;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartException;
use PrestaShop\PrestaShop\Core\Domain\Cart\Query\GetProductQuantityInCart;

/**
 * Handles GetProductQuantityInCart query using dbal connection
 */
final class GetProductQuantityInCartHandler implements GetProductQuantityInCartHandlerInterface
{
    /**
     * @var Connection
     */
    private $connection;
    /**
     * @var string
     */
    private $dbPrefix;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix
    ) {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
    }

    /**
     * @param GetProductQuantityInCart $query
     *
     * @return int
     */
    public function handle(GetProductQuantityInCart $query): int
    {
        $cartIdVal = $query->getCartId()->getValue();
        $productIdVal = $query->getProductId()->getValue();
        $combinationIdVal = $query->getCombinationId();
        $customizationIdVal = $query->getCustomizationId();

        $qb = $this->connection->createQueryBuilder();
        $qb->select('cp.quantity')
            ->from($this->dbPrefix . 'cart_product', 'cp')
            ->where('cp.id_cart = :cart_id AND cp.id_product = :product_id')
            ->setParameter('cart_id', $cartIdVal)
            ->setParameter('product_id', $productIdVal);

        if ($combinationIdVal) {
            $qb->andWhere('id_product_attribute = :combination_id')
                ->setParameter('combination_id', $query->getCombinationId()->getValue());
        }
        if ($customizationIdVal) {
            $qb->andWhere('id_customization = :customization_id')
                ->setParameter('customization_id', $query->getCustomizationId()->getValue());
        }

        $result = $qb->execute()->fetch(PDO::FETCH_COLUMN);

        if (false === $result) {
            throw new CartException(
                'An error occurred when trying to fetch cart product quantity'
            );
        }

        return (int) $result;
    }
}
