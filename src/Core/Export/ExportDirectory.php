<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Export;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;

/**
 * ImportDirectory class is responsible for returning export directory & data related to it.
 */
final class ExportDirectory
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @param ConfigurationInterface $configuration
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Get path to import directory.
     *
     * @return string
     */
    public function getDir()
    {
        return $this->configuration->get('_PS_ADMIN_DIR_') . DIRECTORY_SEPARATOR . 'export' . DIRECTORY_SEPARATOR;
    }

    /**
     * Use export directory object as a string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getDir();
    }
}
