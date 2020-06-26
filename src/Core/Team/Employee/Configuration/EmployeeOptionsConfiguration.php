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

namespace PrestaShop\PrestaShop\Core\Team\Employee\Configuration;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;

/**
 * Class EmployeeOptionsConfiguration handles configuration data for employee options.
 */
final class EmployeeOptionsConfiguration implements DataConfigurationInterface
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var OptionsCheckerInterface
     */
    private $optionsChecker;

    /**
     * @param ConfigurationInterface $configuration
     */
    public function __construct(ConfigurationInterface $configuration, OptionsCheckerInterface $optionsChecker)
    {
        $this->configuration = $configuration;
        $this->optionsChecker = $optionsChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        return [
            'password_change_time' => (int) $this->configuration->get('PS_PASSWD_TIME_BACK'),
            'allow_employee_specific_language' => (int) $this->configuration->get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        $errors = [];

        if (!$this->optionsChecker->canBeChanged()) {
            $errors[] = [
                'key' => 'You cannot change the value of this configuration field in the context of this shop.',
                'parameters' => [],
                'domain' => 'Admin.Notifications.Warning',
            ];

            return $errors;
        }

        if ($this->validateConfiguration($configuration)) {
            $this->configuration->set('PS_PASSWD_TIME_BACK', (int) $configuration['password_change_time']);
            $this->configuration->set(
                'PS_BO_ALLOW_EMPLOYEE_FORM_LANG',
                (bool) $configuration['allow_employee_specific_language']
            );
        }

        return $errors;
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfiguration(array $configuration)
    {
        return isset(
            $configuration['password_change_time'],
            $configuration['allow_employee_specific_language']
        );
    }
}
