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

namespace PrestaShopBundle\Service\Database;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Tools\SchemaTool;
use PrestaShopBundle\Service\Database\Exception\EntityFolderNotFoundException;
use PrestaShopBundle\Service\Database\Exception\EntityNamespaceNotFoundException;
use Symfony\Component\Finder\Finder;

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
     * @var SchemaTool
     */
    private $schemaTool;

    /**
     * @param EntityManagerInterface $em
     * @param Configuration $configuration
     */
    public function __construct(
        EntityManagerInterface $em,
        Configuration $configuration
    ) {
        $this->em = $em;
        $this->configuration = $configuration;
        $this->schemaTool = new SchemaTool($this->em);
    }

    /**
     * Get SQL statements to update database schema based on entities' folder, using doctrine schema update.
     * It only updates tables related to the provided folder.
     *
     * @param string $entitiesFolder
     *
     * @return string[]
     *
     * @throws EntityFolderNotFoundException
     * @throws EntityNamespaceNotFoundException
     */
    public function getFolderSchemaUpdate(string $entitiesFolder): array
    {
        $folderClassList = $this->getFolderClassList($entitiesFolder);
        $updateSqlStatements = $this->schemaTool->getUpdateSchemaSql($folderClassList, true);
        $protectedTables = $this->getProtectedTables($folderClassList);

        return $this->filterSqlStatements($updateSqlStatements, $protectedTables);
    }

    /**
     * Updates database schema based on entities' folder, using doctrine schema update.
     * It only updates tables related to the provided folder.
     *
     * @param string $entitiesFolder
     *
     * @throws EntityFolderNotFoundException
     * @throws EntityNamespaceNotFoundException
     * @throws DBALException
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
     * @return string[]
     *
     * @throws EntityFolderNotFoundException
     * @throws EntityNamespaceNotFoundException
     */
    public function getFolderSchemaRemoval(string $entitiesFolder): array
    {
        $folderClassList = $this->getFolderClassList($entitiesFolder);
        $remainingClassList = array_diff($this->em->getMetadataFactory()->getAllMetadata(), $folderClassList);
        $removalSqlStatements = $this->schemaTool->getUpdateSchemaSql($remainingClassList, false);
        $protectedTables = $this->getProtectedTables($folderClassList);

        return $this->filterSqlStatements($removalSqlStatements, $protectedTables);
    }

    /**
     * Removes database schema based on entities' folder, using doctrine schema update.
     * It only removes tables related to the provided folder.
     *
     * @param string $entitiesFolder
     *
     * @throws DBALException
     * @throws EntityFolderNotFoundException
     * @throws EntityNamespaceNotFoundException
     */
    public function removeFolderSchema(string $entitiesFolder)
    {
        $sqlStatements = $this->getFolderSchemaRemoval($entitiesFolder);

        $this->executeStatements($sqlStatements);
    }

    /**
     * @param string $entitiesFolder
     *
     * @return ClassMetadata[]
     *
     * @throws EntityFolderNotFoundException
     * @throws EntityNamespaceNotFoundException
     */
    private function getFolderClassList(string $entitiesFolder): array
    {
        if (!is_dir($entitiesFolder)) {
            throw new EntityFolderNotFoundException(sprintf(
                'Cannot find entity folder %s',
                $entitiesFolder
            ));
        }

        $entitiesNamespace = $this->getFolderNamespace($entitiesFolder);

        return $this->getMetadataListByNamespace($entitiesNamespace);
    }

    /**
     * @param string $entitiesNamespace
     *
     * @return ClassMetadata[]
     */
    private function getMetadataListByNamespace(string $entitiesNamespace): array
    {
        $metadataList = [];
        /** @var ClassMetadata $metadata */
        foreach ($this->em->getMetadataFactory()->getAllMetadata() as $metadata) {
            if ($metadata->namespace === $entitiesNamespace) {
                $metadataList[] = $metadata;
            }
        }

        return $metadataList;
    }

    /**
     * @param string $entitiesFolder
     *
     * @return string
     *
     * @throws EntityNamespaceNotFoundException
     */
    private function getFolderNamespace(string $entitiesFolder)
    {
        $finder = new Finder();
        $finder->files()->in($entitiesFolder)->name('*.php');
        foreach ($finder as $phpFile) {
            $phpContent = file_get_contents($phpFile->getRealPath());
            if (preg_match('~namespace[ \t]+(.+)[ \t]*;~Um', $phpContent, $matches)) {
                return $matches[1];
            }
        }

        throw new EntityNamespaceNotFoundException(sprintf(
            'Cannot find namespace in folder %s',
            $entitiesFolder
        ));
    }

    /**
     * @param array $sqlStatements
     * @param array $protectedTables
     *
     * @return array
     */
    private function filterSqlStatements(array $sqlStatements, array $protectedTables): array
    {
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
     * @param array $folderClassList
     *
     * @return string[]
     */
    private function getProtectedTables(array $folderClassList): array
    {
        $folderTables = [];
        /** @var ClassMetadata $metadata */
        foreach ($folderClassList as $metadata) {
            $folderTables[] = $metadata->table['name'];
            // Join table are also protected (ex: *_shop tables)
            foreach ($metadata->getAssociationMappings() as $associationMapping) {
                if (!empty($associationMapping['joinTable'])) {
                    $folderTables[] = $associationMapping['joinTable']['name'];
                }
            }
        }

        $protectedTables = [];
        $conn = $this->em->getConnection();
        foreach ($conn->getSchemaManager()->listTableNames() as $tableName) {
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
