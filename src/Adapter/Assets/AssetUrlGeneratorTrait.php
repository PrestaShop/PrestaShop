<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
            return $this->configuration->get('_PS_ROOT_DIR_') . preg_replace('#\\' . preg_quote($trimmedUri) . '#', '', $fullUri, 1);
        }

        return $this->configuration->get('_PS_ROOT_DIR_') . $fullUri;
    }

    /**
     * @return string
     */
    protected function getFQDN()
    {
        if (null === $this->fqdn) {
            if ($this->configuration->get('PS_SSL_ENABLED') && ToolsLegacy::usingSecureMode()) {
                $this->fqdn = $this->configuration->get('_PS_BASE_URL_SSL_');
            } else {
                $this->fqdn = $this->configuration->get('_PS_BASE_URL_');
            }
        }

        return $this->fqdn;
    }
}
