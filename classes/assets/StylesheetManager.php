<?php
/*
* 2007-2015 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
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

    public function register($id, $relativePath, $media = self::DEFAULT_MEDIA, $priority = self::DEFAULT_PRIORITY)
    {
        if ($fullPath = $this->getFullPath($relativePath)) {
            $this->add($id, $fullPath, $this->getMedia($media), $priority);
        }
    }

    protected function add($id, $fullPath, $media, $priority)
    {
        if (filesize($fullPath) === 0) {
            return;
        }

        $this->list[$id] = array(
            'id' => $id,
            'media' => $media,
            'uri' => $this->getFQDN().$this->getUriFromPath($fullPath),
            'priority' => $priority,
        );
    }

    private function getMedia($media)
    {
        return in_array($media, $this->valid_media) ? $media : self::DEFAULT_MEDIA;
    }
}
