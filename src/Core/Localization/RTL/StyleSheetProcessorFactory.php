<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Localization\RTL;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;

/**
 * Class StyleSheetProcessorFactory
 */
final class StyleSheetProcessorFactory implements StyleSheetProcessorFactoryInterface
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
    public function create()
    {
        $rootDir = $this->configuration->get('_PS_ROOT_DIR_');
        $moduleDir = $this->configuration->get('_PS_MODULE_DIR_');

        if (null === $adminDir = $this->configuration->get('_PS_ADMIN_DIR_')) {
            $adminDir = $rootDir . DIRECTORY_SEPARATOR . 'admin';
            $adminDir = is_dir($adminDir) ? $adminDir : ($adminDir . '-dev');
        }

        $themesDir = $this->configuration->get('_PS_ROOT_DIR_') . DIRECTORY_SEPARATOR . 'themes';

        return new Processor(
            $adminDir,
            $themesDir,
            []
        );
    }
}
