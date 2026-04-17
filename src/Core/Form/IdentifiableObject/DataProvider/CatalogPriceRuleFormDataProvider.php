<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Query\GetCatalogPriceRuleForEditing;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\QueryResult\EditableCatalogPriceRule;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction;

/**
 * Provides data for catalog price rule add/edit forms
 */
final class CatalogPriceRuleFormDataProvider implements FormDataProviderInterface
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
    public function getData($catalogPriceRuleId)
    {
        /** @var EditableCatalogPriceRule $editableCatalogPriceRule */
        $editableCatalogPriceRule = $this->queryBus->handle(new GetCatalogPriceRuleForEditing((int) $catalogPriceRuleId));

        $dateTimeFormat = 'Y-m-d H:i:s';
        $price = $editableCatalogPriceRule->getPrice();
        $leaveInitialPrice = false;
        $from = $editableCatalogPriceRule->getFrom();
        $to = $editableCatalogPriceRule->getTo();

        if ($price->isLowerOrEqualThanZero()) {
            $price = null;
            $leaveInitialPrice = true;
        }

        $data = [
            'name' => $editableCatalogPriceRule->getName(),
            'id_shop' => $editableCatalogPriceRule->getShopId(),
            'id_currency' => $editableCatalogPriceRule->getCurrencyId(),
            'id_country' => $editableCatalogPriceRule->getCountryId(),
            'id_group' => $editableCatalogPriceRule->getGroupId(),
            'from_quantity' => $editableCatalogPriceRule->getFromQuantity(),
            'price' => null === $price ? $price : (string) $price,
            'leave_initial_price' => $leaveInitialPrice,
            'date_range' => [
                'from' => $from ? $from->format($dateTimeFormat) : '',
                'to' => $to ? $to->format($dateTimeFormat) : '',
            ],
            'reduction' => [
                'type' => $editableCatalogPriceRule->getReduction()->getType(),
                'value' => (string) $editableCatalogPriceRule->getReduction()->getValue(),
                'include_tax' => $editableCatalogPriceRule->isTaxIncluded(),
            ],
        ];

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData()
    {
        return [
            'from_quantity' => 1,
            'leave_initial_price' => true,
            'reduction' => [
                'type' => Reduction::TYPE_AMOUNT,
                'value' => 0,
                'include_tax' => true,
            ],
        ];
    }
}
