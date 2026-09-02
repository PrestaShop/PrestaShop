<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
            return $this->configuration->get('_PS_ROOT_DIR_') . preg_replace('#' . preg_quote($trimmedUri) . '#', '', $fullUri, 1);
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
