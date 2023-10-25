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

namespace PrestaShop\PrestaShop\Adapter\ApiAccess\CommandHandler;

use Doctrine\ORM\Exception\ORMException;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\ApiAccess\Command\AddApiAccessCommand;
use PrestaShop\PrestaShop\Core\Domain\ApiAccess\CommandHandler\AddApiAccessCommandHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\ApiAccess\Exception\ApiAccessConstraintException;
use PrestaShop\PrestaShop\Core\Domain\ApiAccess\Exception\CannotAddApiAccessException;
use PrestaShop\PrestaShop\Core\Domain\ApiAccess\ValueObject\CreatedApiAccess;
use PrestaShop\PrestaShop\Core\Util\String\RandomString;
use PrestaShopBundle\Entity\ApiAccess;
use PrestaShopBundle\Entity\Repository\ApiAccessRepository;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommandHandler]
class AddApiAccessHandler implements AddApiAccessCommandHandlerInterface
{
    public function __construct(
        private readonly ApiAccessRepository $repository,
        private readonly ValidatorInterface $validator,
        private readonly PasswordHasherInterface $passwordHasher
    ) {
    }

    public function handle(AddApiAccessCommand $command): CreatedApiAccess
    {
        $apiAccess = new ApiAccess();
        $apiAccess->setClientId($command->getApiClientId());
        $apiAccess->setClientName($command->getClientName());
        $secret = RandomString::generate();
        $apiAccess->setClientSecret($this->passwordHasher->hash($secret));
        $apiAccess->setEnabled($command->isEnabled());
        $apiAccess->setDescription($command->getDescription());
        $apiAccess->setScopes($command->getScopes());

        $errors = $this->validator->validate($apiAccess);

        if (count($errors) > 0) {
            throw ApiAccessConstraintException::buildFromPropertyPath(
                $errors->get(0)->getPropertyPath(),
                $errors->get(0)->getMessage(),
                $errors->get(0)->getMessageTemplate()
            );
        }

        try {
            $apiAccessId = $this->repository->save($apiAccess);
        } catch (ORMException $e) {
            throw new CannotAddApiAccessException('Could not add Api access', 0, $e);
        }

        return new CreatedApiAccess($apiAccessId, $secret);
    }
}
