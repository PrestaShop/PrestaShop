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

namespace PrestaShop\PrestaShop\Core\Email;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;

/**
 * Class SmtpDataConfigurator is responsible for configuring SMTP data.
 */
final class SmtpDataConfigurator implements DataConfigurationInterface
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
            'domain' => $this->configuration->get('PS_MAIL_DOMAIN'),
            'server' => $this->configuration->get('PS_MAIL_SERVER'),
            'username' => $this->configuration->get('PS_MAIL_USER'),
            'password' => $this->configuration->get('PS_MAIL_PASSWD'),
            'encryption' => $this->configuration->get('PS_MAIL_SMTP_ENCRYPTION'),
            'port' => $this->configuration->get('PS_MAIL_SMTP_PORT'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $config)
    {
        if ($this->validateConfiguration($config)) {
            $this->configuration->set('PS_MAIL_DOMAIN', $config['domain']);
            $this->configuration->set('PS_MAIL_SERVER', $config['server']);
            $this->configuration->set('PS_MAIL_USER', $config['username']);
            $this->configuration->set('PS_MAIL_SMTP_ENCRYPTION', $config['encryption']);
            $this->configuration->set('PS_MAIL_SMTP_PORT', $config['port']);

            $smtpPassword = (string) $config['password'];

            if ('' !== $smtpPassword || !$this->configuration->get('PS_MAIL_PASSWD')) {
                $this->configuration->set('PS_MAIL_PASSWD', $smtpPassword);
            }
        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfiguration(array $config)
    {
        return isset(
            $config['domain'],
            $config['server'],
            $config['username'],
            $config['encryption'],
            $config['port'],
            $config['password']
        );
    }
}
