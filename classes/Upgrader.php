<?php
/**
 * 2007-2018 PrestaShop.
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

/**
 * Class UpgraderCore.
 */
class UpgraderCore
{
    const DEFAULT_CHECK_VERSION_DELAY_HOURS = 24;
    public $rss_version_link;
    public $rss_md5file_link_dir;
    /**
     * @var bool contains true if last version is not installed
     */
    protected $need_upgrade = false;
    protected $changed_files = array();
    protected $missing_files = array();

    public $version_name;
    public $version_num;
    public $version_is_modified = null;
    /**
     * @var string contains hte url where to download the file
     */
    public $link;
    public $autoupgrade;
    public $autoupgrade_module;
    public $autoupgrade_last_version;
    public $autoupgrade_module_link;
    public $changelog;
    public $md5;

    /**
     * UpgraderCore constructor.
     *
     * @param bool $autoload
     */
    public function __construct($autoload = false)
    {
        $this->rss_version_link = _PS_API_URL_ . '/xml/upgrader.xml';
        $this->rss_md5file_link_dir = _PS_API_URL_ . '/xml/md5/';

        if ($autoload) {
            $this->loadFromConfig();
            // checkPSVersion to get need_upgrade
            $this->checkPSVersion();
        }
    }

    public function __get($var)
    {
        if ($var == 'need_upgrade') {
            return $this->isLastVersion();
        }
    }

