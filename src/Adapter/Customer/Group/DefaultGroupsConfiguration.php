<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Customer\Group;

use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DefaultGroupsConfiguration extends AbstractMultistoreConfiguration
{
    private const CONFIGURATION_FIELDS = [
        'unidentified_group',
        'guest_group',
        'customer_group',
    ];

    public function getConfiguration(): array
    {
        $shopConstraint = $this->getShopConstraint();

        return [
            'unidentified_group' => (int) $this->configuration->get('PS_UNIDENTIFIED_GROUP', null, $shopConstraint),
            'guest_group' => (int) $this->configuration->get('PS_GUEST_GROUP', null, $shopConstraint),
            'customer_group' => (int) $this->configuration->get('PS_CUSTOMER_GROUP', null, $shopConstraint),
        ];
    }

    public function updateConfiguration(array $configuration): array
    {
        if ($this->validateConfiguration($configuration)) {
            $shopConstraint = $this->getShopConstraint();

            $this->updateConfigurationValue('PS_UNIDENTIFIED_GROUP', 'unidentified_group', $configuration, $shopConstraint);
            $this->updateConfigurationValue('PS_GUEST_GROUP', 'guest_group', $configuration, $shopConstraint);
            $this->updateConfigurationValue('PS_CUSTOMER_GROUP', 'customer_group', $configuration, $shopConstraint);
        }

        return [];
    }

    protected function buildResolver(): OptionsResolver
    {
        return (new OptionsResolver())
            ->setDefined(self::CONFIGURATION_FIELDS)
            ->setAllowedTypes('unidentified_group', 'int')
            ->setAllowedTypes('guest_group', 'int')
            ->setAllowedTypes('customer_group', 'int')
        ;
    }
}
