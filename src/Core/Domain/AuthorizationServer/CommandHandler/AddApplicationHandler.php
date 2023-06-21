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

use PrestaShop\PrestaShop\Core\Domain\AuthorizationServer\Command\AddApplicationCommand;
use PrestaShop\PrestaShop\Core\Domain\AuthorizationServer\Exception\ApplicationConstraintException;
use PrestaShop\PrestaShop\Core\Domain\AuthorizationServer\Exception\ApplicationNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\AuthorizationServer\Exception\DuplicateApplicationNameException;
use PrestaShop\PrestaShop\Core\Domain\AuthorizationServer\Model\AuthorizedApplicationFactoryInterface;
use PrestaShop\PrestaShop\Core\Domain\AuthorizationServer\Model\AuthorizedApplicationRepositoryInterface;
use PrestaShop\PrestaShop\Core\Domain\AuthorizationServer\ValueObject\ApplicationId;

/**
 * Handles command which adds new application
 *
 * @experimental
 */
class AddApplicationHandler implements AddApplicationHandlerInterface
{
    /**
     * @var AuthorizedApplicationRepositoryInterface
     */
    private $applicationRepository;

    /**
     * @var AuthorizedApplicationFactoryInterface
     */
    private $authorizedApplicationFactory;

    public function __construct(
        AuthorizedApplicationRepositoryInterface $applicationRepository,
        AuthorizedApplicationFactoryInterface $authorizedApplicationFactory
    ) {
        $this->applicationRepository = $applicationRepository;
        $this->authorizedApplicationFactory = $authorizedApplicationFactory;
    }

    /**
     * {@inheritdoc}
     *
     * @throws ApplicationConstraintException|DuplicateApplicationNameException
     */
    public function handle(AddApplicationCommand $command): ApplicationId
    {
        $application = $this->authorizedApplicationFactory->create();
        $application->setName($command->getName());
        $application->setDescription($command->getDescription());

        $applicationName = $application->getName();

        if ($this->doesApplicationExists($applicationName)) {
            throw new DuplicateApplicationNameException($applicationName, sprintf('Application with name "%s" already exists', $applicationName));
        }

        $this->applicationRepository->create($application);

        return new ApplicationId($application->getId());
    }

    /**
     * Check if application with given name already exist
     *
     * @param $name string
     *
     * @return bool
     */
    private function doesApplicationExists(string $name): bool
    {
        try {
            $this->applicationRepository->getByName($name);

            return true;
        } catch (ApplicationNotFoundException $exception) {
            return false;
        }
    }
}
