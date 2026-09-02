<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Security\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Session\Repository\EmployeeSessionRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Security\Command\ClearOutdatedEmployeeSessionCommand;
use PrestaShop\PrestaShop\Core\Domain\Security\CommandHandler\ClearOutdatedEmployeeSessionHandlerInterface;

/**
 * Class ClearOutdatedEmployeeSessionHandler
 *
 * @internal
 */
#[AsCommandHandler]
class ClearOutdatedEmployeeSessionHandler implements ClearOutdatedEmployeeSessionHandlerInterface
{
    /**
     * @var EmployeeSessionRepository
     */
    private $repository;

    public function __construct(EmployeeSessionRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(ClearOutdatedEmployeeSessionCommand $command): void
    {
        $this->repository->clearOutdatedSessions();
    }
}
