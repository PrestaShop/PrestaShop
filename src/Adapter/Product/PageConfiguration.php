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

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PageConfiguration implements DataConfigurationInterface
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
            'display_quantities' => $this->configuration->get('PS_DISPLAY_QTIES'),
            'display_last_quantities' => $this->configuration->get('PS_LAST_QTIES'),
            'display_unavailable_attributes' => $this->configuration->get('PS_DISP_UNAVAILABLE_ATTR'),
            'allow_add_variant_to_cart_from_listing' => $this->configuration->get('PS_ATTRIBUTE_CATEGORY_DISPLAY'),
            'attribute_anchor_separator' => $this->configuration->get('PS_ATTRIBUTE_ANCHOR_SEPARATOR'),
            'display_discount_price' => $this->configuration->get('PS_DISPLAY_DISCOUNT_PRICE'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $config)
    {
        $errors = [];

        if ($this->validateConfiguration($config)) {
            $this->configuration->set('PS_DISPLAY_QTIES', (int) $config['display_quantities']);
            $this->configuration->set('PS_LAST_QTIES', (int) $config['display_last_quantities']);
            $this->configuration->set('PS_DISP_UNAVAILABLE_ATTR', (int) $config['display_unavailable_attributes']);
            $this->configuration->set('PS_ATTRIBUTE_CATEGORY_DISPLAY', (int) $config['allow_add_variant_to_cart_from_listing']);
            $this->configuration->set('PS_ATTRIBUTE_ANCHOR_SEPARATOR', $config['attribute_anchor_separator']);
            $this->configuration->set('PS_DISPLAY_DISCOUNT_PRICE', (int) $config['display_discount_price']);
        }

        return $errors;
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfiguration(array $config)
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired([
            'display_quantities',
            'display_last_quantities',
            'display_unavailable_attributes',
            'allow_add_variant_to_cart_from_listing',
            'attribute_anchor_separator',
            'display_discount_price',
        ]);

        $resolver->resolve($config);

        return true;
    }
}
