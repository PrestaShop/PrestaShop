<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Webservice;

use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Manages the configuration data about upload quota options.
 */
final class WebserviceConfiguration extends AbstractMultistoreConfiguration
{
    private const CONFIGURATION_FIELDS = ['enable_webservice', 'enable_cgi'];

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        $shopConstraint = $this->getShopConstraint();

        return [
            'enable_webservice' => (bool) $this->configuration->get('PS_WEBSERVICE', false, $shopConstraint),
            'enable_cgi' => (bool) $this->configuration->get('PS_WEBSERVICE_CGI_HOST', false, $shopConstraint),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        if ($this->validateConfiguration($configuration)) {
            $shopConstraint = $this->getShopConstraint();
            $this->updateConfigurationValue('PS_WEBSERVICE', 'enable_webservice', $configuration, $shopConstraint);
            $this->updateConfigurationValue('PS_WEBSERVICE_CGI_HOST', 'enable_cgi', $configuration, $shopConstraint);
        }

        return [];
    }

    /**
     * @return OptionsResolver
     */
    protected function buildResolver(): OptionsResolver
    {
        $resolver = (new OptionsResolver())
            ->setDefined(self::CONFIGURATION_FIELDS)
            ->setAllowedTypes('enable_webservice', 'bool')
            ->setAllowedTypes('enable_cgi', 'bool');

        return $resolver;
    }
}
