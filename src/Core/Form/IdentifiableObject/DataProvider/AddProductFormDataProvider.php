<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Adapter\Currency\CurrencyDataProvider;
use PrestaShop\PrestaShop\Adapter\Order\Repository\OrderRepository;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Query\GetOrderForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\QuerySorting;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;

class AddProductFormDataProvider implements FormDataProviderInterface
{
    public function __construct(
        private CommandBusInterface $queryBus,
        private CurrencyDataProvider $currencyDataProvider,
        private FeatureFlagStateCheckerInterface $featureFlagStateChecker,
        private ?OrderRepository $orderRepository = null,
    ) {
    }

    public function getData($orderId)
    {
        $currency = $this->currencyDataProvider->getCurrencyById($this->getOrderCurrencyId((int) $orderId));
        $multishipmentIsEnabled = $this->featureFlagStateChecker->isEnabled(FeatureFlagSettings::FEATURE_FLAG_IMPROVED_SHIPMENT);

        return [
            'order_id' => $orderId,
            'currency' => $currency,
            'is_multishipment_is_enabled' => $multishipmentIsEnabled,
        ];
    }

    public function getDefaultData()
    {
        return [];
    }

    /**
     * Only the currency is needed: building the whole order view for it ran its queries once more per page.
     */
    private function getOrderCurrencyId(int $orderId): int
    {
        if (null !== $this->orderRepository) {
            return (int) $this->orderRepository->get(new OrderId($orderId))->id_currency;
        }

        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->queryBus->handle(new GetOrderForViewing($orderId, QuerySorting::DESC));

        return $orderForViewing->getCurrencyId();
    }
}
