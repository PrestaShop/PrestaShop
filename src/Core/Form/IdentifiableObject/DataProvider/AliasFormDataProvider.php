<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Alias\Query\GetAliasesBySearchTermForEditing;
use PrestaShop\PrestaShop\Core\Domain\Alias\QueryResult\AliasForEditing;

class AliasFormDataProvider implements FormDataProviderInterface
{
    public function __construct(
        protected readonly CommandBusInterface $queryBus
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getData($searchTerm): array
    {
        /**
         * @var AliasForEditing $aliases
         */
        $aliases = $this->queryBus->handle(new GetAliasesBySearchTermForEditing((string) $searchTerm));

        return [
            'search' => $aliases->getSearchTerm(),
            'aliases' => $aliases->getAliases(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData(): array
    {
        return [];
    }
}
