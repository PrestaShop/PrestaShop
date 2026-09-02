<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\SpecificPrice\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\SpecificPrice\Repository\SpecificPriceRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Command\DeleteSpecificPriceCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\CommandHandler\DeleteSpecificPriceHandlerInterface;

/**
 * Handles DeleteSpecificPriceCommand using legacy object model
 */
#[AsCommandHandler]
class DeleteSpecificPriceHandler implements DeleteSpecificPriceHandlerInterface
{
    /**
     * @var SpecificPriceRepository
     */
    private $specificPriceRepository;

    /**
     * @param SpecificPriceRepository $specificPriceRepository
     */
    public function __construct(SpecificPriceRepository $specificPriceRepository)
    {
        $this->specificPriceRepository = $specificPriceRepository;
    }

    public function handle(DeleteSpecificPriceCommand $command): void
    {
        $this->specificPriceRepository->delete($command->getSpecificPriceId());
    }
}
