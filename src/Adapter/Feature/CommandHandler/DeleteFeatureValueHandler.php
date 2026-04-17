<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Feature\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Feature\Repository\FeatureValueRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Feature\Command\DeleteFeatureValueCommand;
use PrestaShop\PrestaShop\Core\Domain\Feature\CommandHandler\DeleteFeatureValueHandlerInterface;

#[AsCommandHandler]
class DeleteFeatureValueHandler implements DeleteFeatureValueHandlerInterface
{
    public function __construct(
        protected readonly FeatureValueRepository $featureValueRepository
    ) {
    }

    public function handle(DeleteFeatureValueCommand $command): void
    {
        $this->featureValueRepository->delete($command->getFeatureValueId());
    }
}
