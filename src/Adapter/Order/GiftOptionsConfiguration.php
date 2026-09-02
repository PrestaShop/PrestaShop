<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Order;

use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Gift Settings configuration available in ShopParameters > Order Preferences.
 */
class GiftOptionsConfiguration extends AbstractMultistoreConfiguration
{
    /**
     * @var array<int, string>
     */
    private const CONFIGURATION_FIELDS = [
        'enable_gift_wrapping',
        'gift_wrapping_price',
        'gift_wrapping_tax_rules_group',
        'offer_recyclable_pack',
    ];

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        $shopConstraint = $this->getShopConstraint();

        return [
            'enable_gift_wrapping' => (bool) $this->configuration->get('PS_GIFT_WRAPPING', false, $shopConstraint),
            'gift_wrapping_price' => (float) $this->configuration->get('PS_GIFT_WRAPPING_PRICE', 0, $shopConstraint),
            'gift_wrapping_tax_rules_group' => (int) $this->configuration->get('PS_GIFT_WRAPPING_TAX_RULES_GROUP', 0, $shopConstraint),
            'offer_recyclable_pack' => (bool) $this->configuration->get('PS_RECYCLABLE_PACK', false, $shopConstraint),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        if ($this->validateConfiguration($configuration)) {
            $shopConstraint = $this->getShopConstraint();

            $this->updateConfigurationValue('PS_GIFT_WRAPPING', 'enable_gift_wrapping', $configuration, $shopConstraint);
            $this->updateConfigurationValue('PS_GIFT_WRAPPING_PRICE', 'gift_wrapping_price', $configuration, $shopConstraint);
            $this->updateConfigurationValue('PS_GIFT_WRAPPING_TAX_RULES_GROUP', 'gift_wrapping_tax_rules_group', $configuration, $shopConstraint);
            $this->updateConfigurationValue('PS_RECYCLABLE_PACK', 'offer_recyclable_pack', $configuration, $shopConstraint);
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
            ->setAllowedTypes('enable_gift_wrapping', 'bool')
            ->setAllowedTypes('gift_wrapping_price', 'float')
            ->setAllowedTypes('gift_wrapping_tax_rules_group', 'int')
            ->setAllowedTypes('offer_recyclable_pack', 'bool');

        return $resolver;
    }
}
