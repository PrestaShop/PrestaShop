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

namespace PrestaShop\PrestaShop\Adapter\Assets;

use Tools as ToolsLegacy;

trait AssetUrlGeneratorTrait
{
    /**
     * @var string
     */
    protected $fqdn;

    /**
     * @param string $fullPath
     *
     * @return string
     */
    protected function getUriFromPath($fullPath)
    {
        return str_replace($this->configuration->get('_PS_ROOT_DIR_'), rtrim($this->configuration->get('__PS_BASE_URI__'), '/'), $fullPath);
    }

    /**
     * @param string $fullUri
     *
     * @return string
     */
    protected function getPathFromUri($fullUri)
    {
        if ('' !== ($trimmedUri = rtrim($this->configuration->get('__PS_BASE_URI__'), '/'))) {
            return $this->configuration->get('_PS_ROOT_DIR_') . preg_replace('#\\' . $trimmedUri . '#', '', $fullUri, 1);
        }

        return $this->configuration->get('_PS_ROOT_DIR_') . $fullUri;
    }

    /**
     * @return string
     */
    protected function getFQDN()
    {
        if (is_null($this->fqdn)) {
            if ($this->configuration->get('PS_SSL_ENABLED') && ToolsLegacy::usingSecureMode()) {
                $this->fqdn = $this->configuration->get('_PS_BASE_URL_SSL_');
            } else {
                $this->fqdn = $this->configuration->get('_PS_BASE_URL_');
            }
        }

        return $this->fqdn;
    }
}
