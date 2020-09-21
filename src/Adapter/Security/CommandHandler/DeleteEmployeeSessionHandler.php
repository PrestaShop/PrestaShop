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

namespace PrestaShop\PrestaShop\Adapter\Security\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Security\Command\DeleteSessionCommand;
use PrestaShop\PrestaShop\Core\Domain\Security\CommandHandler\DeleteSessionHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Security\Exception\CannotDeleteSuperAdminSessionException;
use PrestaShop\PrestaShop\Core\Domain\Security\Exception\FailedToDeleteSessionException;
use PrestaShop\PrestaShop\Core\Domain\Security\Exception\SessionException;
use PrestaShop\PrestaShop\Core\Domain\Security\Exception\SessionNotFoundException;
use PrestaShop\PrestaShop\Core\Employee\ContextEmployeeProviderInterface;
use PrestaShopException;
use CustomerSession;
use EmployeeSession;

/**
 * Class DeleteSessionHandler
 *
 * @internal
 */
final class DeleteSessionHandler implements DeleteSessionHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(DeleteSessionCommand $command)
    {
        $session = $command->getSessionId();

        try {
            $entity = new CustomerSession($session->getValue());

            if ($entity->id != $entityId) {
                throw new SessionNotFoundException(sprintf('Session with id %s cannot be found.', var_export($entityId, true)));
            }

            if ($this->contextEmployeeProvider->getSessionId() === $entity->id) {
                throw new FailedToDeleteSessionException(sprintf('Failed to delete Session with id %s', var_export($entityId, true)), FailedToDeleteSessionException::PROFILE_IS_ASSIGNED_TO_CONTEXT_EMPLOYEE);
            }

            if (false === $entity->delete()) {
                throw new FailedToDeleteSessionException(sprintf('Failed to delete Session with id %s', var_export($entityId, true)));
            }
        } catch (PrestaShopException $e) {
            throw new SessionException(sprintf('Unexpected error occurred when deleting Session with id %s', var_export($entityId, true)), 0, $e);
        }
    }
}
