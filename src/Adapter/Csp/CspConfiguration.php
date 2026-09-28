<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Csp;

use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Loads and saves the Content Security Policy settings block
 * (Advanced parameters > Security > Content Security Policy).
 */
class CspConfiguration extends AbstractMultistoreConfiguration
{
    private const CONFIGURATION_FIELDS = ['enabled', 'report_only'];

    public function getConfiguration()
    {
        $shopConstraint = $this->getShopConstraint();

        return [
            'enabled' => (bool) $this->configuration->get('PS_CSP_ENABLED', false, $shopConstraint),
            'report_only' => (bool) $this->configuration->get('PS_CSP_REPORT_ONLY', true, $shopConstraint),
        ];
    }

    public function updateConfiguration(array $configuration)
    {
        if ($this->validateConfiguration($configuration)) {
            $shopConstraint = $this->getShopConstraint();
            $this->updateConfigurationValue('PS_CSP_ENABLED', 'enabled', $configuration, $shopConstraint);
            $this->updateConfigurationValue('PS_CSP_REPORT_ONLY', 'report_only', $configuration, $shopConstraint);
        }

        return [];
    }

    protected function buildResolver(): OptionsResolver
    {
        return (new OptionsResolver())
            ->setDefined(self::CONFIGURATION_FIELDS)
            ->setAllowedTypes('enabled', 'bool')
            ->setAllowedTypes('report_only', 'bool');
    }
}
