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
