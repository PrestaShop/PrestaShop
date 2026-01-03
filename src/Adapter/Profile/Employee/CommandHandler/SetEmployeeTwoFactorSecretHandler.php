<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Profile\Employee\CommandHandler;

use Doctrine\ORM\EntityManagerInterface;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\SetEmployeeTwoFactorSecretCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\CommandHandler\SetEmployeeTwoFactorSecretHandlerInterface;
use PrestaShopBundle\Entity\Employee\Employee as EntityEmployee;
use PrestaShopBundle\Entity\Repository\EmployeeRepository;

/**
 * Handles the command that stores the two-factor authentication secret
 *
 * @internal
 */
#[AsCommandHandler]
final class SetEmployeeTwoFactorSecretHandler implements SetEmployeeTwoFactorSecretHandlerInterface
{
    public function __construct(
        private readonly EmployeeRepository $employeeRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function handle(SetEmployeeTwoFactorSecretCommand $command)
    {
        /** @var EntityEmployee $employee */
        $employee = $this->employeeRepository->findOneBy([
            'id' => $command->getEmployeeId()->getValue(),
        ]);

        $employee
            ->setTwoFactorSecret($command->getSecret())
            ->setTwoFactorTotpSecretPlain($command->getSecretPlain());

        $this->entityManager->persist($employee);
        $this->entityManager->flush();
    }
}
