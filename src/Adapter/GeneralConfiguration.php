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

namespace PrestaShop\PrestaShop\Adapter;

use Cookie;
use PrestaShop\PrestaShop\Adapter\Addons\AddonsDataProvider;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;

/**
 * Manages the configuration data about general options.
 */
class GeneralConfiguration implements DataConfigurationInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var Cookie
     */
    private $cookie;

    /**
     * @var bool
     */
    private $isDebug;

    /**
     * @param Configuration $configuration
     * @param Cookie $cookie
     * @param bool|null $isDebug
     */
    public function __construct(Configuration $configuration, Cookie $cookie, bool $isDebug = null)
    {
        $this->configuration = $configuration;
        $this->cookie = $cookie;
        $this->isDebug = $isDebug === null ? (defined('_PS_MODE_DEV_') ? _PS_MODE_DEV_ : true) : $isDebug;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        $configuration = [
            'check_modules_update' => $this->configuration->getBoolean('PRESTASTORE_LIVE'),
            'check_ip_address' => $this->configuration->getBoolean('PS_COOKIE_CHECKIP'),
            'front_cookie_lifetime' => $this->configuration->get('PS_COOKIE_LIFETIME_FO'),
            'back_cookie_lifetime' => $this->configuration->get('PS_COOKIE_LIFETIME_BO'),
            'cookie_samesite' => $this->configuration->get('PS_COOKIE_SAMESITE'),
        ];
        if ($this->isDebug) {
            $configuration['check_modules_stability_channel'] = $this->configuration->get(
                'ADDONS_API_MODULE_CHANNEL',
                AddonsDataProvider::ADDONS_API_MODULE_CHANNEL_STABLE
            );
        }

        return $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        $errors = [];

        if ($this->validateConfiguration($configuration)) {
            if (!$this->validateSameSite($configuration['cookie_samesite'])) {
                $errors[] = [
                    'key' => 'The SameSite=None is only available in secure mode.',
                    'domain' => 'Admin.Advparameters.Notification',
                    'parameters' => [],
                ];
            } else {
                $this->configuration->set('PRESTASTORE_LIVE', (bool) $configuration['check_modules_update']);
                $this->configuration->set('PS_COOKIE_CHECKIP', (bool) $configuration['check_ip_address']);
                $this->configuration->set('PS_COOKIE_LIFETIME_FO', (int) $configuration['front_cookie_lifetime']);
                $this->configuration->set('PS_COOKIE_LIFETIME_BO', (int) $configuration['back_cookie_lifetime']);
                $this->configuration->set('PS_COOKIE_SAMESITE', $configuration['cookie_samesite']);
                if ($this->isDebug) {
                    $this->configuration->set('ADDONS_API_MODULE_CHANNEL', $configuration['check_modules_stability_channel']);
                }
                // Clear checksum to force the refresh
                $this->cookie->checksum = '';
                $this->cookie->write();
            }
        }

        return $errors;
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfiguration(array $configuration)
    {
        $isValid = isset(
                $configuration['check_modules_update'],
                $configuration['check_ip_address'],
                $configuration['front_cookie_lifetime'],
                $configuration['back_cookie_lifetime']
            ) && in_array(
                $configuration['cookie_samesite'],
                Cookie::SAMESITE_AVAILABLE_VALUES
            );
        if ($this->isDebug) {
            $isValid &= in_array(
                $configuration['check_modules_stability_channel'],
                AddonsDataProvider::ADDONS_API_MODULE_CHANNELS
            );
        }

        return (bool) $isValid;
    }

    /**
     * Validate SameSite.
     * The SameSite=None is only working when Secure is settled
     *
     * @param string $sameSite
     *
     * @return bool
     */
    protected function validateSameSite(string $sameSite): bool
    {
        $forceSsl = $this->configuration->get('PS_SSL_ENABLED') && $this->configuration->get('PS_SSL_ENABLED_EVERYWHERE');
        if ($sameSite === Cookie::SAMESITE_NONE) {
            return $forceSsl;
        }

        return true;
    }
}
