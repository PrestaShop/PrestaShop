<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Requirement;

use PrestaShop\PrestaShop\Core\Version;
use SimpleXMLElement;
use Tools;

/**
 * Part of requirements for a PrestaShop website
 * Check if all required files exists.
 */
class CheckMissingOrUpdatedFiles
{
    /**
     * Seconds to wait for the checksum list. The list weighs a few megabytes, so this is
     * generous, but it is bounded: without it the download inherits default_socket_timeout
     * and a stalled connection holds the request open far longer than the page can survive.
     */
    private const CHECKSUM_LIST_TIMEOUT = 30;

    /**
     * @param SimpleXMLElement|null $dir
     * @param string $path
     *
     * @return array{missing: string[], updated: string[], error: bool} `error` is true when the
     *                                                                  checksum list could not be
     *                                                                  read, which means nothing
     *                                                                  was verified
     */
    public function getListOfUpdatedFiles($dir = null, $path = '')
    {
        if (null === $dir) {
            $dir = $this->loadChecksumList();

            if (null === $dir) {
                // Reporting an empty list here would tell the merchant that every file matches,
                // when in fact not a single one was compared.
                return ['missing' => [], 'updated' => [], 'error' => true];
            }
        }

        return $this->compareWithChecksumList($dir, $path) + ['error' => false];
    }

    /**
     * @return SimpleXMLElement|null null when the list is unreachable or malformed
     */
    protected function loadChecksumList()
    {
        $url = _PS_API_URL_ . '/xml/md5-' . Version::MAJOR_VERSION . '/' . Version::VERSION . '.xml';
        $body = Tools::file_get_contents($url, false, null, self::CHECKSUM_LIST_TIMEOUT);

        if (!is_string($body) || '' === $body) {
            return null;
        }

        $xml = @simplexml_load_string($body);

        if (false === $xml || !isset($xml->ps_root_dir[0])) {
            return null;
        }

        return $xml->ps_root_dir[0];
    }

    /**
     * @param SimpleXMLElement $dir
     * @param string $path
     *
     * @return array{missing: string[], updated: string[]}
     */
    private function compareWithChecksumList(SimpleXMLElement $dir, string $path): array
    {
        $fileList = [
            'missing' => [],
            'updated' => [],
        ];

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
            $fileList = array_merge_recursive(
                $fileList,
                $this->compareWithChecksumList($subdir, $path . $subdir['name'] . '/')
            );
        }

        return $fileList;
    }
}
