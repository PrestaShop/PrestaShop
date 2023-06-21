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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\AuthorizationServer\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\AuthorizationServer\Command\EditApplicationCommand;
use PrestaShop\PrestaShop\Core\Domain\AuthorizationServer\Exception\ApplicationNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\AuthorizationServer\Exception\DuplicateApplicationNameException;
use PrestaShop\PrestaShop\Core\Domain\AuthorizationServer\Model\AuthorizedApplicationInterface;
use PrestaShop\PrestaShop\Core\Domain\AuthorizationServer\Model\AuthorizedApplicationRepositoryInterface;

/**
 * Handles command which edit application
 *
 * @experimental
 */
class EditApplicationHandler implements EditApplicationHandlerInterface
{
    /**
     * @var AuthorizedApplicationRepositoryInterface
     */
    private $applicationRepository;

    /**
     * @param AuthorizedApplicationRepositoryInterface $applicationRepository
     */
    public function __construct(AuthorizedApplicationRepositoryInterface $applicationRepository)
    {
        $this->applicationRepository = $applicationRepository;
    }

    /**
     * {@inheritdoc}
     *
     * @throws DuplicateApplicationNameException|ApplicationNotFoundException
     */
    public function handle(EditApplicationCommand $command): void
    {
        /** @var ?AuthorizedApplicationInterface $application */
        $application = $this->applicationRepository->getById($command->getApplicationId());

        $application->setName($command->getName());
        $application->setDescription($command->getDescription());

        $this->assertApplicationWithGivenNameDoesNotExist($application);

        $this->applicationRepository->update($application);
    }

    /**
     * @param AuthorizedApplicationInterface $currentApplication
     *
     * @return void
     *
     * @throws DuplicateApplicationNameException
     */
    private function assertApplicationWithGivenNameDoesNotExist(AuthorizedApplicationInterface $currentApplication): void
    {
        try {
            $name = $currentApplication->getName();
            $application = $this->applicationRepository->getByName($name);
            if ($application->getId() !== $currentApplication->getId()) {
                throw new DuplicateApplicationNameException($name, sprintf('Application with name "%s" already exists', $name));
            }
        } catch (ApplicationNotFoundException $exception) {
            //Any application with same name is found so it's ok
        }
    }
}
