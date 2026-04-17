<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter;

use PrestaShop\PrestaShop\Core\Foundation\Database\EntityMetaData;

/**
 * Retrieve all meta data of an ObjectModel.
 */
class EntityMetaDataRetriever
{
    /**
     * @param string $className
     *
     * @return EntityMetaData
     *
     * @throws CoreException
     */
    public function getEntityMetaData($className)
    {
        $metaData = new EntityMetaData();

        $metaData->setEntityClassName($className);

        if (property_exists($className, 'definition')) {
            // Legacy entity
            $classVars = get_class_vars($className);
            $metaData->setTableName($classVars['definition']['table']);
            $metaData->setPrimaryKeyFieldNames([$classVars['definition']['primary']]);
        } else {
            throw new CoreException(sprintf('Cannot get metadata for entity `%s`.', $className));
        }

        return $metaData;
    }
}
