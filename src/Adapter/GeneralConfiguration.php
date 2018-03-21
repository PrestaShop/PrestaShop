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
namespace PrestaShop\PrestaShop\Adapter;

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
            'check_modules_update' => $this->configuration->getBoolean('PRESTASTORE_LIVE'),
            'check_ip_address' => $this->configuration->getBoolean('PS_COOKIE_CHECKIP'),
            'front_cookie_lifetime' => $this->configuration->get('PS_COOKIE_LIFETIME_FO'),
            'back_cookie_lifetime' => $this->configuration->get('PS_COOKIE_LIFETIME_BO'),
        );
    }

    /**
     * @{inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        $errors = array();

        if ($this->validateConfiguration($configuration)) {
            $this->configuration->set('PRESTASTORE_LIVE', (bool) $configuration['check_modules_update']);
            $this->configuration->set('PS_COOKIE_CHECKIP', (bool) $configuration['check_ip_address']);
            $this->configuration->set('PS_COOKIE_LIFETIME_FO', (int) $configuration['front_cookie_lifetime']);
            $this->configuration->set('PS_COOKIE_LIFETIME_BO', (int) $configuration['back_cookie_lifetime']);
        }

        return $errors;
    }

    /**
     * @{inheritdoc}
     */
    public function validateConfiguration(array $configuration)
    {
        return isset(
            $configuration['check_modules_update'],
            $configuration['check_ip_address'],
            $configuration['front_cookie_lifetime'],
            $configuration['back_cookie_lifetime']
        );
    }
}
