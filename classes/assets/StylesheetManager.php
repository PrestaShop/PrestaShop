<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

class StylesheetManagerCore extends AbstractAssetManager
{
    private $valid_media = array(
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
    );

    protected function getDefaultList()
    {
        return [
            'external' => array(),
            'inline' => array(),
        ];
    }

    public function register(
        $id,
        $relativePath,
        $media = self::DEFAULT_MEDIA,
        $priority = self::DEFAULT_PRIORITY,
        $inline = false,
        $server = 'local',
        $needRtl = true
    ) {
        $fullPath = $this->getFullPath($relativePath);
        $rtlFullPath = $this->getFullPath(str_replace('.css', '_rtl.css', $relativePath));
        $context = Context::getContext();
        $isRTL = is_object($context->language) && $context->language->is_rtl;
        if ('remote' === $server) {
            $this->add($id, $relativePath, $media, $priority, $inline, $server);
        } else if ($needRtl && $isRTL && $rtlFullPath) {
            $this->add($id, $rtlFullPath, $media, $priority, $inline, $server);
        } else if ($fullPath) {
            $this->add($id, $fullPath, $media, $priority, $inline, $server);
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

    public function getList()
    {
        $this->sortList();
        $this->addInlinedStyleContent();

        return $this->list;
    }

    protected function add($id, $fullPath, $media, $priority, $inline, $server)
    {
        $priority = is_int($priority) ? $priority : self::DEFAULT_PRIORITY;
        $media = $this->getSanitizedMedia($media);

        if ('remote' === $server) {
            $uri = $fullPath;
            $type = 'external';
        } else {
            $uri = $this->getFQDN().$this->getUriFromPath($fullPath);
            $type = ($inline) ? 'inline' : 'external';
        }

        $this->list[$type][$id] = array(
            'id' => $id,
            'type' => $type,
            'path' => $fullPath,
            'uri' => $uri,
            'media' => $media,
            'priority' => $priority,
            'server' => $server,
        );
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
                '/* ---- '.$item['id'].' @ '.$item['path'].' ---- */'."\r\n".
                file_get_contents($item['path']);
        }
    }
}
