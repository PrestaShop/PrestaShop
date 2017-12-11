<?php
/*
 * 2007-2017 PrestaShop
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 *  @author PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2017 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Shop;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MaintenanceConfiguration implements DataConfigurationInterface
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
        return array(
            'disable_shop' => $this->configuration->get('PS_SHOP_ENABLE'),
            'maintenance_ip' => $this->configuration->get('PS_MAINTENANCE_IP'),
            'maintenance_text' => $this->configuration->get('PS_MAINTENANCE_TEXT'),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        $errors = array();

        if ($this->validateConfiguration($configuration)) {
            $this->configuration->set('PS_SHOP_ENABLE', $configuration['disable_shop']);
            $this->configuration->set('PS_MAINTENANCE_IP', $configuration['maintenance_ip']);
            $this->configuration->set('PS_MAINTENANCE_TEXT', $configuration['maintenance_text']);
        }

        return $errors;
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfiguration(array $configuration)
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired(
            array(
                'disable_shop',
                'maintenance_ip',
                'maintenance_text',
            )
        );
        $resolver->resolve($configuration);

        return true;
    }
}
