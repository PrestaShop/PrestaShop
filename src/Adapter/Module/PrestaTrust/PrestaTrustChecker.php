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

use Doctrine\Common\Cache\Cache;
use PrestaShop\PrestaShop\Adapter\Module\Module;
use Symfony\Component\Finder\Finder;

class PrestaTrustChecker
{
    protected $checked_extensions = array('php', 'js', 'css', 'tpl');

    /**
     * @var Cache
     */
    protected $cache;

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Add the PrestaTrust data for a module, if it exists
     * 
     * @param Module $module
     * @return void
     */
    public function getDetails(Module $module)
    {
        if (!$this->cache->contains($module->get('name'))) {
            return;
        }
        
        $module->set('prestatrust', $this->cache->fetch($module->get('name')));
    }

    /**
     * If the module is compliant, this class checks its content to detect unwanted changes
     *
     * @param Module $module
     * @return void
     */
    public function checkModule(Module $module)
    {
        /*if (!$this->isModuleCompliant($module)) {
            return;
        }*/

        $details = array(
            'hash' => $this->calculateHash($module->disk->get('path')),
        );

        $this->cache->save($module->get('name'), $details);

        $module->set('prestatrust', $details);
    }

    /**
     * Find all files with defined extensions, and calculate hash from their content
     *
     * @param string $path Path to the module root
     * @return string Hash of the module
     */
    protected function calculateHash($path)
    {
        $preparehash = '';

        $finder = Finder::create();
        $finder->files()->in($path);
        foreach ($this->checked_extensions as $ext) {
            $finder->name('*.'.$ext);
        }

        foreach ($finder as $file) {
            if (in_array($file->getExtension(), $this->checked_extensions)) {
                $preparehash .= $file->getContents();
            }
        }
        return hash('sha256', $preparehash);
    }

    /**
     * Check if a module can be checked with PrestaTrust
     *
     * @param Module $module
     * @return boolean
     */
    protected function isModuleCompliant(Module $module)
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

    protected function hasHexPrefix($str)
    {
        $prefix = '0x';
        return substr($str, 0, strlen($prefix)) === $prefix;
    }
}