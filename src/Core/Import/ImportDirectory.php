<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Import;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * ImportDirectory class is responsible for returning import directory & data related to it.
 */
final class ImportDirectory
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
        return $this->configuration->get('_PS_ADMIN_DIR_') . DIRECTORY_SEPARATOR . 'import' . DIRECTORY_SEPARATOR;
    }

    /**
     * Check if import directory exists.
     *
     * @return bool
     */
    public function exists()
    {
        return (new Filesystem())->exists($this->getDir());
    }

    /**
     * Check if import directory is writable.
     *
     * @return bool
     */
    public function isWritable()
    {
        return is_writable($this->getDir());
    }

    /**
     * Check if import directory is readable.
     *
     * @return bool
     */
    public function isReadable()
    {
        return is_readable($this->getDir());
    }

    /**
     * Use import directory object as a string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getDir();
    }
}
