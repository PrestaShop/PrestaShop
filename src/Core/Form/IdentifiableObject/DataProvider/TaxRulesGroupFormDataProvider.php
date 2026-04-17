<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Query\GetTaxRulesGroupForEditing;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\QueryResult\EditableTaxRulesGroup;

/**
 * Provides data for search engine add/edit form.
 */
class TaxRulesGroupFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var CommandBusInterface
     */
    protected $queryBus;

    /**
     * @param CommandBusInterface $queryBus
     */
    public function __construct(CommandBusInterface $queryBus)
    {
        $this->queryBus = $queryBus;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($id): array
    {
        /** @var EditableTaxRulesGroup $editableTaxRulesGroup */
        $editableTaxRulesGroup = $this->queryBus->handle(new GetTaxRulesGroupForEditing($id));

        return [
            'name' => $editableTaxRulesGroup->getName(),
            'is_enabled' => $editableTaxRulesGroup->isActive(),
            'shop_association' => $editableTaxRulesGroup->getShopAssociationIds(),
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
