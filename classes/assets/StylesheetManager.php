<?php
/**
 * 2007-2016 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
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
            'external' => [],
            'inline' => [],
        ];
    }

    public function register($id, $relativePath, $media = self::DEFAULT_MEDIA, $priority = self::DEFAULT_PRIORITY, $inline = false)
    {
        if ($fullPath = $this->getFullPath($relativePath)) {
            $this->add($id, $fullPath, $media, $priority, $inline);
        }
    }

    public function unregisterById($id)
    {
        unset($this->list[$id]);
    }

    public function getList()
    {
        $this->sortList();
        $this->addInlinedStyleContent();

        return $this->list;
    }

    protected function add($id, $fullPath, $media, $priority, $inline)
    {
        if (filesize($fullPath) === 0) {
            return;
        }

        $media = $this->getSanitizedMedia($media);
        $type = ($inline) ? 'inline' : 'external';

        if (!is_int($priority)) {
            $priority = self::DEFAULT_PRIORITY;
        }

        $this->list[$type][$id] = array(
            'id' => $id,
            'type' => $type,
            'path' => $fullPath,
            'uri' => $this->getFQDN().$this->getUriFromPath($fullPath),
            'media' => $media,
            'priority' => $priority,
        );
    }

    private function getSanitizedMedia($media)
    {
        return in_array($media, $this->valid_media, true) ? $media : self::DEFAULT_MEDIA;
    }

    private function sortList()
    {
        foreach ($this->list as $type => &$items) {
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
