<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Grid\Data\Factory;

use Cart;
use Context;
use Currency;
use Customer;
use PrestaShop\PrestaShop\Core\Grid\Data\Factory\GridDataFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Decorates Doctrine data factory for carts
 */
final class CartGridDataFactory implements GridDataFactoryInterface
{
    /**
     * @var GridDataFactoryInterface
     */
    private $doctrineDataFactory;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param GridDataFactoryInterface $doctrineDataFactory
     * @param TranslatorInterface $translator
     */
    public function __construct(
        GridDataFactoryInterface $doctrineDataFactory,
        TranslatorInterface $translator
    ) {
        $this->doctrineDataFactory = $doctrineDataFactory;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria)
    {
        $data = $this->doctrineDataFactory->getData($searchCriteria);

        $records = $this->applyModifications($data->getRecords());

        return new GridData($records, $data->getRecordsTotal(), $data->getQuery());
    }

    /**
     * @param RecordCollectionInterface $records
     *
     * @return RecordCollection
     */
    private function applyModifications(RecordCollectionInterface $records)
    {
        $modifiedRecords = [];

        foreach ($records as $record) {
            switch ($record['status']) {
                case 'abandoned_cart':
                    $record['status'] = $this->translator->trans(
                        'Abandoned cart',
                        [],
                        'Admin.Orderscustomers.Feature'
                    );
                    break;
                case 'not_ordered':
                    $record['status'] = $this->translator->trans('Not ordered', [], 'Admin.Orderscustomers.Feature');
                    break;
            }

            if (empty($record['carrier_name'])) {
                $record['carrier_name'] = '--';
            }

            if (empty($record['customer_name'])) {
                $record['customer_name'] = '--';
            }

            $record['online'] = $record['id_guest'] ?
                $this->translator->trans('Yes', [], 'Admin.Global') :
                $this->translator->trans('No', [], 'Admin.Global');

            $context = Context::getContext();
            $context->cart = new Cart($record['id_cart']);
            $context->currency = new Currency((int) $context->cart->id_currency);
            $context->customer = new Customer((int) $context->cart->id_customer);

            $record['cart_total'] = Cart::getTotalCart($context->cart->id, true, Cart::BOTH_WITHOUT_SHIPPING);
            $record['is_order_placed'] = $context->cart->orderExists();

            $modifiedRecords[] = $record;
        }

        return new RecordCollection($modifiedRecords);
    }

}
