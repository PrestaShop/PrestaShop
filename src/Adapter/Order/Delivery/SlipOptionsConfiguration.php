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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Adapter\Order\Delivery;

use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This class manages Order delivery slip options configuration.
 */
final class SlipOptionsConfiguration extends AbstractMultistoreConfiguration
{
    public const PREFIX = 'PS_DELIVERY_PREFIX';
    public const NUMBER = 'PS_DELIVERY_NUMBER';
    public const ENABLE_PRODUCT_IMAGE = 'PS_PDF_IMG_DELIVERY';
    private const CONFIGURATION_FIELDS = ['prefix', 'number', 'enable_product_image'];

    /**
     * Returns configuration used to manage slip options in back office.
     *
     * @return array
     */
    public function getConfiguration()
    {
        return [
            'prefix' => (array) $this->configuration->get(self::PREFIX, null, $this->getShopConstraint()),
            'number' => (int) $this->configuration->get(self::NUMBER, 0, $this->getShopConstraint()),
            'enable_product_image' => (bool) $this->configuration->get(self::ENABLE_PRODUCT_IMAGE, false, $this->getShopConstraint()),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        if ($this->validateConfiguration($configuration)) {
            $this->updateConfigurationValue(self::PREFIX, 'prefix', $configuration, $this->getShopConstraint());
            $this->updateConfigurationValue(self::NUMBER, 'number', $configuration, $this->getShopConstraint());
            $this->updateConfigurationValue(self::ENABLE_PRODUCT_IMAGE, 'enable_product_image', $configuration, $this->getShopConstraint());
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
            ->setAllowedTypes('prefix', 'array')
            ->setAllowedTypes('number', 'int')
            ->setAllowedTypes('enable_product_image', 'bool');

        return $resolver;
    }
}
