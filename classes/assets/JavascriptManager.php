<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
     * @param string $id
     * @param string $relativePath
     * @param string $position
     * @param int $priority
     * @param bool $inline
     * @param string|null $attribute
     * @param string $server
     * @param string|null $version
     */
    public function register(
        $id,
        $relativePath,
        $position = self::DEFAULT_JS_POSITION,
        $priority = self::DEFAULT_PRIORITY,
        $inline = false,
        $attribute = null,
        $server = 'local',
        ?string $version = null
    ) {
        if ('remote' === $server) {
            $this->add($id, $relativePath, $position, $priority, $inline, $attribute, $server, $version);
        } elseif ($fullPath = $this->getFullPath($relativePath)) {
            $this->add($id, $fullPath, $position, $priority, $inline, $attribute, $server, $version);
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
     * @param string $id
     * @param string $fullPath
     * @param string $position
     * @param int $priority
     * @param bool $inline
     * @param string $attribute
     * @param string $server
     * @param string|null $version
     */
    protected function add($id, $fullPath, $position, $priority, $inline, $attribute, $server, ?string $version)
    {
        $position = $this->getSanitizedPosition($position);
        $attribute = $this->getSanitizedAttribute($attribute);

        $srcPath = $fullPath;
        $fullPath = $version ? $fullPath . '?' . $version : $fullPath;

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
            'path' => $srcPath,
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
                    file_get_contents($this->getPathFromUri($item['path']));
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
