<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Security\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Session\Repository\EmployeeSessionRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Security\Command\DeleteEmployeeSessionCommand;
use PrestaShop\PrestaShop\Core\Domain\Security\CommandHandler\DeleteEmployeeSessionHandlerInterface;

/**
 * Class DeleteEmployeeSessionHandler
 *
 * @internal
 */
#[AsCommandHandler]
final class DeleteEmployeeSessionHandler implements DeleteEmployeeSessionHandlerInterface
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
    public function handle(DeleteEmployeeSessionCommand $command): void
    {
        $this->repository->delete($command->getEmployeeSessionId());
    }
}
