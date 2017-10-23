<?php
/*
 * 2007-2017 PrestaShop
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
 *  @copyright  2007-2017 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Module\PrestaTrust;

use ZipArchive;
use Doctrine\Common\Cache\Cache;
use PrestaShop\PrestaShop\Adapter\Module\Module;
use PrestaShop\PrestaShop\Adapter\Module\ModuleZip;
use PrestaShopBundle\Service\DataProvider\Marketplace\ApiClient;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Translation\TranslatorInterface;

class PrestaTrustChecker
{
    protected $checked_extensions = array('php', 'js', 'css', 'tpl');
    const SMART_CONTRACT_PATTERN = 'prestatrust-license-verification: ';

    const CHECKS_ALL_OK = 'Module is authenticated.';
    const CHECKS_INTEGRITY_NOK = 'Warning, the module has been modified since its purchase from the Addons Marketplace.';
    const CHECKS_PROPERTY_NOK = 'Warning, the purchase proof is invalid. This license has already been used on another shop.';
    const CHECKS_ALL_NOK = 'Warning, the module has been modified and its purchase proof is invalid.';

    /**
     * @var Cache
     */
    protected $cache;

    /**
     * Addons marketplace API client
     * @var ApiClient
     */
    protected $apiClient;

    /**
     * @var string Shop domain
     */
    protected $domain;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @param Cache $cache Cache provider to keep data between two requests
     * @param ApiClient $apiClient Addons Marketplace API client (Guzzle)
     * @param string $shop_info Shop domain (ex.: http://localhost)
     * @param TranslatorInterface $translator Translator for explanation messages
     */
    public function __construct(Cache $cache, ApiClient $apiClient, $shop_info, TranslatorInterface $translator)
    {
        $this->cache = $cache;
        $this->apiClient = $apiClient;
        $this->domain = $shop_info;
        $this->translator = $translator;
    }

    /**
     * If the module is compliant, this class generates and adds all PrestaTrust related details.
     * If not, the module remains untouched. We do not execute checks to avoid slow performances.
     * 
     * @param Module $module
     */
    public function loadDetailsIntoModule(Module $module)
    {
        if (!$this->isCompliant($module)) {
            return;
        }

        if (!$this->cache->contains($module->get('name'))) {
            return;
        }

        $details = $this->cache->fetch($module->get('name'));
        $details->check_list = $this->requestCheck($details->hash, $this->domain, $this->findSmartContrat($module->disk->get('path')));
        $details->status = array_sum($details->check_list) == count($details->check_list); // True if all content is True
        $details->message = $this->translator->trans($this->getMessage($details->check_list), array(), 'Admin.Modules.Notification');
        
        $module->set('prestatrust', $details);
    }

    /**
     * This function, called by the "module download" event, will look at the uploaded zip before its deletion.
     * Looking at the original content (before unzipping) allows us to make sure we do not have altered content
     * or remaining one from another zip.
     * Any module copy pasted in the module folder won't go through this function.
     *
     * @param string $name Module technical name
     * @param string $zipFile Module Zip location
     */
    public function checkModuleZip(ModuleZip $zipFile)
    {
        // Do we need to check something in order to validate only PrestaTrust related modules?

        $details = new \stdClass;
        $details->hash = $this->calculateHash($zipFile->getSource());

        $this->cache->save($zipFile->getName(), $details);
    }

    /**
     * Find all files with defined extensions, and calculate md5 from their content.
     *
     * @param string $zipFile Path to the module Zip file
     * @return string Hash of the module
     */
    protected function calculateHash($zipFile)
    {
        $preparehash = '';
        $zip = new ZipArchive();
        if (true !== $zip->open($zipFile)) {
            return $preparehash;
        }
        
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $stat = $zip->statIndex($i);
            $file_info = pathinfo($stat['name']);

            if (empty($file_info['filename']) || empty($file_info['extension'])) {
                continue;
            }

            if (in_array(trim($file_info['extension']), $this->checked_extensions)) {
                $preparehash .= $zip->getFromName($file_info['dirname'].'/'.$file_info['basename']);
            }
        }
        $zip->close();
        return hash('sha256', $preparehash);
    }

    /**
     * Find and return the smart contract address to be checked with the API.
     * To find the address, we must find a file which matches the pattern "'prestatrust-license-verification:".
     * The address will be found right after it, and must also match the file name.
     *
     * @param string $path Module root path
     * @return string|null Smart contract address, if found.
     */
    public function findSmartContrat($path)
    {
        $finder = Finder::create();
        $finder->files()->contains(self::SMART_CONTRACT_PATTERN)->in($path);

        // Get the first file in the results
        foreach ($finder as $file) {
            $sc = trim(str_replace(self::SMART_CONTRACT_PATTERN, '', $file->getContents()));
            if ($sc === $file->getFilename()) {
                return $sc;
            }
        }
        return null;
    }

    /**
     * Get message to display at the employee. It is used to explain briefly what is PrestaTrust and what
     * went right (or wrong).
     *
     * @param array $check_list
     * @return string Message displayed for confirmation
     */
    protected function getMessage(array $check_list)
    {
        if ($check_list['integrity'] && $check_list['property']) {
            return self::CHECKS_ALL_OK;
        }
        if (!$check_list['integrity'] && $check_list['property']) {
            return self::CHECKS_INTEGRITY_NOK;
        }
        if ($check_list['integrity'] && !$check_list['property']) {
            return self::CHECKS_PROPERTY_NOK;
        }
        return self::CHECKS_ALL_NOK;
    }

    /**
     * Check if a module can be checked with PrestaTrust. To make it compliant, an attribute "author_address"
     * must exist, start with "0x" and be 42 characters long.
     *
     * @param Module $module
     * @return boolean Module compliancy
     */
    protected function isCompliant(Module $module)
    {
        if (!$module->attributes->has('author_address')) {
            return false;
        }

        $address = $module->attributes->get('author_address');

        // Always ensure 0x prefix.
        // Address should be 20bytes=40 HEX-chars + prefix.
        if (!self::hasHexPrefix($address) || strlen($address) !== 42) {
            return false;
        }

        if (!function_exists('ctype_xdigit') || !ctype_xdigit(substr($address, strlen('0x')))) {
            return false;
        }

        return true;
    }

    /**
     * Check that the string starts with '0x'.
     *
     * @param string $str Author address
     * @return bool True if starts with '0x'
     */
    protected function hasHexPrefix($str)
    {
        $prefix = '0x';
        return substr($str, 0, strlen($prefix)) === $prefix;
    }

    /**
     * Send to the Marketplace API our details about the module, and get results
     * about its integrity and property
     *
     * @param string $hash Calculted hash from the modules files
     * @param string $shop_url Shop domain
     * @param string $contract Smart contract address from module
     * @return array of check list results.
     */
    protected function requestCheck($hash, $shop_url, $contract)
    {
        $result = $this->apiClient->setShopUrl($shop_url)->getPrestaTrustCheck($hash, $contract);
        return array(
            'integrity' => (bool)($result->hash_trusted),
            'property' => (bool)($result->property_trusted),
        );
    }
}