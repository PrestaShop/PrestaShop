<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Tax\Query\GetTaxForEditing;
use PrestaShop\PrestaShop\Core\Domain\Tax\QueryResult\EditableTax;

/**
 * Provides Data for tax add/edit form
 */
final class TaxFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var CommandBusInterface
     */
    private $queryBus;

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
    public function getData($taxId)
    {
        /** @var EditableTax $editableTax */
        $editableTax = $this->queryBus->handle(new GetTaxForEditing($taxId));

        return [
            'name' => $editableTax->getLocalizedNames(),
            'rate' => $editableTax->getRate(),
            'is_enabled' => $editableTax->isActive(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData()
    {
        return [
            'is_enabled' => false,
        ];
    }
}
