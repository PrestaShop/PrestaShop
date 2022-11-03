<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
