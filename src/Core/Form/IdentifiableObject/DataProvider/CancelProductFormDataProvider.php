<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Currency\CurrencyDataProviderInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Query\GetOrderForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderForViewing;
use PrestaShop\PrestaShop\Core\Localization\CLDR\ComputingPrecision;

/**
 * Provides data for product cancellation form in order page
 */
final class CancelProductFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    /**
     * @var CurrencyDataProviderInterface
     */
    private $currencyDataProvider;

    /**
     * @param CommandBusInterface $queryBus
     * @param CurrencyDataProviderInterface $currencyDataProvider
     */
    public function __construct(
        CommandBusInterface $queryBus,
        CurrencyDataProviderInterface $currencyDataProvider
    ) {
        $this->queryBus = $queryBus;
        $this->currencyDataProvider = $currencyDataProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($orderId)
    {
        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->queryBus->handle(new GetOrderForViewing((int) $orderId));
        $computingPrecision = new ComputingPrecision();
        $currency = $this->currencyDataProvider->getCurrencyById($orderForViewing->getCurrencyId());

        return [
            'products' => $orderForViewing->getProducts()->getProducts(),
            'taxMethod' => $orderForViewing->getTaxMethod(),
            'precision' => $computingPrecision->getPrecision($currency->precision),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData()
    {
        return [];
    }
}
