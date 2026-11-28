<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShopBundle\Utils\Database;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\DBAL\Exception as DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;
use Doctrine\Persistence\Mapping\Driver\MappingDriverChain;
use PrestaShop\PrestaShop\Core\Exception\DatabaseException;
use PrestaShop\PrestaShop\Core\Util\Database\EntitySchemaManagerInterface;

/**
 * Class EntitySchemaManager help to manage an entity schema: update, create, drop.
 */
final class EntitySchemaManager implements EntitySchemaManagerInterface
{
    /**
     * @var SchemaTool
     */
    private SchemaTool $schemaTool;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(private EntityManagerInterface $entityManager)
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
        } catch (ToolsException|DBALException $exception) {
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
        } catch (ToolsException|DBALException $exception) {
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
        } catch (ToolsException|DBALException $exception) {
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
