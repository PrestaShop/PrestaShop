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
namespace PrestaShop\PrestaShop\Adapter\Admin;

use PrestaShop\PrestaShop\Adapter\Configuration;
use Symfony\Component\OptionsResolver\OptionsResolver;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;

class NotificationsConfiguration implements DataConfigurationInterface
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
     * @{inheritdoc}
     */
    public function getConfiguration()
    {
        return array(
            'show_notifs_new_orders' => (bool)$this->configuration->get('PS_SHOW_NEW_ORDERS'),
            'show_notifs_new_customers' => (bool)$this->configuration->get('PS_SHOW_NEW_CUSTOMERS'),
            'show_notifs_new_messages' => (bool)$this->configuration->get('PS_SHOW_NEW_MESSAGES'),
        );
    }

    /**
     * @{inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        $errors = array();

        if ($this->validateConfiguration($configuration)) {
            $this->configuration->set('PS_SHOW_NEW_ORDERS', (bool) $configuration['show_notifs_new_orders']);
            $this->configuration->set('PS_SHOW_NEW_CUSTOMERS', (bool) $configuration['show_notifs_new_customers']);
            $this->configuration->set('PS_SHOW_NEW_MESSAGES', (bool) $configuration['show_notifs_new_messages']);
        }

        return $errors;
    }

    /**
     * @{inheritdoc}
     */
    public function validateConfiguration(array $configuration)
    {
        $resolver = new OptionsResolver();
        $resolver
            ->setRequired(
                array(
                    'show_notifs_new_orders',
                    'show_notifs_new_customers',
                    'show_notifs_new_messages',
                )
            );
        $resolver->resolve($configuration);

        return true;
    }
}
