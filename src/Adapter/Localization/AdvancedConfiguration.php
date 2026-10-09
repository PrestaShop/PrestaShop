<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Localization;

use Language;
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
            // A shop upgraded from before these settings existed has no row for them, and the form
            // requires a value, so the constants answer for them until the merchant saves the page.
            'language_pack_url' => $this->configuration->get('PS_LANGUAGE_PACK_URL') ?: Language::SF_LANGUAGE_PACK_URL,
            'emails_pack_url' => $this->configuration->get('PS_EMAILS_PACK_URL') ?: Language::EMAILS_LANGUAGE_PACK_URL,
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
            $this->configuration->set('PS_LANGUAGE_PACK_URL', $config['language_pack_url']);
            $this->configuration->set('PS_EMAILS_PACK_URL', $config['emails_pack_url']);
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
            $config['country_identifier'],
            $config['language_pack_url'],
            $config['emails_pack_url']
        );
    }
}
