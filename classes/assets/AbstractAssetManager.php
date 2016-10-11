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

use \PrestaShop\PrestaShop\Core\ConfigurationInterface;

abstract class AbstractAssetManagerCore
{
    protected $directories;
    protected $configuration;
    protected $list = array();
    protected $fqdn;

    const DEFAULT_MEDIA = 'all';
    const DEFAULT_PRIORITY = 50;
    const JS_BOTTOM = true;

    public function __construct(array $directories, ConfigurationInterface $configuration)
    {
        $this->directories = $directories;
        $this->configuration = $configuration;
    }

    public function getList()
    {
        uasort($this->list, function ($a, $b) {
            if ($a['priority'] === $b['priority']) {
                return 0;
            }
            return ($a['priority'] < $b['priority']) ? -1 : 1;
        });

        return $this->list;
    }

    protected function getFullPath($relativePath)
    {
        foreach ($this->directories as $baseDir) {
            $fullPath = realpath($baseDir.'/'.$relativePath);
            if (is_file($fullPath)) {
                return $fullPath;
            }
        }
    }

    protected function getUriFromPath($fullPath)
    {
        $uri = str_replace(
            $this->configuration->get('_PS_ROOT_DIR_').'/',
            $this->configuration->get('__PS_BASE_URI__'),
            $fullPath
        );

        return $uri;
    }

    protected function getFQDN()
    {
        if (is_null($this->fqdn)) {
            $this->fqdn = ($this->configuration->get('PS_SSL_ENABLED'))
                ? $this->configuration->get('_PS_BASE_URL_SSL_')
                : $this->configuration->get('_PS_BASE_URL_');
        }

        return $this->fqdn;
    }
}
