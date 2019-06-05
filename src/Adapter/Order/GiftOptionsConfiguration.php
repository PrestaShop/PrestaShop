<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Order;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;

/**
 * Gift Settings configuration available in ShopParameters > Order Preferences.
 */
class GiftOptionsConfiguration implements DataConfigurationInterface
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
            'enable_gift_wrapping' => $this->configuration->getBoolean('PS_GIFT_WRAPPING'),
            'gift_wrapping_price' => $this->configuration->get('PS_GIFT_WRAPPING_PRICE'),
            'gift_wrapping_tax_rules_group' => $this->configuration->get('PS_GIFT_WRAPPING_TAX_RULES_GROUP'),
            'offer_recyclable_pack' => $this->configuration->getBoolean('PS_RECYCLABLE_PACK'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        if ($this->validateConfiguration($configuration)) {
            $this->configuration->set('PS_GIFT_WRAPPING', $configuration['enable_gift_wrapping']);
            $this->configuration->set('PS_GIFT_WRAPPING_PRICE', $configuration['gift_wrapping_price']);
            $this->configuration->set('PS_GIFT_WRAPPING_TAX_RULES_GROUP', $configuration['gift_wrapping_tax_rules_group']);
            $this->configuration->set('PS_RECYCLABLE_PACK', $configuration['offer_recyclable_pack']);
        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfiguration(array $configuration)
    {
        return isset(
            $configuration['enable_gift_wrapping'],
            $configuration['gift_wrapping_price'],
            $configuration['gift_wrapping_tax_rules_group'],
            $configuration['offer_recyclable_pack']
        );
    }
}
