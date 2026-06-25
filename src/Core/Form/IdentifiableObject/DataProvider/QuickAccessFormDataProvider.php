<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\QuickAccess\Query\GetQuickAccessForEditing;
use PrestaShop\PrestaShop\Core\Domain\QuickAccess\QueryResult\EditableQuickAccess;

final class QuickAccessFormDataProvider implements FormDataProviderInterface
{
    public function __construct(
        private readonly CommandBusInterface $queryBus
    ) {
    }

    public function getData($quickAccessId): array
    {
        /** @var EditableQuickAccess $quickAccess */
        $quickAccess = $this->queryBus->handle(new GetQuickAccessForEditing((int) $quickAccessId));

        return [
            'name' => $quickAccess->getLocalizedNames(),
            'link' => $quickAccess->getLink(),
            'new_window' => $quickAccess->isNewWindow(),
        ];
    }

    public function getDefaultData(): array
    {
        return [
            'new_window' => true,
        ];
    }
}
