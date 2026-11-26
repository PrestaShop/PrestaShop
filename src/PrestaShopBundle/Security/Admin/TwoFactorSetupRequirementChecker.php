<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Security\Admin;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShopBundle\Entity\Employee\Employee;

/**
 * Determines whether an employee must complete the two-factor
 * authentication setup based on the global configuration
 * and the employee's two-factor authentication settings.
 */
final class TwoFactorSetupRequirementChecker
{
    public function __construct(private readonly ConfigurationInterface $configuration)
    {
    }

    public function isRequired(Employee $employee): bool
    {
        if (!$this->configuration->get('PS_BACKOFFICE_2FA') || !$employee->isTwoFactorRequired()) {
            return false;
        }

        return !$employee->getTwoFactorEnabled()
            || (!$employee->isEmailAuthEnabled() && !$employee->isTotpAuthenticationEnabled());
    }
}
