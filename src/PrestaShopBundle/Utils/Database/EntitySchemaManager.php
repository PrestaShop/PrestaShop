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

namespace PrestaShopBundle\Utils\Database;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\Persistence\Mapping\Driver\MappingDriverChain;
use Exception;
use PrestaShop\PrestaShop\Core\Exception\DatabaseException;
use PrestaShop\PrestaShop\Core\Util\Database\EntitySchemaManagerInterface;

/**
 * Class EntitySchemaManager help to manage an entity schema: update, create, drop.
 */
class EntitySchemaManager implements EntitySchemaManagerInterface
{
    /**
     * @var SchemaTool
     */
    protected SchemaTool $schemaTool;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(protected EntityManagerInterface $entityManager)
    {
        $this->schemaTool = new SchemaTool($this->entityManager);
    }

    /**
     * Create entity table
     *
     * @param string $entityClassName
     * @param bool $dropIfExist
     *
     * @return bool
     *
     * @throws DatabaseException
     */
    public function create(string $entityClassName, bool $dropIfExist = true): bool
    {
        return $this->createMultiple([$entityClassName], $dropIfExist);
    }

    /**
     * Update entity table schema
     *
     * @param string $entityClassName
     *
     * @return bool
     *
     * @throws DatabaseException
     */
    public function update(string $entityClassName): bool
    {
        return $this->updateMultiple([$entityClassName]);
    }

    /**
     * Drop entity table
     *
     * @param string $entityClassName
     *
     * @return bool
     *
     * @throws DatabaseException
     */
    public function drop(string $entityClassName): bool
    {
        return $this->dropMultiple([$entityClassName]);
    }

    /**
     * Create multiple entities tables in a single schema operation,
     * so foreign keys between entities of the same batch are resolved properly.
     *
     * @param array $entitiesClassesName
     * @param bool $dropIfExist
     *
     * @return bool
     *
     * @throws DatabaseException
     */
    public function createMultiple(array $entitiesClassesName, bool $dropIfExist = true): bool
    {
        if ($dropIfExist) {
            $this->dropMultiple($entitiesClassesName);
        }

        try {
            $this->schemaTool->createSchema($this->getClassesMetadata($entitiesClassesName));
        } catch (Exception $exception) {
            throw new DatabaseException($exception->getMessage(), 0, $exception);
        }

        return true;
    }

    /**
     * Update multiple entities tables in a single schema operation
     *
     * @param array $entitiesClassesName
     *
     * @return bool
     *
     * @throws DatabaseException
     */
    public function updateMultiple(array $entitiesClassesName): bool
    {
        try {
            $this->schemaTool->updateSchema($this->getClassesMetadata($entitiesClassesName), true);
        } catch (Exception $exception) {
            throw new DatabaseException($exception->getMessage(), 0, $exception);
        }

        return true;
    }

    /**
     * Drop multiple entities tables in a single schema operation
     *
     * @param array $entitiesClassesName
     *
     * @return bool
     *
     * @throws DatabaseException
     */
    public function dropMultiple(array $entitiesClassesName): bool
    {
        try {
            $this->schemaTool->dropSchema($this->getClassesMetadata($entitiesClassesName));
        } catch (Exception $exception) {
            throw new DatabaseException($exception->getMessage(), 0, $exception);
        }

        return true;
    }

    /**
     * Adds a new path for entities to the entity manager (Ex.: %kernel.project_dir%/modules/MyModule/src/Entity)
     *
     * The path is appended to the already configured metadata drivers so the
     * mappings of the core entities remain available.
     *
     * @param string $entityPath The path where Doctrine should look for entities
     */
    public function addEntityPath(string $entityPath): void
    {
        $configuration = $this->entityManager->getConfiguration();
        $currentDriver = $configuration->getMetadataDriverImpl();

        if ($currentDriver instanceof MappingDriverChain) {
            $defaultDriver = $currentDriver->getDefaultDriver();

            if ($defaultDriver instanceof AnnotationDriver) {
                $defaultDriver->addPaths([$entityPath]);
            } else {
                $currentDriver->setDefaultDriver($this->createAnnotationDriver($entityPath));
            }

            return;
        }

        if ($currentDriver instanceof AnnotationDriver) {
            $currentDriver->addPaths([$entityPath]);

            return;
        }

        $configuration->setMetadataDriverImpl($this->createAnnotationDriver($entityPath));
    }

    /**
     * @param string $entityPath
     *
     * @return AnnotationDriver
     */
    private function createAnnotationDriver(string $entityPath): AnnotationDriver
    {
        return new AnnotationDriver(new AnnotationReader(), [$entityPath]);
    }

    /**
     * @param array $entitiesClassesName
     *
     * @return ClassMetadata[]
     */
    private function getClassesMetadata(array $entitiesClassesName): array
    {
        return array_map(
            fn (string $entityClassName): ClassMetadata => $this->entityManager->getClassMetadata($entityClassName),
            $entitiesClassesName
        );
    }
}
