<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Tab\CommandHandler;

use Doctrine\ORM\EntityManager;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Tab\Command\UpdateTabStatusByClassNameCommand;
use PrestaShop\PrestaShop\Core\Domain\Tab\CommandHandler\UpdateTabStatusByClassNameHandlerInterface;
use PrestaShopBundle\Entity\Repository\TabRepository;
use PrestaShopBundle\Entity\Tab;

#[AsCommandHandler]
class UpdateTabStatusByClassNameHandler implements UpdateTabStatusByClassNameHandlerInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function handle(UpdateTabStatusByClassNameCommand $command): void
    {
        /** @var TabRepository $tabRepository */
        $tabRepository = $this->entityManager->getRepository(Tab::class);

        $tabRepository->changeStatusByClassName(
            $command->getClassName(),
            $command->isStatus()
        );
    }
}
