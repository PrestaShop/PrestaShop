<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