    /**
     * downloadLast download the last version of PrestaShop and save it in $dest/$filename.
     *
     * @param string $dest directory where to save the file
     * @param string $filename new filename
     *
     * @return bool
     *
     * @TODO ftp if copy is not possible (safe_mode for example)
     */
    public function downloadLast($dest, $filename = 'prestashop.zip')
    {
        if (empty($this->link)) {
            $this->checkPSVersion();
        }

        $destPath = realpath($dest) . DIRECTORY_SEPARATOR . $filename;
        if (@copy($this->link, $destPath)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return bool
     */
    public function isLastVersion()
    {
        if (empty($this->link)) {
            $this->checkPSVersion();
        }

        return $this->need_upgrade;
    }

    /**
     * checkPSVersion ask to prestashop.com if there is a new version. return an array if yes, false otherwise.
     *
     * @return mixed
     */
    public function checkPSVersion($force = false)
    {
        if (class_exists('Configuration')) {
            $lastCheck = Configuration::get('PS_LAST_VERSION_CHECK');
        } else {
            $lastCheck = 0;
        }
        // if we use the autoupgrade process, we will never refresh it
        // except if no check has been done before
        if ($force || ($lastCheck < time() - (3600 * Upgrader::DEFAULT_CHECK_VERSION_DELAY_HOURS))) {
            libxml_set_streams_context(@stream_context_create(array('http' => array('timeout' => 3))));
            if ($feed = @simplexml_load_file($this->rss_version_link)) {
                $this->version_name = (string) $feed->version->name;
                $this->version_num = (string) $feed->version->num;
                $this->link = (string) $feed->download->link;
                $this->md5 = (string) $feed->download->md5;
                $this->changelog = (string) $feed->download->changelog;
                $this->autoupgrade = (int) $feed->autoupgrade;
                $this->autoupgrade_module = (int) $feed->autoupgrade_module;
                $this->autoupgrade_last_version = (string) $feed->autoupgrade_last_version;
                $this->autoupgrade_module_link = (string) $feed->autoupgrade_module_link;
                $this->desc = (string) $feed->desc;
                $configLastVersion = array(
                    'name' => $this->version_name,
                    'num' => $this->version_num,
                    'link' => $this->link,
                    'md5' => $this->md5,
                    'autoupgrade' => $this->autoupgrade,
                    'autoupgrade_module' => $this->autoupgrade_module,
                    'autoupgrade_last_version' => $this->autoupgrade_last_version,
                    'autoupgrade_module_link' => $this->autoupgrade_module_link,
                    'changelog' => $this->changelog,
                    'desc' => $this->desc,
                );
                if (class_exists('Configuration')) {
                    Configuration::updateValue('PS_LAST_VERSION', serialize($configLastVersion));
                    Configuration::updateValue('PS_LAST_VERSION_CHECK', time());
                }
            }
        } else {
            $this->loadFromConfig();
        }
        // retro-compatibility :
        // return array(name,link) if you don't use the last version
        // false otherwise
        if (version_compare(_PS_VERSION_, $this->version_num, '<')) {
            $this->need_upgrade = true;

            return array('name' => $this->version_name, 'link' => $this->link);
        } else {
            return false;
        }
    }

    /**
     * load the last version informations stocked in base.
     *
     * @return Upgrader
     */
    public function loadFromConfig()
    {
        $lastVersionCheck = Tools::unSerialize(Configuration::get('PS_LAST_VERSION'));
        if ($lastVersionCheck) {
            if (isset($lastVersionCheck['name'])) {
                $this->version_name = $lastVersionCheck['name'];
            }
            if (isset($lastVersionCheck['num'])) {
                $this->version_num = $lastVersionCheck['num'];
            }
            if (isset($lastVersionCheck['link'])) {
                $this->link = $lastVersionCheck['link'];
            }
            if (isset($lastVersionCheck['autoupgrade'])) {
                $this->autoupgrade = $lastVersionCheck['autoupgrade'];
            }
            if (isset($lastVersionCheck['autoupgrade_module'])) {
                $this->autoupgrade_module = $lastVersionCheck['autoupgrade_module'];
            }
            if (isset($lastVersionCheck['autoupgrade_last_version'])) {
                $this->autoupgrade_last_version = $lastVersionCheck['autoupgrade_last_version'];
            }
            if (isset($lastVersionCheck['autoupgrade_module_link'])) {
                $this->autoupgrade_module_link = $lastVersionCheck['autoupgrade_module_link'];
            }
            if (isset($lastVersionCheck['md5'])) {
                $this->md5 = $lastVersionCheck['md5'];
            }
            if (isset($lastVersionCheck['desc'])) {
                $this->desc = $lastVersionCheck['desc'];
            }
            if (isset($lastVersionCheck['changelog'])) {
                $this->changelog = $lastVersionCheck['changelog'];
            }
        }

        return $this;
    }

    /**
     * return an array of files
     * that the md5file does not match to the original md5file (provided by $rss_md5file_link_dir ).
     *
     * @return array
     */
    public function getChangedFilesList()
    {
        if (is_array($this->changed_files) && count($this->changed_files) == 0) {
            libxml_set_streams_context(@stream_context_create(array('http' => array('timeout' => 3))));
            $checksum = @simplexml_load_file($this->rss_md5file_link_dir . _PS_VERSION_ . '.xml');
            if ($checksum == false) {
                $this->changed_files = false;
            } else {
                $this->browseXmlAndCompare($checksum->ps_root_dir[0]);
            }
        }

        return $this->changed_files;
    }

    /** populate $this->changed_files with $path
     * in sub arrays  mail, translation and core items.
     *
     * @param string $path filepath to add, relative to _PS_ROOT_DIR_
     */
    protected function addChangedFile($path)
    {
        $this->version_is_modified = true;

        if (strpos($path, 'mails/') !== false) {
            $this->changed_files['mail'][] = $path;
        } elseif (
            strpos($path, '/en.php') !== false
            || strpos($path, '/fr.php') !== false
            || strpos($path, '/es.php') !== false
            || strpos($path, '/it.php') !== false
            || strpos($path, '/de.php') !== false
            || strpos($path, 'translations/') !== false
        ) {
            $this->changed_files['translation'][] = $path;
        } else {
            $this->changed_files['core'][] = $path;
        }
    }

    /** populate $this->missing_files with $path
     *
     * @param string $path filepath to add, relative to _PS_ROOT_DIR_
     */
    protected function addMissingFile($path)
    {
        $this->version_is_modified = true;
        $this->missing_files[] = $path;
    }

    /**
     * @param $node
     * @param array $currentPath
     * @param int $level
     */
    protected function browseXmlAndCompare($node, &$currentPath = array(), $level = 1)
    {
        foreach ($node as $key => $child) {
            /** @var SimpleXMLElement $child */
            if (is_object($child) && $child->getName() == 'dir') {
                $currentPath[$level] = (string) $child['name'];
                $this->browseXmlAndCompare($child, $currentPath, $level + 1);
            } elseif (is_object($child) && $child->getName() == 'md5file') {
                // We will store only relative path.
                // absolute path is only used for file_exists and compare
                $relativePath = '';
                for ($i = 1; $i < $level; ++$i) {
                    $relativePath .= $currentPath[$i] . '/';
                }
                $relativePath .= (string) $child['name'];
                $fullpath = _PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . $relativePath;

                $fullpath = str_replace('ps_root_dir', _PS_ROOT_DIR_, $fullpath);

                // replace default admin dir by current one
                $fullpath = str_replace(_PS_ROOT_DIR_ . '/admin', _PS_ADMIN_DIR_, $fullpath);
                if (!file_exists($fullpath)) {
                    $this->addMissingFile($relativePath);
                } elseif (!$this->compareChecksum($fullpath, (string) $child)) {
                    $this->addChangedFile($relativePath);
                }
                // else, file is original (and ok)
            }
        }
    }

    /**
     * Compare checksum.
     *
     * @param string $path
     * @param string $originalSum
     *
     * @return bool
     */
    protected function compareChecksum($path, $originalSum)
    {
        if (md5_file($path) == $originalSum) {
            return true;
        }

        return false;
    }

    /**
     * Is this an authentic PrestaShop version?
     *
     * @return bool
     */
    public function isAuthenticPrestashopVersion()
    {
        $this->getChangedFilesList();

        return !$this->version_is_modified;
    }
}
