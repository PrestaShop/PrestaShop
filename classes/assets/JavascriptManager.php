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
class JavascriptManagerCore extends AbstractAssetManager
{
    protected $list;

    protected $valid_position = ['head', 'bottom'];
    protected $valid_attribute = ['async', 'defer'];

    /**
     * @return array
     */
    protected function getDefaultList()
    {
        $default = [];
        foreach ($this->valid_position as $position) {
            $default[$position] = [
                'external' => [],
                'inline' => [],
            ];
        }

        return $default;
    }

    /**
     * @param $id
     * @param string $relativePath
     * @param string $position
     * @param int $priority
     * @param bool $inline
     * @param string $attribute
     * @param string $server
     */
    public function register(
        $id,
        $relativePath,
        $position = self::DEFAULT_JS_POSITION,
        $priority = self::DEFAULT_PRIORITY,
        $inline = false,
        $attribute = null,
        $server = 'local'
    ) {
        if ('remote' === $server) {
            $this->add($id, $relativePath, $position, $priority, $inline, $attribute, $server);
        } elseif ($fullPath = $this->getFullPath($relativePath)) {
            $this->add($id, $fullPath, $position, $priority, $inline, $attribute, $server);
        }
    }

    public function unregisterById($idToRemove)
    {
        foreach ($this->valid_position as $position) {
            foreach ($this->list[$position] as $type => $null) {
                foreach ($this->list[$position][$type] as $id => $item) {
                    if ($idToRemove === $id) {
                        unset($this->list[$position][$type][$id]);
                    }
                }
            }
        }
    }

    /**
     * @param $id
     * @param string $fullPath
     * @param string $position
     * @param int $priority
     * @param bool $inline
     * @param string $attribute
     * @param string $server
     */
    protected function add($id, $fullPath, $position, $priority, $inline, $attribute, $server)
    {
        $priority = is_int($priority) ? $priority : self::DEFAULT_PRIORITY;
        $position = $this->getSanitizedPosition($position);
        $attribute = $this->getSanitizedAttribute($attribute);

        if ('remote' === $server) {
            $uri = $fullPath;
            $type = 'external';
        } else {
            $uri = $this->getFQDN() . $this->getUriFromPath($fullPath);
            $type = ($inline) ? 'inline' : 'external';
        }

        $this->list[$position][$type][$id] = [
            'id' => $id,
            'type' => $type,
            'path' => $fullPath,
            'uri' => $uri,
            'priority' => $priority,
            'attribute' => $attribute,
            'server' => $server,
        ];
    }

    /**
     * @return array
     */
    public function getList()
    {
        $this->sortList();
        $this->addInlinedJavascriptContent();

        return $this->list;
    }

    private function addInlinedJavascriptContent()
    {
        foreach ($this->valid_position as $position) {
            foreach ($this->list[$position]['inline'] as &$item) {
                $item['content'] =
                    '/* ---- ' . $item['id'] . ' @ ' . $item['path'] . ' ---- */' . "\r\n" .
                    file_get_contents($item['path']);
            }
        }
    }

    private function getSanitizedPosition($position)
    {
        return in_array($position, $this->valid_position, true) ? $position : self::DEFAULT_JS_POSITION;
    }

    private function getSanitizedAttribute($attribute)
    {
        return in_array($attribute, $this->valid_attribute, true) ? $attribute : '';
    }

    private function sortList()
    {
        foreach ($this->valid_position as $position) {
            foreach ($this->list[$position] as $type => $items) {
                Tools::uasort($items, function ($a, $b) {
                    if ($a['priority'] === $b['priority']) {
                        return 0;
                    }

                    return ($a['priority'] < $b['priority']) ? -1 : 1;
                });
                $this->list[$position][$type] = $items;
            }
        }
    }
}
