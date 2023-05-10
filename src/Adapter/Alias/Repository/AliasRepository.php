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
use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Adapter\Alias\Validate\AliasValidator;
use PrestaShop\PrestaShop\Core\Domain\Alias\Exception\AliasNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Alias\Exception\BulkAliasException;
use PrestaShop\PrestaShop\Core\Domain\Alias\Exception\CannotAddAliasException;
use PrestaShop\PrestaShop\Core\Domain\Alias\Exception\CannotDeleteAliasException;
use PrestaShop\PrestaShop\Core\Domain\Alias\ValueObject\AliasId;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\BulkFeatureException;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Repository\AbstractObjectModelRepository;

class AliasRepository extends AbstractObjectModelRepository
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
     * @var AliasValidator
     */
    private $aliasValidator;

    public function __construct(
        Connection $connection,
        string $dbPrefix,
        AliasValidator $aliasValidator
    ) {
        $this->aliasValidator = $aliasValidator;
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
    }

    /**
     * Creates new Alias entity and saves to the database
     *
     * @param string $searchTerm
     * @param string[] $aliases
     * @param bool $active
     *
     * @return AliasId[]
     *
     * @throws CoreException
     */
    public function create(string $searchTerm, array $aliases, bool $active = true): array
    {
        $aliasIds = [];

        foreach ($aliases as $searchAlias) {
            // As search term is not a primary key, we need make sure that alias and search combination does not exist
            if ($this->aliasExists($searchAlias, $searchTerm)) {
                continue;
            }

            $alias = new Alias();
            $alias->search = $searchTerm;
            $alias->alias = $searchAlias;
            $alias->active = $active;
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
     * @return bool
     */
    public function aliasExists(string $alias, string $searchTerm): bool
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('a.id_alias')
            ->from($this->dbPrefix . 'alias', 'a')
            ->where('a.search = :search')
            ->andWhere('a.alias = :alias')
            ->setParameter('search', $searchTerm)
            ->setParameter('alias', $alias)
        ;

        return (bool) $qb->execute()->fetchOne();
    }

    /**
     * @param string $searchTerm
     *
     * @return string[]
     */
    public function getAliasesBySearchTerm(string $searchTerm): array
    {
        $qb = $this->connection->createQueryBuilder()
            ->addSelect('a.alias')
            ->from($this->dbPrefix . 'alias', 'a')
            ->where('a.search = :search')
            ->setParameter('search', $searchTerm)
        ;

        return $qb->execute()->fetchFirstColumn();
    }

    public function delete(AliasId $aliasId): void
    {
        $this->get($aliasId)->delete();
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
     * @param string $searchTerm
     */
    public function deleteAliasesBySearchTerm(string $searchTerm): void
    {
        $exceptions = [];

        $aliasIds = $this->connection->createQueryBuilder()
            ->addSelect('a.id_alias')
            ->from($this->dbPrefix . 'alias', 'a')
            ->where('a.search = :searchTerm')
            ->setParameter('searchTerm', $searchTerm)
            ->execute()
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
}
