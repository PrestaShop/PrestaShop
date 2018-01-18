<?php
/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Product;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StockConfiguration implements DataConfigurationInterface
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        return [
            'allow_ordering_oos' => $this->configuration->get('PS_ORDER_OUT_OF_STOCK'),
            'stock_management' => $this->configuration->get('PS_STOCK_MANAGEMENT'),
            'in_stock_label' => $this->configuration->get('PS_LABEL_IN_STOCK_PRODUCTS'),
            'oos_allowed_backorders' => $this->configuration->get('PS_LABEL_OOS_PRODUCTS_BOA'),
            'oos_denied_backorders' => $this->configuration->get('PS_LABEL_OOS_PRODUCTS_BOD'),
            'delivery_time' => (array) $this->configuration->get('PS_LABEL_DELIVERY_TIME_AVAILABLE'),
            'oos_delivery_time' => (array) $this->configuration->get('PS_LABEL_DELIVERY_TIME_OOSBOA'),
            'pack_stock_management' => $this->configuration->get('PS_PACK_STOCK_TYPE'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $config)
    {
        $errors = [];

        if ($this->validateConfiguration($config)) {
            $this->configuration->set('PS_ORDER_OUT_OF_STOCK', (int) $config['allow_ordering_oos']);
            $this->configuration->set('PS_STOCK_MANAGEMENT', (int) $config['stock_management']);
            $this->configuration->set('PS_LABEL_IN_STOCK_PRODUCTS', $config['in_stock_label']);
            $this->configuration->set('PS_LABEL_OOS_PRODUCTS_BOA', $config['oos_allowed_backorders']);
            $this->configuration->set('PS_LABEL_OOS_PRODUCTS_BOD', $config['oos_denied_backorders']);
            $this->configuration->set('PS_LABEL_DELIVERY_TIME_AVAILABLE', $config['delivery_time']);
            $this->configuration->set('PS_LABEL_DELIVERY_TIME_OOSBOA', $config['oos_delivery_time']);
            $this->configuration->set('PS_PACK_STOCK_TYPE', $config['pack_stock_management']);
        }

        return $errors;
    }

    /**
     *
     * {@inheritdoc}
     */
    public function validateConfiguration(array $configuration)
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired([
            'allow_ordering_oos',
            'stock_management',
            'in_stock_label',
            'delivery_time',
            'oos_allowed_backorders',
            'oos_delivery_time',
            'oos_denied_backorders',
            'pack_stock_management',
        ]);

        $resolver->resolve($configuration);

        return true;
    }
}
