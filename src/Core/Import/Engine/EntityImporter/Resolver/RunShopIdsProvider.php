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
     * @var list<int>|null
     */
    protected ?array $runShopIds = null;

    public function __construct(
        protected readonly ShopRepository $shopRepository,
    ) {
    }

    /**
     * @return list<int>
     */
    public function getRunShopIds(ImportRunContext $context): array
    {
        return $this->runShopIds ??= $this->shopRepository->getAssociatedShopIds($context->getShopConstraint());
    }
}
