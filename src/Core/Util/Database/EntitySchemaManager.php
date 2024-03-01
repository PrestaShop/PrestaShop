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

namespace PrestaShop\PrestaShop\Core\Util\Database;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;
use PrestaShop\PrestaShop\Core\Exception\DatabaseException;

/**
 * Class EntitySchemaManager help to manage an entity schema: update, create, drop.
 */
final class EntitySchemaManager
{
    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $entityManager;

    /**
     * @var SchemaTool
     */
    protected SchemaTool $schemaTool;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->schemaTool = new SchemaTool($this->entityManager);
    }

    /**
     * create entity table
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
        $entityMetaData = $this->entityManager->getClassMetadata($entityClassName);

        if ($dropIfExist) {
            $this->drop($entityClassName);
        }

        try {
            $this->schemaTool->createSchema([$entityMetaData]);
        } catch (ToolsException $exception) {
            throw new DatabaseException($exception->getMessage());
        }

        return true;
    }

    /**
     * Update entity table schema
     *
     * @param string $entityClassName
     *
     * @return bool
     */
    public function update(string $entityClassName): bool
    {
        $classMetadata = $this->entityManager->getClassMetadata($entityClassName);
        $this->schemaTool->updateSchema([$classMetadata], true);

        return true;
    }

    /**
     * Drop entity table
     *
     * @param string $entityClassName
     *
     * @return bool
     */
    public function drop(string $entityClassName): bool
    {
        $classMetadata = $this->entityManager->getClassMetadata($entityClassName);
        $this->schemaTool->dropSchema([$classMetadata]);

        return true;
    }

    /**
     * create multiple entities tables
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
        $status = true;

        foreach ($entitiesClassesName as $entityClassName) {
            if (!$this->create($entityClassName, $dropIfExist)) {
                $status = false;
            }
        }

        return $status;
    }

    /**
     * update multiple entities tables
     *
     * @param array $entitiesClassesName
     *
     * @return bool
     */
    public function updateMultiple(array $entitiesClassesName): bool
    {
        $status = true;

        foreach ($entitiesClassesName as $entityClassName) {
            if (!$this->update($entityClassName)) {
                $status = false;
            }
        }

        return $status;
    }

    /**
     * drop multiple entities tables
     *
     * @param array $entitiesClassesName
     *
     * @return bool
     */
    public function dropMultiple(array $entitiesClassesName): bool
    {
        $status = true;

        foreach ($entitiesClassesName as $entityClassName) {
            if (!$this->drop($entityClassName)) {
                $status = false;
            }
        }

        return $status;
    }
}
