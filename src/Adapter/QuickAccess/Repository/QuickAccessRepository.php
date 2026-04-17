<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\QuickAccess\Repository;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\QuickAccess\QuickAccessRepositoryInterface;
use PrestaShop\PrestaShop\Core\Repository\AbstractObjectModelRepository;

class QuickAccessRepository extends AbstractObjectModelRepository implements QuickAccessRepositoryInterface
{
    public function __construct(
        private Connection $connection,
        private string $dbPrefix
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function fetchAll(LanguageId $languageId): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('q.id_quick_access, q.new_window, q.link, ql.name')
            ->from($this->dbPrefix . 'quick_access', 'q')
            ->innerJoin(
                'q',
                $this->dbPrefix . 'quick_access_lang',
                'ql',
                'q.id_quick_access = ql.id_quick_access'
            )
            ->where('ql.id_lang = :languageId')
            ->addOrderBy('ql.name', 'ASC')
            ->setParameter('languageId', $languageId->getValue())
        ;

        return $qb->execute()->fetchAllAssociative();
    }
}
