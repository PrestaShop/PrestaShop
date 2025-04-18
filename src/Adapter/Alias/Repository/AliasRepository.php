<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Alias\Repository;

use Alias;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Adapter\Alias\Validate\AliasValidator;
use PrestaShop\PrestaShop\Core\Domain\Alias\Exception\AliasConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Alias\Exception\AliasNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Alias\Exception\BulkAliasException;
use PrestaShop\PrestaShop\Core\Domain\Alias\Exception\CannotAddAliasException;
use PrestaShop\PrestaShop\Core\Domain\Alias\Exception\CannotDeleteAliasException;
use PrestaShop\PrestaShop\Core\Domain\Alias\ValueObject\AliasId;
use PrestaShop\PrestaShop\Core\Domain\Alias\ValueObject\SearchTerm;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\BulkFeatureException;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Repository\AbstractObjectModelRepository;

class AliasRepository extends AbstractObjectModelRepository
{
    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param AliasValidator $aliasValidator
     */
    public function __construct(
        protected Connection $connection,
        protected string $dbPrefix,
        protected AliasValidator $aliasValidator
    ) {
    }

    /**
     * Creates new Alias entity and saves to the database
     *
     * @param string $searchTerm
     * @param array{
     *   array{
     *     alias: string,
     *     active: bool,
     *   }
     * } $aliases
     *
     * @return AliasId[]
     *
     * @throws CoreException
     */
    public function addAliases(string $searchTerm, array $aliases): array
    {
        // Get all aliases already in use for other search terms
        $aliasesToAdd = array_column($aliases, 'alias');
        $aliasesAlreadyUsed = $this->getAliasesAlreadyInUseNotForSearchTerm($searchTerm, $aliasesToAdd);

        // If we have aliases already in use, we need to throw an exception
        if (count($aliasesAlreadyUsed) > 0) {
            throw new AliasConstraintException(
                implode(', ', $aliasesAlreadyUsed),
                AliasConstraintException::ALIAS_ALREADY_USED
            );
        }

        $aliasIds = [];

        foreach ($aliases as $searchAlias) {
            // As search term is not a primary key, we need make sure that alias and search combination does not exist
            // if alias exists for search term, we need to apply new active if needed
            $aliasIfExists = $this->getAliasIfExists($searchAlias['alias'], $searchTerm);
            if ($aliasIfExists) {
                $aliasIfExists->active = $searchAlias['active'];
                $this->partialUpdate($aliasIfExists, ['active'], CannotAddAliasException::class);
                continue;
            }

            $alias = new Alias();
            $alias->search = $searchTerm;
            $alias->alias = $searchAlias['alias'];
            $alias->active = $searchAlias['active'];
            $this->aliasValidator->validate($alias);

            $this->addObjectModel($alias, CannotAddAliasException::class);

            $aliasIds[] = new AliasId((int) $alias->id);
        }

        return $aliasIds;
    }

    /**
     * @param AliasId $aliasId
     *
     * @return Alias
     */
    public function get(AliasId $aliasId): Alias
    {
        /** @var Alias $alias */
        $alias = $this->getObjectModel(
            $aliasId->getValue(),
            Alias::class,
            AliasNotFoundException::class
        );

        return $alias;
    }

    /**
     * @param string $alias
     * @param string $searchTerm
     *
     * @return Alias|null
     */
    public function getAliasIfExists(string $alias, string $searchTerm): ?Alias
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('a.id_alias')
            ->from($this->dbPrefix . 'alias', 'a')
            ->where('a.search = :search')
            ->andWhere('a.alias = :alias')
            ->setParameter('search', $searchTerm)
            ->setParameter('alias', $alias)
        ;
        $alias = $qb->executeQuery()->fetchOne();

        if ($alias) {
            return $this->get(new AliasId((int) $alias));
        }

