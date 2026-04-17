<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Smarty;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;

/**
 * This class will manage Smarty configuration for a Shop.
 */
class SmartyCacheConfiguration implements DataConfigurationInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        return [
            'template_compilation' => $this->configuration->get('PS_SMARTY_FORCE_COMPILE'),
            'cache' => $this->configuration->getBoolean('PS_SMARTY_CACHE'),
            'multi_front_optimization' => $this->configuration->getBoolean('PS_SMARTY_LOCAL'),
            'clear_cache' => $this->configuration->get('PS_SMARTY_CLEAR_CACHE'),
            'smarty_console' => $this->configuration->get('PS_SMARTY_CONSOLE'),
            'smarty_console_key' => $this->configuration->get('PS_SMARTY_CONSOLE_KEY'),
        ];
    }

    /**
     * {@inheritdoc}
     *
     * Note: 'smarty_console' and 'smarty_console_key' keys are not allowed for update.
     */
    public function updateConfiguration(array $configuration)
    {
        $errors = [];
        if ($this->validateConfiguration($configuration)) {
            $this->configuration->set('PS_SMARTY_FORCE_COMPILE', $configuration['template_compilation']);
            $this->configuration->set('PS_SMARTY_CACHE', $configuration['cache']);
            $this->configuration->set('PS_SMARTY_LOCAL', $configuration['multi_front_optimization']);
            $this->configuration->set('PS_SMARTY_CLEAR_CACHE', $configuration['clear_cache']);
        }

        return $errors;
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfiguration(array $configuration)
    {
        return isset(
            $configuration['template_compilation'],
            $configuration['cache'],
            $configuration['multi_front_optimization'],
            $configuration['clear_cache'],
            $configuration['smarty_console'],
            $configuration['smarty_console_key']
        );
    }
}
