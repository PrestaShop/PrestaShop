<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Security\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Session\Repository\CustomerSessionRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Security\Command\ClearOutdatedCustomerSessionCommand;
use PrestaShop\PrestaShop\Core\Domain\Security\CommandHandler\ClearOutdatedCustomerSessionHandlerInterface;

/**
 * Class ClearOutdatedCustomerSessionHandler
 *
 * @internal
 */
#[AsCommandHandler]
class ClearOutdatedCustomerSessionHandler implements ClearOutdatedCustomerSessionHandlerInterface
{
    /**
     * @var CustomerSessionRepository
     */
    private $repository;

    public function __construct(CustomerSessionRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(ClearOutdatedCustomerSessionCommand $command): void
    {
        $this->repository->clearOutdatedSessions();
    }
}
