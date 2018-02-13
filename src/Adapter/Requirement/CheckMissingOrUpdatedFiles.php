<?php
/**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Requirement;

/**
 * Part of requirements for a PrestaShop website
 * Check if all required files exists.
 */
class CheckMissingOrUpdatedFiles
{
    /**
     * @return array
     */
    public function getListOfUpdatedFiles($dir = null, $path = '')
    {
        $fileList = array(
            'missing' => array(),
            'updated' => array(),
        );
        
        if (is_null($dir)) {
            $xml = @simplexml_load_file(_PS_API_URL_.'/xml/md5/'._PS_VERSION_.'.xml');
            if (!$xml) {
                return $fileList;
            }

            $dir = $xml->ps_root_dir[0];
        }

        $excludeRegexp = '(install(-dev|-new)?|themes|tools|cache|docs|download|img|localization|log|mails|translations|upload|modules|override/(:?.*)index.php$)';
        $adminDir = basename(_PS_ADMIN_DIR_);

        foreach ($dir->md5file as $file) {
            $filename = preg_replace('#^admin/#', $adminDir . '/', $path . $file['name']);
            if (preg_match('#^' . $excludeRegexp . '#', $filename)) {
                continue;
            }

            if (!file_exists(_PS_ROOT_DIR_ . '/' . $filename)) {
                $fileList['missing'][] = $filename;
            } elseif (md5_file(_PS_ROOT_DIR_ . '/' . $filename) !== (string)$file) {
                $fileList['updated'][] = $filename;
            }
        }

        foreach ($dir->dir as $subdir) {
            $this->getListOfUpdatedFiles($subdir, $path.$subdir['name'].'/');
        }

        return $fileList;
    }
}
