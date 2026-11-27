<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

/**
 * Customer groups associated to at least one shop of the context, keyed by name.
 */
final class CustomerGroupChoiceProvider implements FormChoiceProviderInterface
{
    public function __construct(
        private readonly Connection $connection,
        private readonly string $dbPrefix,
        private readonly LanguageContext $languageContext,
        private readonly ShopContext $shopContext,
    ) {
    }

    public function getChoices(): array
    {
        $shopAssociationQuery = $this->connection->createQueryBuilder()
            ->select('1')
            ->from($this->dbPrefix . 'group_shop', 'gs')
            ->where('gs.id_group = g.id_group')
            ->andWhere('gs.id_shop IN (:shopIds)')
        ;

        $qb = $this->connection->createQueryBuilder()
            ->select('g.id_group', 'gl.name')
            ->from($this->dbPrefix . 'group', 'g')
            ->innerJoin('g', $this->dbPrefix . 'group_lang', 'gl', 'gl.id_group = g.id_group AND gl.id_lang = :languageId')
            ->where('EXISTS (' . $shopAssociationQuery->getSQL() . ')')
            ->setParameter('languageId', $this->languageContext->getId())
            ->setParameter('shopIds', $this->shopContext->getAssociatedShopIds(), Connection::PARAM_INT_ARRAY)
            ->orderBy('g.id_group', 'ASC')
        ;

        $choices = [];
        foreach ($qb->executeQuery()->fetchAllAssociative() as $group) {
            $choices[$group['name']] = (int) $group['id_group'];
        }

        return $choices;
    }
}
