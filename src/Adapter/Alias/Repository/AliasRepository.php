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
use Doctrine\DBAL\Driver\Exception;
use PrestaShop\PrestaShop\Adapter\Alias\Validate\AliasValidator;
use PrestaShop\PrestaShop\Core\Domain\Alias\Exception\AliasException;
use PrestaShop\PrestaShop\Core\Domain\Alias\Exception\AliasNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Alias\Exception\CannotAddAliasException;
use PrestaShop\PrestaShop\Core\Domain\Alias\ValueObject\AliasId;
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
    }

    /**
     * Creates new Alias entity and saves to the database
     *
     * @param string $search
     * @param array $aliases
     *
     * @return AliasId[]
     *
     * @throws CoreException
     */
    public function create(string $search, array $aliases): array
    {
        $aliasIds = [];

        foreach ($aliases as $searchAlias) {
            $alias = new Alias();
            $alias->search = $search;
            $alias->alias = $searchAlias;
            $alias->active = true;

            $this->aliasValidator->validateCreation($alias);
            $this->addObjectModel($alias, CannotAddAliasException::class);

            $aliasIds = new AliasId($alias->id);
        }

        return $aliasIds;
    }

    public function get(AliasId $aliasId): Alias
    {
        try {
            /** @var Alias $alias */
            $alias = $this->getObjectModel(
                $aliasId->getValue(),
                Alias::class,
                AliasException::class
            );
        } catch (AliasException $e) {
            throw new AliasNotFoundException($aliasId, $e->getMessage());
        }

        return $alias;
    }

    /**
     * @param string $search
     *
     * @return string[]
     *
     * @throws Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function getAliasesBySearch(string $search): array
    {

        $qb = $this->connection->createQueryBuilder()
            ->addSelect('a.alias')
            ->from($this->dbPrefix . 'alias', 'a')
            ->where('a.search = :search')
            ->setParameter('search', $search)
        ;

        return $qb->execute()->fetchFirstColumn();
    }
}
