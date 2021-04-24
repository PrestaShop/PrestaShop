<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
