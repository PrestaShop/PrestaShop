<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
class StylesheetManagerCore extends AbstractAssetManager
{
    private $valid_media = [
        'all',
        'braille',
        'embossed',
        'handheld',
        'print',
        'projection',
        'screen',
        'speech',
        'tty',
        'tv',
    ];

    protected function getDefaultList()
    {
        return [
            'external' => [],
            'inline' => [],
        ];
    }

    /**
     * @param string $id
     * @param string $relativePath
     * @param string $media
     * @param int $priority
     * @param bool $inline
     * @param string $server
     * @param bool $needRtl
     * @param string|null $version
     */
    public function register(
        $id,
        $relativePath,
        $media = self::DEFAULT_MEDIA,
        $priority = self::DEFAULT_PRIORITY,
        $inline = false,
        $server = 'local',
        $needRtl = true,
        ?string $version = null
    ) {
        $fullPath = $this->getFullPath($relativePath);
        $rtlFullPath = $this->getFullPath(str_replace('.css', '_rtl.css', $relativePath));
        $context = Context::getContext();
        $isRTL = is_object($context->language) && $context->language->is_rtl;
        if ('remote' === $server) {
            $this->add($id, $relativePath, $media, $priority, $inline, $server, $version);
        } elseif ($needRtl && $isRTL && $rtlFullPath) {
            $this->add($id, $rtlFullPath, $media, $priority, $inline, $server, $version);
        } elseif ($fullPath) {
            $this->add($id, $fullPath, $media, $priority, $inline, $server, $version);
        }
    }

    public function unregisterById($idToRemove)
    {
        foreach ($this->list as $type => $null) {
            foreach ($this->list[$type] as $id => $item) {
                if ($idToRemove === $id) {
                    unset($this->list[$type][$id]);
                }
            }
        }
    }

    /**
     * @return array
     */
    public function getList()
    {
        $this->sortList();
        $this->addInlinedStyleContent();

        return $this->list;
    }

    /**
     * @param string $id
     * @param string $fullPath
     * @param string $media
     * @param int $priority
     * @param bool $inline
     * @param string $server
     * @param string|null $version
     */
    protected function add($id, $fullPath, $media, $priority, $inline, $server, ?string $version)
    {
        $media = $this->getSanitizedMedia($media);

        $srcPath = $fullPath;
        $fullPath = $version ? $fullPath . '?' . $version : $fullPath;

        if ('remote' === $server) {
            $uri = $fullPath;
            $type = 'external';
        } else {
            $uri = $this->getFQDN() . $this->getUriFromPath($fullPath);
            $type = ($inline) ? 'inline' : 'external';
        }

        $this->list[$type][$id] = [
            'id' => $id,
            'type' => $type,
            'path' => $srcPath,
            'uri' => $uri,
            'media' => $media,
            'priority' => $priority,
            'server' => $server,
        ];
    }

    private function getSanitizedMedia($media)
    {
        return in_array($media, $this->valid_media, true) ? $media : self::DEFAULT_MEDIA;
    }

    private function sortList()
    {
        foreach ($this->list as &$items) {
            Tools::uasort(
                $items,
                function ($a, $b) {
                    if ($a['priority'] === $b['priority']) {
                        return 0;
                    }

                    return ($a['priority'] < $b['priority']) ? -1 : 1;
                }
            );
        }
    }

    private function addInlinedStyleContent()
    {
        foreach ($this->list['inline'] as &$item) {
            $item['content'] =
                '/* ---- ' . $item['id'] . ' @ ' . $item['path'] . ' ---- */' . "\r\n" .
                file_get_contents($this->getPathFromUri($item['path']));
        }
    }
}
