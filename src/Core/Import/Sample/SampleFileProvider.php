<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Import\Sample;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Class SampleFileProvider is responsible for providing sample import files.
 */
final class SampleFileProvider implements SampleFileProviderInterface
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
     * {@inheritdoc}
     */
    public function getFile($sampleFileName)
    {
        $path = $this->configuration->get('_PS_ROOT_DIR_') .
              DIRECTORY_SEPARATOR .
              'docs' .
              DIRECTORY_SEPARATOR .
              'csv_import' .
              DIRECTORY_SEPARATOR;

        try {
            $sampleFile = new File($path . $sampleFileName . '.csv');
        } catch (FileNotFoundException) {
            return null;
        }

        return $sampleFile;
    }
}
