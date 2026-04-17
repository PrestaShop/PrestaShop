<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Currency\Query\GetCurrencyForEditing;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\ExchangeRate;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\Precision;

/**
 * Class CurrencyFormDataProvider
 */
final class CurrencyFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var array
     */
    private $contextShopIds;

    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    /**
     * @param CommandBusInterface $queryBus
     * @param array $contextShopIds
     */
    public function __construct(CommandBusInterface $queryBus, array $contextShopIds)
    {
        $this->contextShopIds = $contextShopIds;
        $this->queryBus = $queryBus;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($id)
    {
        /** @var \PrestaShop\PrestaShop\Core\Domain\Currency\QueryResult\EditableCurrency $result */
        $result = $this->queryBus->handle(new GetCurrencyForEditing((int) $id));

        return [
            'id' => $id,
            'iso_code' => $result->getIsoCode(),
            'names' => $result->getNames(),
            'symbols' => $result->getSymbols(),
            'transformations' => $result->getTransformations(),
            'exchange_rate' => $result->getExchangeRate(),
            'precision' => $result->getPrecision(),
            'shop_association' => $result->getAssociatedShopIds(),
            'active' => $result->isEnabled(),
            'unofficial' => $result->isUnofficial(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData()
    {
        return [
            'precision' => Precision::DEFAULT_PRECISION,
            'exchange_rate' => ExchangeRate::DEFAULT_RATE,
            'shop_association' => $this->contextShopIds,
            'active' => true,
        ];
    }
}
