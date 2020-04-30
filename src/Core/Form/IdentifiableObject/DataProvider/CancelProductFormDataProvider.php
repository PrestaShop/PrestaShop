<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
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
