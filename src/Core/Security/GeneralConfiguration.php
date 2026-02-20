<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Security;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShopBundle\Entity\Repository\EmployeeRepository;

/**
 * Responsible for saving configuration options for security
 */
class GeneralConfiguration implements DataConfigurationInterface
{
    public function __construct(
        private readonly ConfigurationInterface $configuration,
        private readonly EmployeeRepository $employeeRepository,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        return [
            'token' => $this->configuration->get('PS_SECURITY_TOKEN'),
            '2fa' => $this->configuration->get('PS_BACKOFFICE_2FA'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        $infoMessages = [];

        if ($this->validateConfiguration($configuration)) {
            $this->configuration->set('PS_SECURITY_TOKEN', $configuration['token']);

            // Check if 2FA is being disabled
            $wasEnabled = (bool) $this->configuration->get('PS_BACKOFFICE_2FA');
            $willBeEnabled = (bool) $configuration['2fa'];

            if ($wasEnabled && !$willBeEnabled) {
                // 2FA is being disabled - clear all employee 2FA data
                if ($this->employeeRepository->hasAnyEmployeeWith2FAEnabled()) {
                    $this->employeeRepository->resetAllTwoFactorData();
                    $infoMessages[] = [
                        'type' => 'info',
                        'key' => '2fa_data_reset',
                    ];
                }
            }

            $this->configuration->set('PS_BACKOFFICE_2FA', $configuration['2fa']);
        }

        return $infoMessages;
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfiguration(array $configuration)
    {
        return isset($configuration['token']);
    }
}
