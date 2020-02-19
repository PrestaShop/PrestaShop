<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
declare(strict_types=1);

namespace PrestaShopBundle\Service\Database;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Tools\SchemaTool;
use PrestaShop\PrestaShop\Core\Exception\FileNotFoundException;
use PrestaShop\PrestaShop\Core\Util\Exception\NamespaceNotFoundException;
use PrestaShop\PrestaShop\Core\Util\NamespaceFinder;

class DoctrineFolderSchemaUpdater
{
    /**
     * @param EntityManagerInterface $em
     */
    private $em;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var NamespaceFinder
     */
    private $namespaceFinder;

    /**
     * @var SchemaTool
     */
    private $schemaTool;

    /**
     * @var ClassMetadata[]
     */
    private $allClasses;

    /**
     * @var ClassMetadata[]
     */
    private $namespaceClasses;

    /**
     * @var string[]
     */
    private $tableIds;

    /**
     * @param EntityManagerInterface $em
     * @param Configuration $configuration
     * @param NamespaceFinder $namespaceFinder
     */
    public function __construct(
        EntityManagerInterface $em,
        Configuration $configuration,
        NamespaceFinder $namespaceFinder
    ) {
        $this->em = $em;
        $this->configuration = $configuration;
        $this->namespaceFinder = $namespaceFinder;
        $this->schemaTool = new SchemaTool($this->em);
    }

    /**
     * Get SQL statements to update database schema based on entities' folder, using doctrine schema update.
     * It only updates tables related to the provided folder.
     *
     * @param string $entitiesFolder
     *
     * @return array
     *
     * @throws FileNotFoundException
     * @throws NamespaceNotFoundException
     */
    public function getFolderSchemaUpdate(string $entitiesFolder): array
    {
        $this->fetchEntityFolderClassList($entitiesFolder);
        // Save mode true: we update only the namespaces classes, and other tables are not dropped
        $updateSqlStatements = $this->schemaTool->getUpdateSchemaSql($this->namespaceClasses, true);

        return $this->filterSqlStatements($updateSqlStatements);
    }

    /**
     * Updates database schema based on entities' folder, using doctrine schema update.
     * It only updates tables related to the provided folder.
     *
     * @param string $entitiesFolder
     *
     * @throws DBALException
     * @throws FileNotFoundException
     * @throws NamespaceNotFoundException
     */
    public function updateFolderSchema(string $entitiesFolder)
    {
        $sqlStatements = $this->getFolderSchemaUpdate($entitiesFolder);

        $this->executeStatements($sqlStatements);
    }

    /**
     * Get SQL statements to remove database schema based on entities' folder, using doctrine schema update.
     * It only removes tables related to the provided folder.
     *
     * @param string $entitiesFolder
     *
     * @return array
     *
     * @throws FileNotFoundException
     * @throws NamespaceNotFoundException
     */
    public function getFolderSchemaRemoval(string $entitiesFolder): array
    {
        $this->fetchEntityFolderClassList($entitiesFolder);
        $remainingClassList = array_diff($this->em->getMetadataFactory()->getAllMetadata(), $this->namespaceClasses);
        // Save mode false: we force drop on the missing classes (which are the namespace ones here)
        $removalSqlStatements = $this->schemaTool->getUpdateSchemaSql($remainingClassList, false);

        return $this->filterSqlStatements($removalSqlStatements);
    }

    /**
     * Removes database schema based on entities' folder, using doctrine schema update.
     * It only removes tables related to the provided folder.
     *
     * @param string $entitiesFolder
     *
     * @throws DBALException
     * @throws FileNotFoundException
     * @throws NamespaceNotFoundException
     */
    public function removeFolderSchema(string $entitiesFolder)
    {
        $sqlStatements = $this->getFolderSchemaRemoval($entitiesFolder);

        $this->executeStatements($sqlStatements);
    }

    /**
     * @param string $entitiesFolder
     *
     * @throws FileNotFoundException
     * @throws NamespaceNotFoundException
     */
    private function fetchEntityFolderClassList(string $entitiesFolder): void
    {
        if (!is_dir($entitiesFolder)) {
            throw new FileNotFoundException(sprintf(
                'Cannot find entity folder %s',
                $entitiesFolder
            ));
        }

        $entitiesNamespace = $this->namespaceFinder->findNamespaceFromFolder($entitiesFolder);

        $this->allClasses = $this->em->getMetadataFactory()->getAllMetadata();
        $this->namespaceClasses = [];
        /** @var ClassMetadata $metadata */
        foreach ($this->allClasses as $metadata) {
            if ($metadata->namespace === $entitiesNamespace) {
                $this->namespaceClasses[] = $metadata;
            }
        }
    }

    /**
     * The scope of this class is to ONLY update/remove the table related to a specific
     * entity folder. So any statement related to another table will be skipped.
     *
     * @param array $sqlStatements
     *
     * @return array
     */
    private function filterSqlStatements(array $sqlStatements): array
    {
        $protectedTables = $this->getProtectedTables();

        // Remove the DROP TABLE
        foreach ($sqlStatements as $key => $sql) {
            $matches = [];
            if (preg_match('/DROP TABLE (.+?)$/', $sql, $matches)) {
                $droppedTable = trim($matches[1]);
                if (in_array($droppedTable, $protectedTables)) {
                    unset($sqlStatements[$key]);
                }
            }
        }

        // Then remove the ALTER TABLE on protected tables
        foreach ($sqlStatements as $key => $sql) {
            $matches = [];
            if (preg_match('/ALTER TABLE (.+?) /', $sql, $matches)) {
                $alteredTable = trim($matches[1]);
                if (in_array($alteredTable, $protectedTables)) {
                    unset($sqlStatements[$key]);
                }
            }
        }

        return $sqlStatements;
    }

    /**
     * Returns the list of table that are NOT related to the entity folder
     *
     * @return string[]
     */
    private function getProtectedTables(): array
    {
        $folderTables = [];
        /** @var ClassMetadata $metadata */
        foreach ($this->namespaceClasses as $metadata) {
            $folderTables[] = $metadata->table['name'];
            // Join table are also protected (ex: *_shop tables)
            foreach ($metadata->getAssociationMappings() as $associationMapping) {
                if (!empty($associationMapping['joinTable'])) {
                    $folderTables[] = $associationMapping['joinTable']['name'];
                }
            }
        }

        $protectedTables = [];
        if (null === $this->tableIds) {
            $conn = $this->em->getConnection();
            $this->tableIds = $conn->getSchemaManager()->listTableNames();
        }

        foreach ($this->tableIds as $tableName) {
            if (!in_array($tableName, $folderTables)) {
                $protectedTables[] = $tableName;
            }
        }

        return $protectedTables;
    }

    /**
     * @param array $sqlStatements
     *
     * @throws DBALException
     */
    private function executeStatements(array $sqlStatements)
    {
        $conn = $this->em->getConnection();

        foreach ($sqlStatements as $sql) {
            $conn->executeQuery($sql);
        }
    }
}
