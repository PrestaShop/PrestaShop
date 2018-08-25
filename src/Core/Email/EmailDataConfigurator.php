<?php
/**
 * 2007-2018 PrestaShop.
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

namespace PrestaShop\PrestaShop\Core\Email;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;

/**
 * Class EmailDataConfigurator is responsible for configuring email data.
 */
final class EmailDataConfigurator implements DataConfigurationInterface
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @param ConfigurationInterface $configuration
     */
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
            'send_emails_to' => $this->configuration->get('PS_MAIL_EMAIL_MESSAGE'),
            'mail_method' => (int) $this->configuration->get('PS_MAIL_METHOD'),
            'mail_type' => (int) $this->configuration->get('PS_MAIL_TYPE'),
            'log_emails' => (bool) $this->configuration->get('PS_LOG_EMAILS'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $config)
    {
        if ($this->validateConfiguration($config)) {
            $this->configuration->set('PS_MAIL_EMAIL_MESSAGE', $config['send_emails_to']);
            $this->configuration->set('PS_MAIL_METHOD', $config['mail_method']);
            $this->configuration->set('PS_MAIL_TYPE', $config['mail_type']);
            $this->configuration->set('PS_LOG_EMAILS', $config['log_emails']);
        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfiguration(array $config)
    {
        return isset(
            $config['send_emails_to'],
            $config['mail_method'],
            $config['mail_type'],
            $config['log_emails']
        );
    }
}
