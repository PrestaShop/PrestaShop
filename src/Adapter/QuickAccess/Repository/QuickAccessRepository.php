<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\QuickAccess\Repository;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\QuickAccess\Exception\CannotAddQuickAccessException;
use PrestaShop\PrestaShop\Core\Domain\QuickAccess\Exception\CannotDeleteQuickAccessException;
use PrestaShop\PrestaShop\Core\Domain\QuickAccess\Exception\CannotUpdateQuickAccessException;
use PrestaShop\PrestaShop\Core\Domain\QuickAccess\Exception\QuickAccessNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\QuickAccess\ValueObject\QuickAccessId;
use PrestaShop\PrestaShop\Core\QuickAccess\QuickAccessRepositoryInterface;
use PrestaShop\PrestaShop\Core\Repository\AbstractObjectModelRepository;
use QuickAccess;

class QuickAccessRepository extends AbstractObjectModelRepository implements QuickAccessRepositoryInterface
{
    public function __construct(
        private readonly Connection $connection,
        private readonly string $dbPrefix
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

    public function get(QuickAccessId $quickAccessId): QuickAccess
    {
        /** @var QuickAccess $quickAccess */
        $quickAccess = $this->getObjectModel(
            $quickAccessId->getValue(),
            QuickAccess::class,
            QuickAccessNotFoundException::class
        );

        return $quickAccess;
    }

    public function add(QuickAccess $quickAccess): QuickAccess
    {
        $this->addObjectModel($quickAccess, CannotAddQuickAccessException::class);

        return $quickAccess;
    }

    public function update(QuickAccess $quickAccess): void
    {
        $this->updateObjectModel($quickAccess, CannotUpdateQuickAccessException::class);
    }

    public function delete(QuickAccessId $quickAccessId): void
    {
        $this->deleteObjectModel($this->get($quickAccessId), CannotDeleteQuickAccessException::class);
    }

    /**
     * Returns true if a quick access with the given link URL already exists.
     * The DB has no UNIQUE KEY on `link`, so the duplicate check must be done explicitly.
     */
    public function hasLink(string $link): bool
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('q.id_quick_access')
            ->from($this->dbPrefix . 'quick_access', 'q')
            ->where('q.link = :link')
            ->setParameter('link', $link)
            ->setMaxResults(1)
        ;

        return (bool) $qb->execute()->fetchOne();
    }
}
