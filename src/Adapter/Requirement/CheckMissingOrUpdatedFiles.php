<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Requirement;

use PrestaShop\PrestaShop\Core\Version;

/**
 * Part of requirements for a PrestaShop website
 * Check if all required files exists.
 */
class CheckMissingOrUpdatedFiles
{
    /**
     * @param string|null $dir
     * @param string $path
     *
     * @return array
     */
    public function getListOfUpdatedFiles($dir = null, $path = '')
    {
        $fileList = [
            'missing' => [],
            'updated' => [],
        ];

        if (null === $dir) {
            $xml = @simplexml_load_file(_PS_API_URL_ . '/xml/md5-' . Version::MAJOR_VERSION . '/' . Version::VERSION . '.xml');
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
            } elseif (md5_file(_PS_ROOT_DIR_ . '/' . $filename) !== (string) $file) {
                $fileList['updated'][] = $filename;
            }
        }

        foreach ($dir->dir as $subdir) {
            $fileList = array_merge_recursive($fileList, $this->getListOfUpdatedFiles($subdir, $path . $subdir['name'] . '/'));
        }

        return $fileList;
    }
}
