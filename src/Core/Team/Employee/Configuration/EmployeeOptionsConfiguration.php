<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
