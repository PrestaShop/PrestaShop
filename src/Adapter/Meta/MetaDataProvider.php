<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Meta;

use Db;
use DbQuery;
use Meta;
use PrestaShop\PrestaShop\Core\Meta\MetaDataProviderInterface;

/**
 * Class MetaDataProvider is responsible for providing data related with meta entity.
 */
class MetaDataProvider implements MetaDataProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getIdByPage($pageName)
    {
        $query = new DbQuery();
        $query->select('`id_meta`');
        $query->from('meta');
        $query->where('`page`= "' . pSQL($pageName) . '"');

        $idMeta = 0;
        $result = Db::getInstance()->getValue($query);

        if ($result) {
            $idMeta = $result;
        }

        return $idMeta;
    }

    /**
     * @return array
     */
    public function getAvailablePages()
    {
        return Meta::getPages(true);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultMetaPageNameById($metaId)
    {
        $query = new DbQuery();
        $query->select('`page`');
        $query->from('meta');
        $query->where('`id_meta`=' . (int) $metaId);
        $query->where('`page` NOT LIKE "module-%"');
        $result = Db::getInstance()->getValue($query);

        return is_string($result) ? $result : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getModuleMetaPageNameById($metaId)
    {
        $query = new DbQuery();
        $query->select('`page`');
        $query->from('meta');
        $query->where('`id_meta`=' . (int) $metaId);
        $query->where('`page` LIKE "module-%"');

        $result = Db::getInstance()->getValue($query);

        return is_string($result) ? $result : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultMetaPageNamesExcludingFilled()
    {
        $pages = Meta::getPages(true);

        $result = [];
        foreach ($pages as $pageName => $fileName) {
            if (!$this->isModuleFile($fileName)) {
                $result[$pageName] = $fileName;
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getNotConfiguredModuleMetaPageNames()
    {
        $pages = Meta::getPages(true);

        $result = [];
        foreach ($pages as $pageName => $fileName) {
            if ($this->isModuleFile($fileName)) {
                $result[$pageName] = $fileName;
            }
        }

        return $result;
    }

    /**
     * Checks whenever the file contains module file pattern.
     *
     * @param string $fileName
     *
     * @return bool
     */
    private function isModuleFile($fileName)
    {
        return str_starts_with($fileName, 'module-');
    }
}
