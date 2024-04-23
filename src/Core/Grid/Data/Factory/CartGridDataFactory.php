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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Data\Factory;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Context\CurrencyContext;
use PrestaShop\PrestaShop\Core\Domain\Cart\CartStatus;
use PrestaShop\PrestaShop\Core\Domain\Cart\Query\GetCartForViewing;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Localization\LocaleInterface;
use PrestaShopBundle\Translation\TranslatorInterface;

/**
 * Gets data for cart grid
 */
class CartGridDataFactory implements GridDataFactoryInterface
{
    public function __construct(
        protected readonly GridDataFactoryInterface $cartDataFactory,
        protected readonly TranslatorInterface $translator,
        protected readonly LocaleInterface $locale,
        protected readonly CommandBusInterface $queryBus,
        protected readonly CurrencyContext $currencyContext,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria)
    {
        $cartData = $this->cartDataFactory->getData($searchCriteria);
        $modifiedRecords = $this->applyModifications($cartData->getRecords());

        return new GridData(
            $modifiedRecords,
            $cartData->getRecordsTotal(),
            $cartData->getQuery()
        );
    }

    /**
     * Modify all records in grid data.
     *
     * @param RecordCollectionInterface $records
     *
     * @return RecordCollectionInterface
     */
    private function applyModifications(RecordCollectionInterface $records): RecordCollectionInterface
    {
        $modifiedRecords = [];
        foreach ($records as $record) {
            $modifiedRecords[] = $this->setRecordData($record);
        }

        return new RecordCollection($modifiedRecords);
    }

    /**
     * Modify cart record for grid data.
     *
     * @param array<string, mixed> $record
     *
     * @return array<string, mixed>
     */
    private function setRecordData(array $record): array
    {
        $cartForViewing = $this->queryBus->handle(new GetCartForViewing((int) $record['id_cart']));
        $record['cart_total'] = $this->locale->formatPrice(
            $cartForViewing->getCartSummary()['total_products'],
            $this->currencyContext->getIsoCode()
        );

        $record['unremovable'] = $record['status'] === CartStatus::ORDERED;
        $record['status_badge_color'] = $record['status'] === CartStatus::ORDERED ? 'success' : 'danger';
        $record['status'] = $this->getOrderLabel($record);

        $record['customer_online_id'] = $record['customer_online'];
        $record['customer_online'] = $record['customer_online_id'] > 0 ?
            $this->translator->trans('Yes', [], 'Shop.Theme.Global') :
            $this->translator->trans('No', [], 'Shop.Theme.Global');

        return $record;
    }

    /**
     * Compute id_order column label.
     *
     * @param array $record
     *
     * @return string
     */
    private function getOrderLabel(array $record): string
    {
        switch ($record['status']) {
            case CartStatus::ORDERED:
                return $this->translator->trans('Ordered', [], 'Admin.Orderscustomers.Feature');
            case CartStatus::NOT_ORDERED:
                return $this->translator->trans('Non ordered', [], 'Admin.Orderscustomers.Feature');
            case CartStatus::ABANDONED_CART:
                return $this->translator->trans('Abandoned cart', [], 'Admin.Orderscustomers.Feature');
        }

        return '';
    }
}
