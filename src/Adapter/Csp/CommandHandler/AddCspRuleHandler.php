<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Csp\CommandHandler;

use DateTimeImmutable;
use PrestaShop\PrestaShop\Adapter\Csp\CspPolicyCacheInterface;
use PrestaShop\PrestaShop\Adapter\Csp\CspRuleValidator;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Csp\Command\AddCspRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\Csp\CommandHandler\AddCspRuleHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Csp\Exception\CannotAddCspRuleException;
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspContext;
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspRuleId;
use PrestaShopBundle\Entity\CspRule;
use PrestaShopBundle\Entity\Repository\CspRuleRepository;

/**
 * @internal
 */
#[AsCommandHandler]
final class AddCspRuleHandler implements AddCspRuleHandlerInterface
{
    public function __construct(
        private readonly CspRuleRepository $repository,
        private readonly CspRuleValidator $validator,
        private readonly CspPolicyCacheInterface $policyCache,
    ) {
    }

    public function handle(AddCspRuleCommand $command): CspRuleId
    {
        $context = $command->getContext();

        // The back office is one global surface stored under shop id 0; a storefront rule needs a single
        // shop (the toolbar hides "Add" in an all-shops/group scope).
        if (CspContext::ADMIN === $context) {
            $shopId = 0;
        } else {
            $shopIdValue = $command->getShopConstraint()->getShopId();
            if (null === $shopIdValue) {
                throw new CannotAddCspRuleException('A CSP rule can only be added for a single shop.');
            }
            $shopId = $shopIdValue->getValue();
        }

        // Coarsen here too, so a granular directive reaching this command directly (e.g. via the Admin API)
        // is stored as its parent.
        $directive = $command->getDirective()->coarsen()->value;
        $source = $command->getSource()->getValue();

        $this->validator->assertSourceIsNotAlreadyAllowed($context, $shopId, $directive, $source);

        $rule = (new CspRule())
            ->setShopId($shopId)
            ->setContext($context->value)
            ->setDirective($directive)
            ->setSource($source)
            ->setDateAdd(new DateTimeImmutable());

        $ruleId = new CspRuleId($this->repository->add($rule));

        // A storefront rule changes the cached policy for that shop; the back office is not cached.
        if (CspContext::FRONT === $context) {
            $this->policyCache->invalidate($shopId);
        }

        return $ruleId;
    }
}
