<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Csp;

use PrestaShop\PrestaShop\Core\Domain\Csp\Exception\CspConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspContext;
use PrestaShopBundle\Entity\Repository\CspRuleRepository;

/** Holds the cross-row uniqueness rule for CSP allow-list rules; single-value format validation lives in the value objects. */
final class CspRuleValidator
{
    public function __construct(
        private readonly CspRuleRepository $repository,
    ) {
    }

    /**
     * @throws CspConstraintException when the source is already allowed for the directive on the surface
     */
    public function assertSourceIsNotAlreadyAllowed(CspContext $context, int $shopId, string $directive, string $source): void
    {
        if (null !== $this->repository->findOneByShopDirectiveSource($context, $shopId, $directive, $source)) {
            throw new CspConstraintException(
                sprintf('The source "%s" is already allowed for directive "%s".', $source, $directive),
                CspConstraintException::DUPLICATE_RULE
            );
        }
    }
}
