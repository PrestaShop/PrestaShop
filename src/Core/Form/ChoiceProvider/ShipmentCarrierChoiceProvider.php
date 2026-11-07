<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception as DBALException;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

/**
 * Provides the carriers that actually ship something, as choices for the shipments grid filter.
 *
 * Choices are keyed on the carrier name rather than on its id on purpose: editing a carrier creates
 * a new ps_carrier row and soft-deletes the previous one, so shipments of what merchants perceive as
 * a single carrier are spread over several ids that all share the same name.
 */
final class ShipmentCarrierChoiceProvider implements FormChoiceProviderInterface
{
    public function __construct(
        private readonly Connection $connection,
        private readonly string $dbPrefix,
        private readonly ShopContext $shopContext,
    ) {
    }

    /**
     * {@inheritdoc}
     *
     * @throws DBALException if the carriers cannot be read from the database
     */
    public function getChoices(): array
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('c.`name`')
            ->from($this->dbPrefix . 'shipment', 's')
            ->innerJoin('s', $this->dbPrefix . 'carrier', 'c', 's.`id_carrier` = c.`id_carrier`')
            // Scoped exactly like the grid this feeds: offering a carrier the listing can never
            // return would leak which carriers another shop ships with, and match nothing.
            ->innerJoin('s', $this->dbPrefix . 'orders', 'o', 's.`id_order` = o.`id_order`')
            ->where('s.`deleted` = 0')
            ->andWhere('o.`id_shop` IN (:context_shop_ids)')
            ->andWhere('c.`name` != \'\'')
            ->setParameter('context_shop_ids', $this->shopContext->getAssociatedShopIds(), ArrayParameterType::INTEGER)
            ->groupBy('c.`name`')
            ->orderBy('c.`name`', 'ASC')
        ;

        $choices = [];

        foreach ($qb->executeQuery()->fetchFirstColumn() as $name) {
            $choices[$name] = $name;
        }

        return $choices;
    }
}
