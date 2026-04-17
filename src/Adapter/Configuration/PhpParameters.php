<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Configuration;

use InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Util\ArrayFinder;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class able to manage configuration stored in Php files.
 */
class PhpParameters
{
    /**
     * @var ArrayFinder the current configuration
     */
    private $configuration;

    /**
     * @var string the PHP filename
     */
    private $filename;

    public function __construct($filename)
    {
        if (!is_readable($filename)) {
            throw new InvalidArgumentException("File $filename is not readable for configuration");
        }

        $this->filename = $filename;
        $phpArray = require $this->filename;
        $this->configuration = new ArrayFinder($phpArray);
    }

    /**
     * @return array return the complete configuration
     */
    public function getConfiguration()
    {
        return $this->configuration->get();
    }

    /**
     * Insert a value into configuration at the specified path.
     *
     * @param string $propertyPath
     * @param mixed $value
     */
    public function setProperty($propertyPath, $value)
    {
        $this->configuration->set($propertyPath, $value);
    }

    /**
     * Persist the modifications done on the original configuration file.
     *
     * @return bool
     */
    public function saveConfiguration()
    {
        try {
            $filesystem = new Filesystem();
            $filesystem->dumpFile($this->filename, '<?php return ' . var_export($this->configuration->get(), true) . ';' . "\n");

            if (function_exists('opcache_invalidate')) {
                @opcache_invalidate($this->filename);
            }
        } catch (IOException) {
            return false;
        }

        return true;
    }
}
