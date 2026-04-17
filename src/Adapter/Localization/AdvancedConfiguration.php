<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Localization;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;

/**
 * Class AdvancedConfiguration is responsible for 'Improve > International > Localization' page
 * 'Advanced' form data.
 */
class AdvancedConfiguration implements DataConfigurationInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @param Configuration $configuration
     */
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
            'language_identifier' => $this->configuration->get('PS_LOCALE_LANGUAGE'),
            'country_identifier' => $this->configuration->get('PS_LOCALE_COUNTRY'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $config)
    {
        $errors = [];

        if ($this->validateConfiguration($config)) {
            $this->configuration->set('PS_LOCALE_LANGUAGE', $config['language_identifier']);
            $this->configuration->set('PS_LOCALE_COUNTRY', $config['country_identifier']);
        }

        return $errors;
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfiguration(array $config)
    {
        return isset(
            $config['language_identifier'],
            $config['country_identifier']
        );
    }
}
