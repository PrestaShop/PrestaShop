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

namespace PrestaShop\PrestaShop\Adapter\ApiAccess\QueryHandler;

use Doctrine\ORM\NoResultException;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\ApiAccess\Exception\ApiAccessNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\ApiAccess\Query\GetApiAccessForEditing;
use PrestaShop\PrestaShop\Core\Domain\ApiAccess\QueryHandler\GetApiAccessForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\ApiAccess\QueryResult\EditableApiAccess;
use PrestaShopBundle\Entity\Repository\ApiAccessRepository;

#[AsQueryHandler]
class GetApiAccessForEditingHandler implements GetApiAccessForEditingHandlerInterface
{
    public function __construct(private readonly ApiAccessRepository $repository)
    {
    }

    public function handle(GetApiAccessForEditing $query): EditableApiAccess
    {
        try {
            $apiAccess = $this->repository->getById($query->getApiAccessId()->getValue());
        } catch (NoResultException $e) {
            throw new ApiAccessNotFoundException(sprintf('Could not find Api access %s', $query->getApiAccessId()->getValue()), 0, $e);
        }

        return new EditableApiAccess(
            $apiAccess->getId(),
            $apiAccess->getClientId(),
            $apiAccess->getClientName(),
            $apiAccess->isEnabled(),
            $apiAccess->getDescription(),
            $apiAccess->getScopes()
        );
    }
}
