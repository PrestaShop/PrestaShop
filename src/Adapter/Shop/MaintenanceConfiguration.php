<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Shop;

use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This class loads and saves data configuration for the Maintenance page.
 */
class MaintenanceConfiguration extends AbstractMultistoreConfiguration
{
    /**
     * @var array<int, string>
     */
    private const CONFIGURATION_FIELDS = ['enable_shop', 'maintenance_allow_admins', 'maintenance_ip', 'maintenance_text'];

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        $shopConstraint = $this->getShopConstraint();

        return [
            'enable_shop' => (bool) $this->configuration->get('PS_SHOP_ENABLE', false, $shopConstraint),
            'maintenance_allow_admins' => (bool) $this->configuration->get('PS_MAINTENANCE_ALLOW_ADMINS', false, $shopConstraint),
            'maintenance_ip' => $this->configuration->get('PS_MAINTENANCE_IP', null, $shopConstraint),
            'maintenance_text' => $this->configuration->get('PS_MAINTENANCE_TEXT', null, $shopConstraint),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configurationInputValues)
    {
        if ($this->validateConfiguration($configurationInputValues)) {
            $shopConstraint = $this->getShopConstraint();

            $this->updateConfigurationValue('PS_SHOP_ENABLE', 'enable_shop', $configurationInputValues, $shopConstraint);
            $this->updateConfigurationValue('PS_MAINTENANCE_ALLOW_ADMINS', 'maintenance_allow_admins', $configurationInputValues, $shopConstraint);
            $this->updateConfigurationValue('PS_MAINTENANCE_IP', 'maintenance_ip', $configurationInputValues, $shopConstraint);
            $this->updateConfigurationValue('PS_MAINTENANCE_TEXT', 'maintenance_text', $configurationInputValues, $shopConstraint, ['html' => true]);
        }

        return [];
    }

    /**
     * @return OptionsResolver
     */
    protected function buildResolver(): OptionsResolver
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined(self::CONFIGURATION_FIELDS);
        $resolver->setAllowedTypes('enable_shop', 'bool');
        $resolver->setAllowedTypes('maintenance_allow_admins', 'bool');
        $resolver->setAllowedTypes('maintenance_ip', ['string', 'null']);
        $resolver->setAllowedTypes('maintenance_text', ['array', 'null']);

        return $resolver;
    }
}
