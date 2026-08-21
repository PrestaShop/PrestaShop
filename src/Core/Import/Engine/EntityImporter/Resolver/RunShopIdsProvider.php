<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Resolver;

use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopRepository;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportRunContext;

/**
 * The concrete shops of the run's frozen scope — what auto-created entities
 * get associated with. Memoized for the service lifetime (one batch request):
 * the run's ShopConstraint cannot change, and the resolvers ask on every
 * auto-creation.
 */
class RunShopIdsProvider
{
    /**
     * @var array<string, list<int>> shop ids, keyed by the constraint they were resolved for
     */
    protected array $runShopIds = [];

    public function __construct(
        protected readonly ShopRepository $shopRepository,
    ) {
    }

    /**
     * @return list<int>
     */
    public function getRunShopIds(ImportRunContext $context): array
    {
        $shopConstraint = $context->getShopConstraint();

        // keyed by constraint rather than memoized once: a single instance only
        // ever serves one run today (one per batch request, and the constraint is
        // frozen), but the signature promises to answer for the context it is
        // handed, and handing run B the shop ids of run A would silently
        // associate auto-created brands and features with the wrong shops.
        // ShopConstraint has no __toString, hence the explicit key
        $cacheKey = sprintf(
            '%d:%d:%d',
            $shopConstraint->getShopId()?->getValue() ?? 0,
            $shopConstraint->getShopGroupId()?->getValue() ?? 0,
            (int) $shopConstraint->isStrict()
        );

        return $this->runShopIds[$cacheKey] ??= $this->shopRepository->getAssociatedShopIds($shopConstraint);
    }
}
