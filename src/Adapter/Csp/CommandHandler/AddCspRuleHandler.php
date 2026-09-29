<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Csp\CommandHandler;

use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use PrestaShop\PrestaShop\Adapter\Csp\CspRuleValidator;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Csp\Command\AddCspRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\Csp\CommandHandler\AddCspRuleHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Csp\Exception\CannotAddCspRuleException;
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspRuleId;
use PrestaShopBundle\Entity\CspRule;
use PrestaShopBundle\Entity\Repository\CspLogRepository;
use PrestaShopBundle\Entity\Repository\CspRuleRepository;

/**
 * @internal
 */
#[AsCommandHandler]
final class AddCspRuleHandler implements AddCspRuleHandlerInterface
{
    public function __construct(
        private readonly CspRuleRepository $repository,
        private readonly CspLogRepository $logRepository,
        private readonly CspRuleValidator $validator,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function handle(AddCspRuleCommand $command): CspRuleId
    {
        $shopId = $command->getShopConstraint()->getShopId();
        if (null === $shopId) {
            throw new CannotAddCspRuleException('A CSP rule can only be added for a single shop.');
        }

        // Coarsen here too, so a granular directive reaching this command directly (e.g. via the Admin API)
        // is stored as its parent.
        $directive = $command->getDirective()->coarsen()->value;
        $source = $command->getSource()->getValue();

        $this->validator->assertSourceIsNotAlreadyAllowed($shopId->getValue(), $directive, $source);

        // Wrap both writes in a transaction: a rule without its log placeholder is invisible in the log-driven grid.
        /** @var CspRuleId $ruleId */
        $ruleId = $this->entityManager->getConnection()->transactional(function () use ($shopId, $directive, $source): CspRuleId {
            $rule = (new CspRule())
                ->setShopId($shopId->getValue())
                ->setDirective($directive)
                ->setSource($source)
                ->setDateAdd(new DateTimeImmutable());

            $ruleId = new CspRuleId($this->repository->add($rule));

            // A manually added source has no log row; seed a placeholder so it is visible and revocable in the grid.
            $this->logRepository->insertPlaceholderIfAbsent($shopId->getValue(), $directive, $source);

            return $ruleId;
        });

        return $ruleId;
    }
}
