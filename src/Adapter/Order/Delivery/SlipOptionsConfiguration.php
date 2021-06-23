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

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;

/**
 * This class manages Order delivery slip options configuration.
 */
final class SlipOptionsConfiguration implements DataConfigurationInterface
{
    public const PREFIX = 'PS_DELIVERY_PREFIX';
    public const NUMBER = 'PS_DELIVERY_NUMBER';
    public const ENABLE_PRODUCT_IMAGE = 'PS_PDF_IMG_DELIVERY';

    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Returns configuration used to manage slip options in back office.
     *
     * @return array
     */
    public function getConfiguration()
    {
        return [
            'prefix' => $this->configuration->get(self::PREFIX),
            'number' => $this->configuration->getInt(self::NUMBER),
            'enable_product_image' => $this->configuration->getBoolean(self::ENABLE_PRODUCT_IMAGE),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        if ($this->validateConfiguration($configuration)) {
            $this->configuration->set(self::PREFIX, $configuration['prefix']);
            $this->configuration->set(self::NUMBER, $configuration['number']);
            $this->configuration->set(self::ENABLE_PRODUCT_IMAGE, $configuration['enable_product_image']);
        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfiguration(array $configuration)
    {
        return isset(
            $configuration['prefix'],
            $configuration['number'],
            $configuration['enable_product_image']
        );
    }
}
