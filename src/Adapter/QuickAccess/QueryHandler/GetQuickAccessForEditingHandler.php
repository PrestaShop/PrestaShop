<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\QuickAccess\QueryHandler;

use PrestaShop\PrestaShop\Adapter\QuickAccess\Repository\QuickAccessRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\QuickAccess\Query\GetQuickAccessForEditing;
use PrestaShop\PrestaShop\Core\Domain\QuickAccess\QueryHandler\GetQuickAccessForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\QuickAccess\QueryResult\EditableQuickAccess;

#[AsQueryHandler]
class GetQuickAccessForEditingHandler implements GetQuickAccessForEditingHandlerInterface
{
    public function __construct(private readonly QuickAccessRepository $repository)
    {
    }

    public function handle(GetQuickAccessForEditing $query): EditableQuickAccess
    {
        $quickAccessId = $query->getQuickAccessId();
        $quickAccess = $this->repository->get($quickAccessId);

        return new EditableQuickAccess(
            $quickAccessId->getValue(),
            $quickAccess->name,
            (string) $quickAccess->link,
            (bool) $quickAccess->new_window,
        );
    }
}
