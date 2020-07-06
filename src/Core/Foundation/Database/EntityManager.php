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

namespace PrestaShop\PrestaShop\Core\Foundation\Database;

use PrestaShop\PrestaShop\Adapter\EntityMetaDataRetriever;

class EntityManager
{
    private $db;
    private $configuration;

    private $entityMetaData = [];

    public function __construct(
        DatabaseInterface $db,
        \PrestaShop\PrestaShop\Core\ConfigurationInterface $configuration
    ) {
        $this->db = $db;
        $this->configuration = $configuration;
    }

    /**
     * Return current database object used.
     *
     * @return DatabaseInterface
     */
    public function getDatabase()
    {
        return $this->db;
    }

    /**
     * Return current repository used.
     *
     * @param string $className
     *
     * @return mixed
     */
    public function getRepository($className)
    {
        $repositoryClass = null;
        if (is_callable([$className, 'getRepositoryClassName'])) {
            $repositoryClass = call_user_func([$className, 'getRepositoryClassName']);
        }

        if (!$repositoryClass) {
            $repositoryClass = '\\PrestaShop\\PrestaShop\\Core\\Foundation\\Database\\EntityRepository';
        }

        $repository = new $repositoryClass(
            $this,
            $this->configuration->get('_DB_PREFIX_'),
            $this->getEntityMetaData($className)
        );

        return $repository;
    }

    /**
     * Return entity's meta data.
     *
     * @param string $className
     *
     * @return mixed
     *
     * @throws \PrestaShop\PrestaShop\Adapter\CoreException
     */
    public function getEntityMetaData($className)
    {
        if (!array_key_exists($className, $this->entityMetaData)) {
            $metaDataRetriever = new EntityMetaDataRetriever();
            $this->entityMetaData[$className] = $metaDataRetriever->getEntityMetaData($className);
        }

        return $this->entityMetaData[$className];
    }

    /**
     * Flush entity to DB.
     *
     * @param EntityInterface $entity
     *
     * @return $this
     */
    public function save(EntityInterface $entity)
    {
        $entity->save();

        return $this;
    }

    /**
     * DElete entity from DB.
     *
     * @param EntityInterface $entity
     *
     * @return $this
     */
    public function delete(EntityInterface $entity)
    {
        $entity->delete();

        return $this;
    }
}