        return null;
    }

    /**
     * @param string $searchTerm
     *
     * @return array{
     *   array{
     *     alias: string,
     *     active: bool,
     *   }
     * }
     */
    public function getAliasesBySearchTerm(string $searchTerm): array
    {
        $qb = $this->connection->createQueryBuilder()
            ->addSelect('a.alias, a.active')
            ->from($this->dbPrefix . 'alias', 'a')
            ->where('a.search = :search')
            ->setParameter('search', $searchTerm)
            ->addOrderBy('a.alias', 'ASC')
        ;

        return $qb->executeQuery()->fetchAllAssociative();
    }

    public function delete(AliasId $aliasId): void
    {
        $this->deleteObjectModel($this->get($aliasId), CannotDeleteAliasException::class);
    }

    /**
     * @param Alias $alias
     * @param string[] $propertiesToUpdate
     * @param string $exceptionClass
     *
     * @return void
     */
    public function partialUpdate(Alias $alias, array $propertiesToUpdate, string $exceptionClass): void
    {
        $this->aliasValidator->validate($alias);
        $this->partiallyUpdateObjectModel($alias, $propertiesToUpdate, $exceptionClass);
    }

    /**
     * Deletes all related aliases
     *
     * @param SearchTerm $searchTerm
     */
    public function deleteAliasesBySearchTerm(SearchTerm $searchTerm): void
    {
        $exceptions = [];

        $aliasIds = $this->connection->createQueryBuilder()
            ->addSelect('a.id_alias')
            ->from($this->dbPrefix . 'alias', 'a')
            ->where('a.search = :searchTerm')
            ->setParameter('searchTerm', $searchTerm->getValue())
            ->executeQuery()
            ->fetchFirstColumn()
        ;

        if (empty($aliasIds)) {
            return;
        }

        foreach ($aliasIds as $currentAliasId) {
            try {
                $this->deleteObjectModel($this->get(new AliasId((int) $currentAliasId)), CannotDeleteAliasException::class);
            } catch (CannotDeleteAliasException $e) {
                $exceptions[] = $e;
            }
        }

        if (!empty($exceptions)) {
            throw new BulkAliasException(
                $exceptions,
                'Errors occurred during Alias bulk delete action',
                BulkFeatureException::FAILED_BULK_DELETE
            );
        }
    }

    /**
     * @param string $searchPhrase
     * @param int|null $limit
     *
     * @return array<int, array<string, string>>
     */
    public function searchSearchTerms(string $searchPhrase, ?int $limit = null): array
    {
        return $this->connection->createQueryBuilder()
            ->addSelect('a.search')
            ->from($this->dbPrefix . 'alias', 'a')
            ->addOrderBy('a.search', 'ASC')
            ->addGroupBy('a.search')
            ->setMaxResults($limit)
            ->where('a.search LIKE :searchPhrase')
            ->setParameter('searchPhrase', '%' . $searchPhrase . '%')
            ->executeQuery()
            ->fetchAllAssociative();
    }

    /**
     * @param array $searchTerms
     *
     * @return array{
     *   array{
     *     id_alias: int,
     *     search: string,
     *     alias: string,
     *     active: bool,
     *   }
     * }
     */
    public function getAliasesBySearchTerms(array $searchTerms): array
    {
        $qb = $this->connection->createQueryBuilder()
            ->addSelect('a.id_alias, a.search, a.alias, a.active')
            ->from($this->dbPrefix . 'alias', 'a')
            ->where('a.search IN (:searchTerms)')
            ->setParameter('searchTerms', $searchTerms, ArrayParameterType::STRING)
            ->addOrderBy('a.search', 'ASC')
            ->addOrderBy('a.alias', 'ASC')
        ;

        return $qb->executeQuery()->fetchAllAssociative();
    }

    /**
     * @param string $searchTerm
     * @param string[] $aliases
     *
     * @return string[]
     */
    private function getAliasesAlreadyInUseNotForSearchTerm(string $searchTerm, array $aliases): array
    {
        $qb = $this->connection->createQueryBuilder()
            ->addSelect('DISTINCT a.alias')
            ->from($this->dbPrefix . 'alias', 'a')
            ->addOrderBy('a.alias', 'ASC')
            ->where('a.search != :searchTerm')
            ->andWhere('a.alias IN (:aliases)')
            ->setParameter('searchTerm', $searchTerm)
            ->setParameter('aliases', $aliases, ArrayParameterType::STRING)
        ;

        return $qb->executeQuery()->fetchFirstColumn();
    }
}
