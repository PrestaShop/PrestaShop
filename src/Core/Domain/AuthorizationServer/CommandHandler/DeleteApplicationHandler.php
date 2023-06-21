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

use PrestaShop\PrestaShop\Core\Domain\AuthorizationServer\Command\DeleteApplicationCommand;
use PrestaShop\PrestaShop\Core\Domain\AuthorizationServer\Exception\DeleteApplicationException;
use PrestaShop\PrestaShop\Core\Domain\AuthorizationServer\Model\ApiAccessRepositoryInterface;
use PrestaShop\PrestaShop\Core\Domain\AuthorizationServer\Model\AuthorizedApplicationRepositoryInterface;
use PrestaShop\PrestaShop\Core\Repository\TransactionManagerInterface;
use PrestaShopBundle\Entity\AuthorizedApplication;

/**
 * Handles command which delete application
 *
 * @experimental
 */
class DeleteApplicationHandler implements DeleteApplicationHandlerInterface
{
    /**
     * @var AuthorizedApplicationRepositoryInterface
     */
    private $applicationRepository;

    /**
     * @var ApiAccessRepositoryInterface
     */
    private $apiAccessRepository;

    /**
     * @var TransactionManagerInterface
     */
    private $transactionManager;

    /**
     * @param AuthorizedApplicationRepositoryInterface $applicationRepository
     * @param ApiAccessRepositoryInterface $apiAccessRepository
     * @param TransactionManagerInterface $transactionManager
     */
    public function __construct(
        AuthorizedApplicationRepositoryInterface $applicationRepository,
        ApiAccessRepositoryInterface $apiAccessRepository,
        TransactionManagerInterface $transactionManager)
    {
        $this->applicationRepository = $applicationRepository;
        $this->apiAccessRepository = $apiAccessRepository;
        $this->transactionManager = $transactionManager;
    }

    /**
     * {@inheritdoc}
     *
     * @throws DeleteApplicationException
     */
    public function handle(DeleteApplicationCommand $command): void
    {
        $this->transactionManager->beginTransaction();

        try {
            /** @var ?AuthorizedApplication $application */
            $application = $this->applicationRepository->getById($command->getApplicationId());
            $this->apiAccessRepository->deleteByApplication($application);
            $this->applicationRepository->delete($application);
        } catch (\Exception $exception) {
            $this->transactionManager->rollback();
            throw new DeleteApplicationException($exception->getMessage());
        }

        $this->transactionManager->commit();
    }
}
